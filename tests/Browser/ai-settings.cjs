// UI regression against Blade rendered by AiModelSettingsTest with an in-memory DB.
// Generate fixture: $env:AI_REVIEW='1'; php artisan test --filter=test_authorized_roles_see_save_and_reload_model
// Run after npm run build: node tests/Browser/ai-settings.cjs
// All provider responses are local mocks. This never starts Laravel or calls Gemini.
const fs = require('node:fs');
const path = require('node:path');
const http = require('node:http');
const os = require('node:os');
const {spawn} = require('node:child_process');
const assert = require('node:assert/strict');
const root = path.resolve(__dirname, '../..');
const review = path.join(root, 'storage/framework/testing/ai-review');
const calls = [];
const server = http.createServer((req, res) => {
    const url = new URL(req.url, 'http://localhost');
    if (url.pathname === '/admin/settings/test-model') {
        let body = '';
        req.on('data', data => { body += data; });
        req.on('end', () => {
            const data = JSON.parse(body);
            calls.push(data);
            setTimeout(() => {
                res.setHeader('Content-Type', 'application/json');
                res.end(JSON.stringify({success:true, provider:data.provider, model:data.gemini_model, reply:'<img src=x onerror="window.previewXss=true"> Koneksi uji berhasil.'}));
            }, 350);
        });
        return;
    }
    const file = url.pathname === '/admin/setelan'
        ? path.join(review, 'setelan.html') : path.resolve(root, 'public', '.' + url.pathname);
    if (!(file === path.join(review, 'setelan.html') || file.startsWith(path.join(root, 'public') + path.sep)) || !fs.existsSync(file)) {
        res.writeHead(404).end(); return;
    }
    const ext = path.extname(file);
    res.setHeader('Content-Type', ({'.html':'text/html; charset=utf-8','.css':'text/css','.js':'text/javascript','.png':'image/png'})[ext] || 'application/octet-stream');
    const bytes = fs.readFileSync(file);
    res.end(ext === '.html' ? bytes.toString().replaceAll('http://localhost', `http://127.0.0.1:${server.address().port}`) : bytes);
});
let chrome;
let ws;
(async () => {
    assert.ok(fs.existsSync(path.join(review, 'setelan.html')), 'Generate the test HTML fixture first');
    await new Promise(resolve => server.listen(0, '127.0.0.1', resolve));
    const profile = fs.mkdtempSync(path.join(os.tmpdir(), 'rpjmd-ai-review-'));
    chrome = spawn(process.env.CHROME_PATH || 'C:/Program Files/Google/Chrome/Application/chrome.exe',
        ['--headless=new','--remote-debugging-port=0',`--user-data-dir=${profile}`,'--no-first-run','about:blank'], {windowsHide:true, stdio:'ignore'});
    chrome.on('error', error => { console.error(error.message); server.close(); process.exitCode=1; });
    const portFile = path.join(profile, 'DevToolsActivePort');
    for (let i=0; !fs.existsSync(portFile) && i<100; i++) await new Promise(r=>setTimeout(r,100));
    const port = fs.readFileSync(portFile,'utf8').split('\n')[0];
    const targets = await fetch(`http://127.0.0.1:${port}/json/list`).then(r=>r.json());
    ws = new WebSocket(targets.find(t=>t.type==='page').webSocketDebuggerUrl);
    await new Promise((resolve,reject)=>{
        const timeout=setTimeout(()=>reject(new Error('Chrome connection timed out')),10000);
        ws.addEventListener('open',()=>{clearTimeout(timeout);resolve();},{once:true});
        ws.addEventListener('error',()=>{clearTimeout(timeout);reject(new Error('Chrome connection failed'));},{once:true});
    });
    let seq=0; const waiting=new Map(); const errors=[];
    ws.addEventListener('message', event=>{
        const data=JSON.parse(event.data);
        if(data.id){const item=waiting.get(data.id);waiting.delete(data.id);data.error?item.reject(data.error):item.resolve(data.result);}
        if(data.method==='Runtime.exceptionThrown') errors.push(data.params.exceptionDetails.exception?.description || data.params.exceptionDetails.text);
    });
    const call=(method,params={})=>new Promise((resolve,reject)=>{
        const id=++seq;
        const timeout=setTimeout(()=>{waiting.delete(id);reject(new Error(`Chrome command timed out: ${method}`));},10000);
        waiting.set(id,{resolve:value=>{clearTimeout(timeout);resolve(value);},reject:error=>{clearTimeout(timeout);reject(error);}});
        ws.send(JSON.stringify({id,method,params}));
    });
    const evaluate=async expression=>{
        const response=await call('Runtime.evaluate',{expression,returnByValue:true,awaitPromise:true});
        if(response.exceptionDetails) throw new Error(response.exceptionDetails.text);
        return response.result.value;
    };
    await call('Page.enable'); await call('Runtime.enable'); await call('Network.enable');
    await call('Network.setBlockedURLs',{urls:['https://*']});
    await call('Page.navigate',{url:`http://127.0.0.1:${server.address().port}/admin/setelan`});
    for(let i=0;i<100;i++){
        if(await evaluate("document.readyState === 'complete' && !!document.getElementById('test-ai-model')")) break;
        await new Promise(r=>setTimeout(r,100));
    }
    const active=await evaluate("document.getElementById('active-model-display').textContent");
    assert.match(active,/gemini-2.5-flash/);
    assert.equal(await evaluate('window.adminHasUnsavedChanges()'), false);
    await evaluate("document.getElementById('gemini_model').value='gemini-2.5-pro'; document.getElementById('gemini_model').dispatchEvent(new Event('change',{bubbles:true})); document.getElementById('test-ai-model').click()");
    assert.equal(await evaluate("document.getElementById('test-ai-model').disabled"),true);
    await new Promise(r=>setTimeout(r,100));
    await evaluate("document.getElementById('gemini_model').value='gemini-2.5-flash'; document.getElementById('gemini_model').dispatchEvent(new Event('change',{bubbles:true}))");
    await new Promise(r=>setTimeout(r,500));
    assert.equal(await evaluate("document.getElementById('ai-test-result').textContent"),'','Stale result discarded');
    await evaluate("document.getElementById('gemini_model').value='gemini-2.5-pro'; document.getElementById('gemini_model').dispatchEvent(new Event('change',{bubbles:true})); document.getElementById('test-ai-model').click()");
    await new Promise(r=>setTimeout(r,700));
    assert.match(await evaluate("document.getElementById('ai-test-result').textContent"),/Pengujian berhasil/);
    assert.equal(await evaluate("document.querySelectorAll('#ai-test-result img').length"),0);
    assert.equal(await evaluate('!!window.previewXss'),false);
    assert.equal(await evaluate("document.getElementById('active-model-display').textContent"),active);
    assert.equal(await evaluate('window.adminHasUnsavedChanges()'), true, 'Preview must not mark the candidate as saved');
    const payload=await evaluate("Object.fromEntries(new FormData(document.getElementById('ai-settings-form')))");
    assert.equal(payload.gemini_model,'gemini-2.5-pro'); assert.equal(payload.provider,'gemini');
    assert.ok(!Object.hasOwn(payload,'api_key'));
    await evaluate("document.getElementById('gemini_model').focus()");
    await call('Input.dispatchKeyEvent',{type:'keyDown',key:'Tab',code:'Tab',windowsVirtualKeyCode:9});
    await call('Input.dispatchKeyEvent',{type:'keyUp',key:'Tab',code:'Tab',windowsVirtualKeyCode:9});
    assert.equal(await evaluate('document.activeElement.id'),'test-ai-model');
    const widths=[320,768,1440];
    for(const width of widths){
        await call('Emulation.setDeviceMetricsOverride',{width,height:1000,deviceScaleFactor:1,mobile:false});
        const screenshot=await call('Page.captureScreenshot',{format:'png',captureBeyondViewport:false});
        fs.writeFileSync(path.join(review,`setelan-${width}.png`),Buffer.from(screenshot.data,'base64'));
        assert.ok(await evaluate('document.documentElement.scrollWidth <= document.documentElement.clientWidth'),`Overflow at ${width}`);
    }
    // 200% desktop browser zoom gives a 640 CSS-pixel viewport on a 1280px window.
    await call('Emulation.setDeviceMetricsOverride',{width:640,height:500,deviceScaleFactor:2,mobile:false});
    assert.ok(await evaluate('document.documentElement.scrollWidth <= document.documentElement.clientWidth'),'Overflow at 200% equivalent viewport');
    assert.equal(await evaluate("document.getElementById('test-ai-model').getBoundingClientRect().width > 0"),true);
    assert.equal(errors.length,0,JSON.stringify(errors));
    assert.ok(calls.length >= 1);
    console.log(JSON.stringify({passed:true, widths, checks:['candidate isolation','loading','stale response','text-only preview','save payload','dirty form','keyboard','no overflow','200% equivalent viewport','no JS exceptions'], provider:'local mock only'}));
    await call('Browser.close'); ws.close(); server.close();
})().catch(error=>{console.error(error);chrome?.kill();ws?.close();server.close();process.exitCode=1;});
