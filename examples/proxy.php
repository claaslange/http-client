<?php

use React\Stream\BufferedSink;
require __DIR__ . '/../vendor/autoload.php';

$loop = React\EventLoop\Factory::create();

$dnsResolverFactory = new React\Dns\Resolver\Factory();
$dnsResolver = $dnsResolverFactory->createCached('8.8.8.8', $loop);

$factory = new React\HttpClient\Factory();
$client = $factory->create($loop, $dnsResolver);
$client = $client->withProxy('127.0.0.1:8080');

$request = $client->request('GET', 'http://github.com/');
$request->on('response', function ($response) {
    var_dump($response->getHeaders());

    BufferedSink::createPromise($response)->then(function ($body) {
        echo $body;
    });
});
$request->end();
$loop->run();
