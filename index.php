<?php

ini_set('display_errors', 1);
ini_set('output_buffering', 0);
ini_set('zlib.output_compression', 0);
ini_set('output_handler', 0);
ini_set('zlib.output_handler', 0);
ini_set('proxy_buffering', 0);
apache_setenv('no-gzip', 1);

require_once ('./settings.php');

//var_dump(stripos($_SERVER["HTTP_HOST"], "emo"));
if (stripos($_SERVER["HTTP_HOST"], "emo") == '1') {
    $text = "demo";
} else {
    $text = " ... Enter your key";
}

if ((isset($_POST['key_id'])) OR (isset($_COOKIE['key_id']) )) {

    ($_POST['key_id']) ? $key = $_POST['key_id'] : $key = $_COOKIE['key_id'];

    require_once ($config['path'] . '/system/core.actions.php');

    $action = new CoreActions($config);

    switch ($action->keyCheck($key)):
        case ('freeze'):
            $errmsg = "key is blocked";
            break;
        case ('not exist'):
            $errmsg = "key is not exist";
            break;
        case ('not correct'):
            $errmsg = "key is not correct";
            break;
        case ('ok'):
            $errmsg = "ok";




//TODO:what if we havent key in old and new database both?
            //check for first time           
            if ($action->firstTime($key)) {
                $action->importSamples($key);
            }

            //check if we need update this acc
            if ($action->checkUpdates($key)) {
                $action->importOldData($key);
            }


            $userSetts = $action->load('settings', $data = array('key_id' => $key), NULL);
            $str = $userSetts['parameter'];

            $str = unserialize($str);

            setcookie("key_id", $key, time() + (3600 * $str["stay_logged_in"]));

            header("Location:./activities.php");
            break;
    endswitch;
}


if (isset($_GET['expired']))
    $errmsg = 'Your session has been expired';

if (isset($_GET['noexist']))
    $errmsg = 'Key doesn\'t exist';

if (isset($_GET['notcorrect']))
    $errmsg = "This is not a key";

if (isset($_GET['loggedout']))
    $errmsg = "You're logged out";

if (isset($_GET['freeze']))
    $errmsg = "Your key has been blocked";

if (isset($_GET['dontknow']))
    $errmsg = "В душе не ебу, че это за ошибка";

if (isset($_GET['nokey']))
    $errmsg = 'Your key is not valid';

if (isset($_GET['expired']))
    $errmsg = 'Your session has been expired.';

if ($errmsg)
    $class = "style=\"display:block;\"";

$page = '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <title>Twindexator &mdash; member\'s area</title>
        <link rel="stylesheet" type="text/css" href="./css/login.css" />
        <link rel="SHORTCUT ICON" href="./i/favicon.ico" />
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    </head>
    <body>
        <div id="logo">&nbsp;</div>
        <div id="message" ' . $class . '>' . $errmsg . '</div>
        <div id="stripe">        
            <div id="keyform">
                <form method="post" action="./index.php">
                    <input type="text" name="key_id" value="" maxlength="32" placeholder=" ' . $text . '"/>
                    <input type="submit" value="" class="submit" />
                </form>
            </div>

        </div>
        <div id="footer">
            <ul>
                <li><a href="http://twindexator.com">Site</a></li>
                <li><a href="http://forum.twindexator.com">Forum</a></li>
                <li><a href="http://help.twindexator.com">Help</a></li>
            </ul>
        </div>
    </body>
</html>';


echo $page;
?>