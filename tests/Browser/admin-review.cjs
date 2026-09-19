// Visual smoke test against HTML rendered by AdminMultipageTest with a test database.
// Usage: node tests/Browser/admin-review.cjs baseline|after
const fs = require('node:fs');
const path = require('node:path');
const http = require('node:http');
const { spawn } = require('node:child_process');
const os = require('node:os');
const assert = require('node:assert/strict');
const mode = process.argv[2] || 'after';
const root = path.resolve(__dirname, '../..');
const review = path.join(root, 'storage/framework/testing/admin-review');
const pages = {dashboard: '/admin', dokumen: '/admin/dokumen', ingest: '/admin/ingest', pengguna: '/admin/pengguna', setelan: '/admin/setelan'};
const server = http.createServer((req, res) => {
    const url = new URL(req.url, 'http://localhost');
    let file;
    if (url.pathname.startsWith('/admin')) {
        const key = Object.keys(pages).find(key => pages[key] === url.pathname) || url.pathname.replace(/^\/admin\//, '').replaceAll('/', '-');
        file = path.join(review, mode === 'baseline' ? 'baseline.html' : `${key}.html`);
    } else {
        file = path.resolve(root, 'public', '.' + url.pathname);
        if (!file.startsWith(path.join(root, 'public') + path.sep)) { res.writeHead(403).end(); return; }
    }
    if (!fs.existsSync(file)) { res.writeHead(404).end(); return; }
    const ext = path.extname(file);
    res.setHeader('Content-Type', ({'.html':'text/html','.css':'text/css','.js':'text/javascript','.png':'image/png','.jpg':'image/jpeg'})[ext] || 'application/octet-stream');
    const data = fs.readFileSync(file);
    res.end(ext === '.html' ? data.toString().replaceAll('http://localhost', `http://127.0.0.1:${server.address().port}`) : data);
});
let chrome;
(async () => {
    await new Promise(resolve => server.listen(0, '127.0.0.1', resolve));
    const profile = fs.mkdtempSync(path.join(os.tmpdir(), 'rpjmd-admin-review-'));
    const binary = process.env.CHROME_PATH || 'C:/Program Files/Google/Chrome/Application/chrome.exe';
    chrome = spawn(binary, ['--headless=new', '--remote-debugging-port=0', `--user-data-dir=${profile}`, '--no-first-run', 'about:blank'], {windowsHide:true, stdio:'ignore'});
    const portFile = path.join(profile, 'DevToolsActivePort');
    for (let i=0; !fs.existsSync(portFile) && i<100; i++) await new Promise(r=>setTimeout(r,100));
    const port = fs.readFileSync(portFile, 'utf8').split('\n')[0];
    const targets = await fetch(`http://127.0.0.1:${port}/json/list`).then(r=>r.json());
    const ws = new WebSocket(targets.find(t=>t.type==='page').webSocketDebuggerUrl);
    await new Promise(r=>ws.addEventListener('open',r,{once:true}));
    let seq=0; const waiting=new Map(); const errors=[];
    ws.addEventListener('message', e=>{ const v=JSON.parse(e.data); if(v.id){const p=waiting.get(v.id);waiting.delete(v.id);v.error?p.reject(v.error):p.resolve(v.result);} if(v.method==='Runtime.exceptionThrown')errors.push(v.params.exceptionDetails.text+': '+(v.params.exceptionDetails.exception?.description||'')); });
    const call=(method,params={})=>new Promise((resolve,reject)=>{const id=++seq;waiting.set(id,{resolve,reject});ws.send(JSON.stringify({id,method,params}));});
    const evaluate=async expression=>(await call('Runtime.evaluate',{expression,returnByValue:true,awaitPromise:true})).result.value;
    await call('Page.enable'); await call('Runtime.enable');
    const results=[];
    for(const width of [375,768,1024,1440]){
        await call('Emulation.setDeviceMetricsOverride',{width,height:900,deviceScaleFactor:1,mobile:false});
        for(const [key,url] of Object.entries(pages)){
            await call('Page.navigate',{url:`http://127.0.0.1:${server.address().port}${url}`});
            await evaluate('new Promise(r=>setTimeout(r,600))');
            if(mode==='baseline')await evaluate(`showSection('section-${key}')`);
            await evaluate('document.fonts.ready');
            const metrics=await evaluate(`({width:innerWidth,scroll:document.documentElement.scrollWidth,section:document.querySelector('.content-section.block')?.id,active:document.querySelector('[aria-current="page"]')?.textContent.trim()})`);
            const shot=await call('Page.captureScreenshot',{format:'png',captureBeyondViewport:false});
            fs.writeFileSync(path.join(review,`${mode}-${key}-${width}.png`),Buffer.from(shot.data,'base64'));
            results.push({page:key,...metrics});
            if(mode==='after'){assert.equal(metrics.scroll,width,`${key} overflow at ${width}`);assert.equal(metrics.section,`section-${key}`);}
        }
    }
    fs.writeFileSync(path.join(review,`${mode}-results.json`),JSON.stringify({results,errors},null,2));
    console.log(JSON.stringify({screenshots:results.length,errors,overflow:results.filter(r=>r.scroll>r.width)},null,2));
    await call('Browser.close'); ws.close(); server.close();
    if(mode==='after')assert.equal(errors.length,0,'JavaScript errors');
})().catch(e=>{console.error(e); chrome?.kill();server.close();process.exitCode=1;});
