<?php

namespace Mailman2Wrapper;
use Curl\Curl;
use \Exception;

class Client {

    private $host;
    private $group;
    private $curl;

    function __construct($host, $group, $password){
        $this->host = $host;
        $this->group = $group;
        $this->curl = new Curl;
        $this->login($password);
    }

    private function login($password){
        $this->curl->post($this->host . '/admin/' . $this->group, array(
            'admlogin' => 'Let+me+in...',
            'adminpw' => $password
        ));
        // Handle 401, 403, 500
        if($this->curl->error){
            throw new Exception($this->curl->httpErrorMessage, $this->curl->httpError);
        }
        $this->curl->setCookie('groups.neiist.rp+admin', $this->curl->getCookie('groups.neiist.rp+admin'));
        return true;
    }
    }
}