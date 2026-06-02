<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

use App\Models\Book;
use Illuminate\Http\Request;

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    Request::create('/api/books', 'GET')
);

echo json_encode(json_decode($response->getContent()), JSON_PRETTY_PRINT);
