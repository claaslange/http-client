<?php

namespace React\Tests\HttpClient;

use React\HttpClient\Client;
use React\Promise\Deferred;

class ClientTest extends TestCase
{
    private $tcp;
    private $tls;
    private $client;

    public function setUp()
    {
        $this->tcp = $this->getMock('React\SocketClient\ConnectorInterface');
        $this->tls = $this->getMock('React\SocketClient\ConnectorInterface');
        $this->client = new Client($this->tcp, $this->tls);
    }

    public function testGetHttp()
    {
        $stream = $this->createStreamMock();
        $promise = $this->createPromiseResolved($stream);

        $this->tcp->expects($this->once())->method('create')->with($this->equalTo('www.google.com'), $this->equalTo(80))->will($this->returnValue($promise));

        $this->tls->expects($this->never())->method('create');

        $request = $this->client->request('GET', 'http://www.google.com/');
        $request->end();
    }

    public function testGetHttps()
    {
        $stream = $this->createStreamMock();
        $promise = $this->createPromiseResolved($stream);

        $this->tcp->expects($this->never())->method('create');

        $this->tls->expects($this->once())->method('create')->with($this->equalTo('www.google.com'), $this->equalTo(443))->will($this->returnValue($promise));

        $request = $this->client->request('GET', 'https://www.google.com/');
        $request->end();
    }

    private function createStreamMock()
    {
        return $this->getMockBuilder('React\Stream\Stream')->disableOriginalConstructor()->getMock();
    }

    private function createPromiseResolved($value)
    {
        $deferred = new Deferred();
        $deferred->resolve($value);

        return $deferred->promise();
    }
}
