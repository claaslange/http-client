<?php

namespace React\HttpClient;

use React\SocketClient\ConnectorInterface;

class ProxyConnector implements ConnectorInterface
{
    private $proxyHost;
    private $proxyPort;
    private $connector;

    public function __construct($proxyHost, $proxyPort, ConnectorInterface $connector)
    {
        $this->proxyHost = $proxyHost;
        $this->proxyPort = $proxyPort;
        $this->connector = $connector;
    }

    // always connect to proxy, irrespective of given target host and port
    public function create($host, $port)
    {
        return $this->connector->create($this->proxyHost, $this->proxyPort);
    }
}
