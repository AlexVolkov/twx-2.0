<?php

/*
 * Thread main file
 */

//var_dump($argv);

///!!! THis is tested only for script run, not for cron launch
$source = 'script';

$thread_id = $argv[1];
$task_id = $argv[2];
$key_id = $argv[3];
$act = $argv[4];

/* ???? */
$key_id = $key_id;
$thread_id = $thread_id;
$task_id = $task_id;
$act = $act;

/* its must be a list of aviable services, which needs to get 
 * from include cycle of some of system directory 
 */

$avaibleServices = array(
    "twitter", "pingfm", "identica"
);

/* end */


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
$tdata = unserialize($task['task_data']);
//var_dump($task); die();

$core->WriteLog($task["mask"], "thread " . $thread_id . " started", $source, "1", "info");

foreach ($avaibleServices as $as) { //forming meta data dor every thread
    if (array_key_exists($as, $tdata))
        $metaThread[$as] = $tdata[$as];
}

//var_dump($task); die();

$lines = explode("\n", $task['task_content']);
//var_dump($lines); die();

//calculating ranges
foreach($metaThread as $k => $mt){
    $loadNum = ceil($mt / $tdata['threads']);
    //$accs = $core->LoadAccs($loadNum, $tdata['use_error_accounts'], $key_id);
    $accs = $core->load('account', $data, NULL, $loadNum);
    //$ranges[$k] = ($thread_id * ($mt / $tdata['threads']) ) . "|" . ($mt / $tdata['threads']) ;
    //var_dump($mt);
}

//var_dump($accs);
?>
