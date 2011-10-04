<?php


class twitter implements Poster {

    public $pair;
    public $tweet;
    public $proxy = null;
    public $retweet;
    public $follow;
    protected $token;
    public $id;
    //private $mainpage = 'http://mobile.twitter.com/';
    private $mainpage = 'http://internet.yandex.ru/';

    /*function __construct($config) {

        if (!$config) {
            die(_('_CANNOT_READ_CONFIGURATION'));
        }

        $this->config = $config;
        //require_once (__DIR__ . '/post.interface.php');
        if (!$dbconn) {
            $this->dbconn = new PDO("mysql:host=" . $config['dbserver'] .
                            ";dbname=" . $config['db'], $config['dbuser'], $config['dbpassword']);
            $this->dbconn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } else {
            $this->dbconn = $dbconn;
        }
        //var_dump($this->dbconn);
    }*/
    
    public function LoadPage($page){
        
    }

    public function Retweet() {
        echo 'twitter retweet';
    }

    public function Tweet() {
        echo 'twitter tweet';
        $this->ReturnResult();
    }

    public function ReturnResult() {
        return 'twitter return result';
    }

    public function Post($type) {
        echo 'twitter posting to ' . $type;
        return $this->ReturnResult();
    }

}

?>
