<?php

namespace Mmenozzi\ProcessWebSocket;

use Ratchet\ConnectionInterface as RatchetConnection;
use Ratchet\ConnectionInterface;
use React\EventLoop\LoopInterface;
use React\Socket\ConnectionInterface as ReactConnection;
use React\Stream\Stream;

class ProcessStream extends Stream implements ReactConnection, RatchetConnection
{
    public $resourceId;

    public function __construct($stream, LoopInterface $loop)
    {
        parent::__construct($stream, $loop);
        $this->resourceId = (int)$stream;
    }

    /**
     * Returns the remote address (client IP) where this connection has been established from
     *
     * @return string|null remote address (client IP) or null if unknown
     */
    public function getRemoteAddress()
    {
        return null;
    }

    /**
     * Send data to the connection
     * @param  string $data
     * @return ConnectionInterface
     */
    public function send($data)
    {
        // This is a read only stream. So send method does nothing.
        return $this;
    }
}
