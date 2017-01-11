<?php

namespace Mmenozzi\ProcessWebSocket;

use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

class App implements MessageComponentInterface
{
    const BUFFER_SIZE_LIMIT = 300;

    /**
     * @var \SplObjectStorage
     */
    private $clients;

    /**
     * @var array
     */
    private $buffer = [];

    public function __construct()
    {
        $this->clients = new \SplObjectStorage();
    }

    public function onOpen(ConnectionInterface $conn)
    {
        // Store the new connection to send messages to later
        $this->clients->attach($conn);
        echo "New connection! ({$conn->resourceId})" . PHP_EOL;
        $msg = implode('', array_slice($this->buffer, -10));
        echo "Sending message '{$msg}' to client {$conn->resourceId}" . PHP_EOL;
        $conn->send($msg);
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        echo "Message '{$msg}' from client {$from->resourceId}" . PHP_EOL;
        $from->send('You\'re not allowed to send messages.');
    }

    public function onClose(ConnectionInterface $conn)
    {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);
        echo "Connection {$conn->resourceId} has disconnected" . PHP_EOL;
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}" . PHP_EOL;
        $conn->close();
    }

    public function onStreamMessage(ProcessStream $from, $msg)
    {
        echo "Message '{$msg}' from process stream {$from->resourceId}" . PHP_EOL;
        $this->buffer[] = $msg;
        $this->buffer = array_slice($this->buffer, self::BUFFER_SIZE_LIMIT * -1);
        foreach ($this->clients as $client) {
            echo "Sending message '{$msg}' to client {$client->resourceId}" . PHP_EOL;
            $client->send($msg);
        }
    }
}
