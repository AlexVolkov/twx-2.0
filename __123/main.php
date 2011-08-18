<?php

function __autoload($name) {
    if (!file_exists(__DIR__ . "/services/" . $name . ".class.php")) {
        Throw new Exception("Message to display");
    } else {
        include_once (__DIR__ . "/services/" . $name . ".class.php");
    }
}

try {
    $type = 'twitter_tweet';
    $type = explode("_", $type);

    //(!file_exists("./services/".$type[0].".class.php"))? die('123') : include_once ("./services/".$type[0].".class.php") ; 

    $workClass = new $type[0];
    var_dump($workClass->Post($type[1]));
} catch (Exception $e) {
    echo $e->getMessage();
}
?>
