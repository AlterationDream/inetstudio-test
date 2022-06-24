<?php
abstract class StorageService {
    abstract public function getOptionValue($for, $key);
}

class DB extends StorageService {

    private function runQuery($sql) {
        /*
         *
         */
        return $result;
    }

    public function getOptionValue($for, $key)
    {
        $sql = "
            SELECT value 
            FROM options
            WHERE namespace = " . $for ."
                AND key = " . $key;

        return self::runQuery($sql);
    }
}

class Concept {
    private $client;
    private $storageService;

    public function __construct(StorageService $storageService) {
        $this->client = new \GuzzleHttp\Client();
        $this->storageService = $storageService;
    }

    private function getSecretKey() {
        return $this->storageService->getOptionValue('concept', 'secretKey');
    }

    public function getUserData() {
        $params = [
            'auth' => ['user', 'pass'],
            'token' => $this->getSecretKey()
        ];

        $request = new \Request('GET', 'https://api.method', $params);
        $promise = $this->client->sendAsync($request)->then(function ($response) {
            $result = $response->getBody();
        });

        $promise->wait();
    }
}