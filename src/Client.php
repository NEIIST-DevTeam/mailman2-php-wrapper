<?php

namespace Mailman2Wrapper;
use Curl\Curl;
use \Exception;

class Client {

    private $base;
    private $curl;

    function __construct($base, $password){
        $this->base = $base;
        $this->curl = new Curl;
        $this->login($password);
    }

    private function login($password){
        $this->curl->post($this->base, array(
            'admlogin' => 'Let+me+in...',
            'adminpw' => $password
        ));
        // Handle 401, 403, 500
        if($this->curl->error){
            throw new Exception($this->curl->httpErrorMessage, $this->curl->httpError);
        }
        else return true;
    }
}