<?php

namespace React\HttpClient;

use React\SocketClient\ConnectorInterface;

class Client
{
    private $connector;
    private $secureConnector;

    private $proxyConnector = null;

    public function __construct(ConnectorInterface $connector, ConnectorInterface $secureConnector)
    {
        $this->connector = $connector;
        $this->secureConnector = $secureConnector;
    }

    public function request($method, $url, array $headers = array())
    {
        $requestUrl = null;
        if ($this->proxyConnector !== null) {
            $requestUrl = $url;
        }

        $requestData = new RequestData($method, $url, $headers, $requestUrl);

        if ($this->proxyConnector !== null) {
            $connector = $this->proxyConnector;
        } else {
            $connector = $this->getConnectorForScheme($requestData->getScheme());
        }

        return new Request($connector, $requestData);
    }

    public function withProxy($proxy)
    {
        if (strpos($proxy, '://') === false) {
            $proxy = 'http://' . $proxy;
        }

        $parts = parse_url($proxy);

        $client = clone $this;
        $client->proxyConnector = new ProxyConnector($parts['host'], $parts['port'] ?: 80, $this->connector);

        return $client;
    }

    public function withoutProxy()
    {
        $client = clone $this;
        $client->proxyConnector = null;

        return $client;
    }

    private function getConnectorForScheme($scheme)
    {
        return ('https' === $scheme) ? $this->secureConnector : $this->connector;
    }
}
