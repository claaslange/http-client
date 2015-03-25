<?php

namespace React\HttpClient;

class RequestData
{
    private $method;
    private $url;
    private $headers;

    private $requestUrl;
    private $protocolVersion = '1.1';

    public function __construct($method, $url, array $headers = array(), $requestUrl = null)
    {
        $this->method = $method;
        $this->url = $url;
        $this->headers = $headers;

        $this->requestUrl = ($requestUrl !== null) ? $requestUrl : $this->getPath();
    }

    private function mergeDefaultheaders(array $headers)
    {
        $port = ($this->getDefaultPort() === $this->getPort()) ? '' : ":{$this->getPort()}";
        $connectionHeaders = ('1.1' === $this->protocolVersion) ? array('Connection' => 'close') : array();

        return array_merge(
            array(
                'Host'          => $this->getHost().$port,
                'User-Agent'    => 'React/alpha',
            ),
            $connectionHeaders,
            $headers
        );
    }

    public function getScheme()
    {
        return parse_url($this->url, PHP_URL_SCHEME);
    }

    public function getHost()
    {
        return parse_url($this->url, PHP_URL_HOST);
    }

    public function getPort()
    {
        return (int) parse_url($this->url, PHP_URL_PORT) ?: $this->getDefaultPort();
    }

    public function getDefaultPort()
    {
        return ('https' === $this->getScheme()) ? 443 : 80;
    }

    public function getPath()
    {
        $path = parse_url($this->url, PHP_URL_PATH) ?: '/';
        $queryString = parse_url($this->url, PHP_URL_QUERY);

        return $path.($queryString ? "?$queryString" : '');
    }

    public function setProtocolVersion($version)
    {
        $this->protocolVersion = $version;
    }

    public function __toString()
    {
        $headers = $this->mergeDefaultheaders($this->headers);

        $data = '';
        $data .= "{$this->method} {$this->requestUrl} HTTP/{$this->protocolVersion}\r\n";
        foreach ($headers as $name => $value) {
            $data .= "$name: $value\r\n";
        }
        $data .= "\r\n";

        return $data;
    }
}
