<?php

abstract class XMLHTTPRequestService {
    abstract public function request(string $url, string $method = 'POST', array $options = []);
}

class XMLHttpService extends XMLHTTPRequestService {
    public function request(string $url, string $method = 'POST', array $options = [])
    {

    }
}


class Http {
    private $service;

    public function __construct(XMLHTTPRequestService $service)
    {
        $this->service = $service;
    }

    public function get(string $url, array $options) {
        $this->service->request($url, 'GET', $options);
    }

    public function post(string $url, array $options) {
        $this->service->request($url, 'POST', $options);
    }
}

$xmlhttpreq = new XMLHttpService();
$http = new Http($xmlhttpreq);