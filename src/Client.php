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
        $this->curl->setCookies($this->curl->responseCookies);
        return true;
    }

    public function subscribe($who, $welcome = true, $notify = true) {
        if(!is_array($who)) $who = array($who);

        $data = array(
            'csrf_token' => $this->getCSRF($this->host . '/admin/' . $this->group . '/members/add'),
            'subscribees' => join('\n', $who),
            'subscribe_or_invite' => 0,
            'send_welcome_msg_to_this_batch' => (int) $welcome,
            'send_notifications_to_list_owner' => (int) $notify,
            'setmemberopts_btn' => 'Submit your changes'
        );
        $this->curl->post($this->host . '/admin/' . $this->group . '/members/add', $data);

        // Handle 401, 403, 500
        if($this->curl->error){
            throw new Exception($this->curl->httpErrorMessage, $this->curl->httpError);
        }
        return true;
    }

    public function unsubscribe($who, $bye = true, $notify = true){
        if(!is_array($who)) $who = array($who);

        $data = array(
            'csrf_token' => $this->getCSRF($this->host . '/admin/' . $this->group . '/members/remove'),
            'unsubscribees' => join('\n', $who),
            'send_unsub_ack_to_this_batch' => (int) $bye,
            'send_unsub_notifications_to_list_owner' => (int) $notify,
            'setmemberopts_btn' => 'Submit your changes'
        );
        $this->curl->post($this->host . '/admin/' . $this->group . '/members/remove', $data);

        // Handle 401, 403, 500
        if($this->curl->error){
            throw new Exception($this->curl->httpErrorMessage, $this->curl->httpError);
        }
        return true;
    }

    public function updateName($email, $username){
        $this->curl->post($this->host . '/options/' . $this->group . '/' . $email, array(
            'fullname' => $username,
            'change-of-address' => 'Change My Address and Name'
        ));
        // Handle 401, 403, 500
        if($this->curl->error){
            throw new Exception($this->curl->httpErrorMessage, $this->curl->httpError);
        }
        return true;
    }

    public function listSubscribers(){
        $this->curl->get($this->host . '/roster/' . $this->group);
        // Handle 401, 403, 500
        if($this->curl->error){
            throw new Exception($this->curl->httpErrorMessage, $this->curl->httpError);
        }
        if(preg_match_all('%<a.*?href=(?:"|\').*?/options/' . $this->group . '/.*?(?:--at--|@).*?(?:"|\')-*?>(.*?(?:(?: |)*at(?: |)*|@).*?)</a>%', $this->curl->response, $matches)){
            foreach($matches[1] as &$value){
                $value = str_replace(' at ', '@', $value);
                $value = str_replace(' ', '', $value);
            }
            return $matches[1];
        }
        return array();
    }

    private function getCSRF($where){
        $this->curl->get($where);
        if($this->curl->error){
            throw new Exception($this->curl->httpErrorMessage, $this->curl->httpError);
        }
        if(preg_match('/<input.*?name=(\'|")csrf_token("|\'").*?>/', $this->curl->response, $match)){
            if(preg_match('/value=(?:\'|")(.*?)(?:\'|")/', $match[0], $match)){
                return $match[1];
            }
        }
        return false;
    }
}
