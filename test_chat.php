<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::create('/api/chat', 'POST', ['message' => 'apa itu rpjmd?']);
$response = $kernel->handle($request);
echo $response->getContent();
