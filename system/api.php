<?php

/*
 * API 
 * 
 */


//net proverki klucha
require_once '../settings.php';
require_once $config['path'] . 'system/core.actions.php';

if ($_POST["what"] && $_POST["what"] != '') {
    $act = new CoreActions($config);
} else {
    die (_("NO_ACTION_DEFINED"));
}

if ($data['key_id'])
    die(_("NO_KEY_SPECIFIED"));

//var_dump($_POST["what"]);

switch ($_POST["what"]) {

    case "task":
        $what = "addtask";

        if (isset($_POST['id']))
            $what = "updatetask";

        $act->save($_POST, $what);
        break;

    case "addAccount":
        $accs = explode("\n", $_POST['account']);

        foreach ($accs as $num => $acc) {
            if (strlen($acc) > 3) {
                $accs[$num] = trim($acc);
            }
        }
        $accs = array_unique($accs);
        foreach ($accs as $num => $acc) {
            $data['service'] = $_POST['service'];
            $data['key_id'] = $_POST['key_id'];
            $data['account'] = $acc;
            $data['operation'] = "addaccs";
            $tmp = $act->save($data, 'addaccs');
        }
        break;

    case "updateAccount":
        $data['key_id'] = $_POST['key_id'];
        $data['pair'] = $_POST['pair'];
                $data['id'] = $_POST['id'];
        $data['operation'] = "updateacc";
        $tmp = $act->save($data, 'updateacc');
        break;

    case "updateSettings":
        $data['key_id'] = $_POST['key_id'];
        $data['operation'] = "updatesettings";
        $data['parameter'] = ($_POST);
        unset($data['parameter']['key_id'], $data['parameter']['what']);
        $data['parameter']['pingers'] = base64_encode($data['parameter']['pingers']); 
        //var_dump($data); die();
        //$data['parameter'] = $act->oneLine($data['parameter']);
        $data['parameter'] = serialize($data['parameter']);
        $tmp = $act->save($data, "updatesettings");
        break;

    default:
        die(_("NO_ACTION_TO_DO"));
        break; //TODO throw except for no-defined action
}
?>
