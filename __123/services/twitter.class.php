<?php

require_once (__DIR__ . '/conn.class.php');
require_once (__DIR__ . '/post.interface.php');

class Twitter extends Connector implements Poster {

    public $proxy;

    function __construct() {
        $this->connect();
    }

    function retweet() {
        echo 'twitter retweet';
    }

    function tweet() {
        echo 'twitter tweet';
        $this->ReturnResult();
    }

    function ReturnResult() {
        return 'twitter return result';
    }

    function Post($type) {
        echo 'twitter posting to ' . $type;
        return $this->ReturnResult();
    }

}

?>
