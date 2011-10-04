<?php


class Identica  implements Poster {

    public $proxy;
    private $mainpage = 'http://identi.ca';
    
    function __construct() {
        //$this->connect();
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
    
    function LoadPage($page) {
        //
    }

}

//$test = new Identica();
?>
