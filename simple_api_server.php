<?php

// Simple API server để test với Postman
// Chạy: php simple_api_server.php
// Server sẽ chạy tại: http://localhost:8004

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';

// Create a simple HTTP server
$server = new \React\Http\HttpServer(function (\Psr\Http\Message\ServerRequestInterface $request) use ($app) {
    $path = $request->getUri()->getPath();
    $method = $request->getMethod();
    
    echo "Request: $method $path\n";
    
    try {
        // Handle the request through Laravel
        $response = $app->handle($request);
        
        return new \React\Http\Message\Response(
            200,
            ['Content-Type' => 'application/json'],
            $response->getContent()
        );
    } catch (Exception $e) {
        return new \React\Http\Message\Response(
            500,
            ['Content-Type' => 'application/json'],
            json_encode(['error' => $e->getMessage()])
        );
    }
});

$socket = new \React\Socket\SocketServer('127.0.0.1:8004');
$server->listen($socket);

echo "Writing System API Server running at http://localhost:8004\n";
echo "Available endpoints:\n";
echo "- GET /api/writing/qa-exercises\n";
echo "- GET /api/writing/sentence-building-exercises\n";
echo "- GET /api/writing/complete-sentence-exercises\n";
echo "- GET /api/writing/attempts\n";
echo "- POST /api/writing/attempts\n";
echo "\nPress Ctrl+C to stop the server\n";






