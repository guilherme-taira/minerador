<?php
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
require 'vendor/autoload.php';

class ConnectionCounter implements MessageComponentInterface {
    protected $clients;
    protected $userCount = 0;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {
        // When a new connection is opened
        $this->clients->attach($conn);
        $this->userCount++;
        echo "New connection! Total users: {$this->userCount}\n";

        // Notify all clients about the updated user count
        $this->broadcastUserCount();
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        // Optional: Handle incoming messages from clients
    }

    public function onClose(ConnectionInterface $conn) {
        // When a connection is closed
        $this->clients->detach($conn);
        $this->userCount--;
        echo "Connection closed! Total users: {$this->userCount}\n";

        // Notify all clients about the updated user count
        $this->broadcastUserCount();
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "Error: {$e->getMessage()}\n";
        $conn->close();
    }

    // Broadcast the current user count to all connected clients
    public function broadcastUserCount() {
        foreach ($this->clients as $client) {
            $client->send(json_encode(['userCount' => $this->userCount]));
        }
    }
}

// Run the WebSocket server
$server = \Ratchet\Server\IoServer::factory(
    new \Ratchet\Http\HttpServer(
        new \Ratchet\WebSocket\WsServer(
            new ConnectionCounter()
        )
    ),
    8080 // Port number for WebSocket
);

$server->run();
