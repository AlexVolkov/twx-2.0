<?php

$key_id = '9a20cd9d6421ed2d6d30d38172f6a237';

$task_id = '4';



/*
 * Main code for launching and handling streams
 *
 */


if (!require_once '../settings.php')
    die(_('ERROR_CANNOT_LOAD_SETTINGS'));

if (!require_once $config['path'] . 'system/core.actions.php')
    die(_('ERROR_CANNOT_LOAD_CORE_FILE'));

$core = new CoreActions($config);

switch ($core->keyCheck($key_id)):
    case ('freeze'):
        die(_(KEY_IS_SUSPENDED));
        break;
    case ('not exist'):
        die(_(KEY_IS_NOT_EXISTS));
        break;
    case ('not correct'):
        die(_(KEY_IS_NOT_CORRECT));
        break;
endswitch;

$data['key_id'] = $key_id;
$data['id'] = $task_id;

$task = $core->load('task', $data);

$taskDBQuery = "CREATE TEMPORARY TABLE `twx`.`tmp_task_mask` (
  `status` TINYINT(4) NULL DEFAULT NULL ,
  `pair` TEXT NULL DEFAULT NULL ,
  `service` TEXT NULL DEFAULT NULL,
  `text` TEXT NULL DEFAULT NULL,
  `link` TEXT NULL DEFAULT NULL)
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;";

$core->dbconn->query($taskDBQuery);

$tdata = unserialize($task['task_data']);

//remove empty values
foreach ($tdata as $tn => $td) {
    if ($td == '')
        unset($tdata[$tn]);
}

//var_dump($tdata); die();
//forming array with avaible services to post in

require_once $config['path'] . 'posters/post.interface.php';

if ($handle = opendir($config['path'] . 'posters/')) {                  //reading all poster's files
    while (false !== ($file = readdir($handle))) {
        if (strpos($file, ".class.php") > 0) {
            $clFile = substr($file, 0, strpos($file, ".class.php"));    //determine filename - name of class
            if (array_key_exists($clFile, $tdata)) {                    //if this name presents in task data
                $avaibleServices[] = $clFile;
                include_once $config['path'] . 'posters/' . $file;      //include it
                $poster[$clFile] = new $clFile;
            }
        }
    }
}

//var_dump($poster);die();
//forming meta for threads
$metaThread = array();
foreach ($avaibleServices as $as) {                                     //forming meta data dor every thread
    if (array_key_exists($as, $tdata))
        $metaThread["thread_data"][$as] = $tdata[$as];
}

var_dump($metaThread); die();

//threads launching
for ($i = 0; $i < $tdata["threads"]; $i++) {
    //$sh = stream_socket_client($url . ':80', $errno, $errstr, 10, STREAM_CLIENT_ASYNC_CONNECT | STREAM_CLIENT_CONNECT);
}



//if cronned part
//if manually launched part



/*
 * CREATE TEMPORARY TABLE `twx`.`tmp_task_mask` (
  `id` VARCHAR(32) NOT NULL ,
  `status` TINYINT(4) NULL DEFAULT NULL ,
  `pair` TEXT NULL DEFAULT NULL ,
  `service` TEXT NULL DEFAULT NULL )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;
 * 
 * 
  $dbconn = new PDO("mysql:host=" . $config['dbserver'] .
  ";dbname=" . $config['db'], $config['dbuser'], $config['dbpassword']);
  $dbconn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
 */


//var_dump($dbconn);
//
//require_once './init.php';
//$init = new init();
?>
