<?php

require_once __DIR__ . '/../vendor/autoload.php';

$fileName = __FILE__;
$usage = "Usage: php {$fileName} port command";

array_shift($argv);
$argCount = count($argv);
if ($argCount <= 1) {
    echo "Error: argument count should be greater than 1 ({$argCount} given)." . PHP_EOL;
    echo $usage . PHP_EOL;
    exit(1);
}

$port = array_shift($argv);
if (!is_numeric($port)) {
    echo "Error: first argument should be a port number ('{$port}' given)." . PHP_EOL;
    echo $usage . PHP_EOL;
    exit(1);
}

$cmd = implode(' ', $argv);
echo "Listening for WebSocket connections on port '{$port}'. Broadcasting command '{$cmd}'..." . PHP_EOL;

$app = new \Mmenozzi\ProcessWebSocket\App();

$loop = \React\EventLoop\Factory::create();
$webSock = new React\Socket\Server($loop);
$webSock->listen((int)$port, '0.0.0.0');

$processConnection = new \Mmenozzi\ProcessWebSocket\ProcessStream(popen($cmd, 'r'), $loop);
$processConnection->on('data', function ($data, $conn) use ($app) {
    $app->onStreamMessage($conn, $data);
});
$webSock->emit('connection', [$processConnection]);

$server = new \Ratchet\Server\IoServer(
    new \Ratchet\Http\HttpServer(
        new \Ratchet\WebSocket\WsServer(
            $app
        )
    ),
    $webSock,
    $loop
);

$server->run();
