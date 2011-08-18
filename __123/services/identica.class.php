<?php

require_once (__DIR__ . '/conn.class.php');
require_once (__DIR__ . '/post.interface.php');

class Identica extends Connector implements Poster {

    public $proxy;

    function __construct() {
        $this->connect();
    }

    function retweet() {
        echo 'identica retweet';
    }

    function tweet() {
        echo 'identica tweet';
    }

    function ReturnResult() {
        return 'identica return result';
    }

    function Post($type) {
        echo 'identica posting to ' . $type;
        return $this->ReturnResult();
    }

}

//$test = new Identica();
?>
