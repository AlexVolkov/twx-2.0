<?php

/* core class to interact with database and 
 * perform basic account actions
 */

error_reporting(1);

class CoreActions {

    public $dbconn;
    public $config;

    function __construct($config) {

        if (!$config) {
            die(_('_CANNOT_READ_CONFIGURATION'));
        }

        $this->config = $config;
        if (!$dbconn) {
            $this->dbconn = new PDO("mysql:host=" . $config['dbserver'] .
                            ";dbname=" . $config['db'], $config['dbuser'], $config['dbpassword']);
            $this->dbconn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } else {
            $this->dbconn = $dbconn;
        }
        //var_dump($this->dbconn);
    }

    public function keyCheck($key) {
        $res = @file_get_contents($this->config['authUrl'] . $key);
        //var_dump($res);
        if ($res == $key . '|1') {
            return("ok");
        }

        if ($res == $key . '|2')
            return("freeze");

        if ($res == $key . '|8')
            return("not exist");

        if ($res == $key . '|9')
            return("not correct");
    }

    public function keyExists($key) {
        if ($key == '' OR $key == false) {
            die(_('ERROR_EMPTY_KEY'));
        }

        preg_match("#^[0-9a-f]{32}$#i", $key, $retk);

        if (!$retk)
            die(_('ERROR_KEY_IS_NOT_VALID_FORMAT'));

        $sql = "SELECT `id` FROM `" . $this->config['db'] . "`.`keys` WHERE `id` = '" . $key . "' LIMIT 1;";
        $res = $this->dbconn->query($sql)->fetch(PDO::FETCH_ASSOC);
        if ($res["id"] != $key)
            die(_('KEY_NOT_FOUND_IN_DATABASE'));
        return true;
    }

    public function load($what, $data, $exclude = NULL) {

        if (!$data['key_id'])
            die('NO_KEY_SPECIFIED');
        switch ($what) {
            default:
                break;
            case "account":
                if (!$data['id'])
                    die('NO_ID_SPECIFIED');
                $sql = "SELECT * FROM `" . DBNAME . "`.`accounts` WHERE `id` = '"
                        . $data['id'] . " AND `key_id` = " . $data['key_id'] . " ';";
                $res = $this->dbconn->db->query($sql)->fetch(PDO::FETCH_ASSOC);
                var_dump($res);
                break;

            case "settings":
                $sql = "SELECT * FROM `" . $this->config['db'] . "`.`user_config` WHERE `key_id` = '"
                        . $data['key_id'] . "';";
                //echo $sql; die();
                $res = $this->dbconn->query($sql)->fetch(PDO::FETCH_ASSOC);
                return $res;
                break;
            case "task":
                if (!$data['id'])
                    die('NO_ID_SPECIFIED');
                $sql = "SELECT * FROM `" . DBNAME . "`.`tasks` WHERE `id` = '"
                        . $data['id'] . " AND `key_id` = " . $data['key_id'] . " ';";
                $res = $this->dbconn->db->query($sql)->fetch(PDO::FETCH_ASSOC);
                var_dump($res);
                break;
        }
    }

    public function oneLine($data) {
        foreach ($data as $n => $d):
            $data[$n] = preg_replace("/\n/", "#n", $d);
            $data[$n] = preg_replace("/\r/", "#r", $d);
        //return str_replace(array("\r", "\n"), array('%', '#'),$data);

        endforeach;
        return $data;
    }

    public function save($data, $where, $exclude = NULL) {
        unset($str);

        if (!$data['key_id'])
            die("NO_KEY_SPECIFIED");

        if (($kc = $this->keyCheck($data['key_id'])) != 'ok')
            die("key is " . $kc);

        if (!$data['operation'])
            die("don't know what to do");

        $ans = $this->keyExists($data['key_id']);
        if (!$ans)
            die(_($ans));


        $insertStat = "INSERT INTO %TABLE% (%colname%) VALUES (%colvalue%)";

        switch ($where) {
            case("addtask"):
                $mainData = array(
                    'key_id' => $data["key_id"],
                    'operation' => $data["operation"],
                    'mask' => $data["mask"],
                    'task_type' => $data["task_type"],
                    'service' => $data["service"],
                    'task_name' => $data["task_name"],
                    'task_content' => $data["task_content"],
                    'task_cron_intval' => $data["task_cron_intval"],
                    'task_progress' => '0',
                    'task_status' => 'stop'
                );
                $table = 'tasks';
                $mainData['task_data']['task_creation_time'] = date("r");
                $mainData['task_data']['task_modified_time'] = date("r");
                $mainData['task_data']['task_last_launch'] = date("r");
                foreach ($data as $d => $t):
                    if (!array_key_exists(trim($d), $mainData)) {
                        $mainData['task_data'][$d] = $t;
                    }
                endforeach;

                unset($mainData['key'], $mainData['operation'], $ins, $vals);
                foreach ($mainData as $m => $t) {
                    $ins .= "`" . $m . "`, ";
                    if (!is_array($t)) {
                        $vals .= "'" . $t . "', ";
                    } else {
                        $vals .= "'" . serialize($t) . "', ";
                    }
                }
                $q = "INSERT INTO `" . $this->config['db'] . "`.`" . $table . "` (" .
                        substr($ins, 0, -2) . ") VALUES (" . substr($vals, 0, -2) . ");";
                break;

            case("addaccs"):
                $mainData = array(
                    'key_id' => $data["key_id"],
                    'service' => $data["service"],
                    'pair' => $data["account"],
                    'error' => ''
                );
                $table = 'accounts';
                $sqlAct = $insertStat;
                break;

            case("updatesettings"):
                $exclude = array(
                    'key_id',
                    'operation'
                );
                $q = $this->sqlUpdate('user_config', $data, $exclude, NULL);
                //var_dump($q);
                break;

            case("updatetask"):
                $exclude = array(
                    'what',
                    'operation',
                    'id',
                    'key_id'
                );
                $packed = array(
                    'numAccs',
                    'is_dripped',
                    'task_shortener',
                    'threads',
                    'work_by_sitemap',
                    'grab_titles',
                    'strip_links'
                );

                $q = $this->sqlUpdate('tasks', $data, $exclude, $packed);
                break;

            case("updateacc"):
                var_dump($data);
                $exclude = array(
                    'what',
                    'operation',
                    'id',
                    'key_id'
                );

                $q = $this->sqlUpdate('accounts', $data, $exclude, NULL);
                break;

            default:
                die('can not determine where to save');
        }

        //var_dump($q);
        //die();

        if ($this->dbconn->query($q)) {
            echo _("SAVED_SUCCESSFULLY");
        } else {
            echo _("CANNOT_PERFORM_QUERY");
        }
    }

    public function firstTime($key) {
        $sql = "SELECT `keys`.`id` FROM `" . $this->config['db'] . "`.`keys` WHERE `keys`.`id` = '$key'";
        $r = $this->dbconn->query($sql)->fetch(PDO::FETCH_ASSOC);
        return $r;
    }

    public function importSamples($key) {
        $page = file_get_contents($this->config['path'] . "/pages/welcome.php");

        $this->flushData(1);

        echo $page;

        $sql = "INSERT INTO " . $this->config['db'] . ".`keys` (`id`, `status`, `valid_thru`, `owner_name`, `last_login`, `restrictions`)  VALUES ('$key', 1, '0', 'noname', '0', '0')";
        //$sql = "SELECT `keys`.`id` FROM `" . $this->config['db'] . "`.`keys` WHERE `keys`.`id` = '$key'";

        echo "<div id=\"info\"><p>";
        echo _("create your account");
        echo str_repeat(".", 10);
        if (!$this->dbconn->query($sql))
            die(_('ERROR_IMPORT_KEY_FIRST_TIME'));
        //return $r;
        echo _("done");
        echo "<br/>";
        $this->flushData(1);


        //import settings
        echo _("import default user settings");
        echo str_repeat(".", 10);
        $sql = "INSERT INTO `twx`.`user_config` (`id`, `parameter`, `key_id`) VALUES (NULL, 'a:4:{s:14:\"stay_logged_in\";s:1:\"5\";s:10:\"removeLogs\";s:1:\"3\";s:23:\"removeTaskWithoutLaunch\";s:1:\"3\";s:7:\"pingers\";s:0:\"\";}', '$key');";

        if (!$this->dbconn->query($sql))
            die(_('ERROR_IMPORT_USER_SETTINGS_FIRST_TIME'));

        echo _("done");
        echo "<br/>";
        $this->flushData(1);

        //import sample tasks
        echo _("import sample tasks");
        echo str_repeat(".", 10);
        $sql = "INSERT INTO `twx`.`tasks` (`id`, `key_id`, `mask`, `task_name`, `task_data`, `task_content`, `task_cron_intval`, `task_progress`, `task_status`, `task_type`, `service`) VALUES (NULL, '$key', '', 'noname', NULL, NULL, '0', '0', 'stop', NULL, NULL);";
        if (!$this->dbconn->query($sql))
            die(_('ERROR_IMPORT_SAMPLE_TASKS_FIRST_TIME'));

        echo _("done");
        echo "<br/>";
        $this->flushData(1);

        //import accounts
        echo _("import sample accounts");
        echo str_repeat(".", 10);
        $sql = "INSERT INTO `twx`.`accounts` (`id`, `pair`, `service`, `error`, `key_id`) VALUES (NULL, 'vasya:123456', 'twitter', NULL, '$key');";
        if (!$this->dbconn->query($sql))
            die(_('ERROR_IMPORT_SAMPLE_ACCOUNTS_FIRST_TIME'));

        echo _("done");
        echo "<br/>";
        $this->flushData(1);


        echo _("account created");
        $this->flushData(3);
        echo ("<script type=\"text/javascript\">
            <!--
                
                window.location = \"./\"
            
            //-->
        </script>");


        echo "</p></div>";
    }

    public function checkUpdates($key) {
        $query = $this->dbconn->query("SELECT `id` FROM `" . $this->config['db'] . "`.`keys` WHERE `id` = '" . $key . "';")->Fetch(PDO::FETCH_ASSOC);

        if ($query) {
            return false;
        }

        $query = $this->dbconn->query("USE `service`;");
        $query = $this->dbconn->query("SELECT `key` FROM `keys` WHERE `key` = '$key';")->Fetch(PDO::FETCH_ASSOC);

        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    private function flushData($n) {
        if (ob_get_length()) {
            @ob_flush();
            @flush();
            @ob_end_flush();
        }
        @ob_start();
        sleep($n);
    }

    private function getPercent($number, $total) {
        $percentage = round($number * 100 / $total) . "%";
        return $percentage;
    }

    public function importOldData($key) {
        $page = file_get_contents($this->config['path'] . "/pages/maintenance.php");
        echo $page;
        $this->flushData(1);
        $sql = "USE `cust_tables`";
        $this->dbconn->query($sql);


        echo "<div id=\"info\">";
        echo "<br/>";
        echo _("begin upgrading your account");
        $this->flushData(1);
        echo "<br/>";



        //create key
        echo _("create your account");
        echo str_repeat(".", 10);
        //create key
        $sql = "INSERT INTO " . $this->config['db'] . ".`keys` (`id`, `status`, `valid_thru`, `owner_name`, `last_login`, `restrictions`)  VALUES ('$key', 1, '0', 'noname', '0', '0')";
        if (!$this->dbconn->query($sql))
            die(_('ERROR_IMPORT_KEY_WHILE_UPGRADE'));
        echo _("done");
        echo "<br/>";
        $this->flushData(1);


        //import tasks
        $sql = "SELECT * FROM `cust_tables`.`" . $key . "_tasks`";
        $r = $this->dbconn->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        echo _("transferring tasks");
        echo str_repeat(".", 10);
        $this->flushData(1);
        foreach ($r as $n => $v) {
            $mask = $this->GenerateMask();

            switch ($v["source"]) {
                case("tweets"):
                    $service = "twitter_single";
                    break;
                case("feeds"):
                    $service = "twitter_feed";
                    break;
                case("retweet"):
                    $service = "twitter_retweet";
                    break;
                case("follow"):
                    $service = "twitter_follow";
                    break;
            }

            if ($v["cronIntval"] == '')
                $v["cronIntval"] = 0;

            $s['service'] = $service;
            $s['accs'] = $v['used_accounts'];
            $s['shortener'] = $v['shortener'];
            $s['task_threads'] = 10;
            $s['task_creation_time'] = 'NOW()';
            $s['task_modified_time'] = 'NOW()';
            $s['task_last_launch'] = 'NOW()';
            $s['task_ping_results'] = 'no';
            $s['task_skip_accs_with_errors'] = 'yes';
            $s['task_work_by_sitemap'] = 'no';
            $s['task_grab_titles'] = 'no';
            $s['strip_links'] = 'no';
            $s['is_dripped'] = 'no';


            $s = serialize($s);
            //var_dump(($s)); die();

            $tq = "INSERT INTO `twx`.`tasks` (
                                        `id` ,
                                        `key_id` ,
                                        `mask` ,
                                        `task_name` ,
                                        `task_data` ,
                                        `task_content` ,
                                        `task_cron_intval` ,
                                        `task_progress` ,
                                        `task_status` 

                                    ) VALUES (
                                        NULL , 
                                        '$key', 
                                        '$mask', 
                                        '$v[task_name]', 
                                        '$s' , 
                                        '$v[content]', 
                                        '$v[cronIntval]', 
                                        '$v[progress]', 
                                        '$v[status]'
                                    );";
            //var_dump($tq); die();
            unset($s);
            if (!$this->dbconn->query($tq))
                die(_('ERROR_IMPORT_TASKS_WHILE_UPGRADE'));
        }
        echo _("done");
        echo "<br/>";
        $this->flushData(1);


        //import accounts
        //TODO: get percentage instead num of accs
        echo _("transferring accounts");
        echo str_repeat(".", 10);
        $this->flushData(1);
        $query = "SELECT * FROM `cust_tables`.`" . $key . "_accounts` WHERE 1 = 1;";
        $q = $this->dbconn->query($query)->FetchAll(PDO::FETCH_ASSOC);

        $numToImp = 0;
        $aq = '';
        foreach ($q as $n => $v) {
            $aq .= "INSERT INTO `twx`.`accounts` (
                  `id` ,
                  `pair` ,
                  `service` ,
                  `error` ,
                  `key_id`
          )VALUES (
                  NULL ,
                  '$v[pair]' ,
                  'twitter' ,
                  '$v[error]',
                  '$key'
          );";

            if ($numToImp == 1000) {
                if (!$this->dbconn->query($aq))
                    die(_('ERROR_IMPORT_ACCOUNTS_WHILE_UPGRADE'));
                $numToImp = 0;
                $aq = '';
                echo "<div id=\"overlay\">" . $this->getPercent($n, count($q)) . "</div> ";
                $this->flushData(0);
            } else {
                $numToImp++;
            }

            if (($n + 1) == count($q)) {
                if (!$this->dbconn->query($aq))
                    die(_('ERROR_IMPORT_ACCOUNTS_WHILE_UPGRADE'));
                break;
            }
        }
        unset($ag);
        echo "<div id=\"overlay\">" . _("done"). "</div> ";
        echo "<br/>";
        $this->flushData(1);
        //ends import accounts
        
        
        //import settings
        echo _("transferring user settings");
        echo str_repeat(".", 10);
        $this->flushData(1);
        $query = "SELECT * FROM `cust_tables`.`" . $key . "_config` WHERE 1 = 1;";
        $qw = $this->dbconn->query($query)->FetchAll(PDO::FETCH_ASSOC);

        foreach ($qw as $n => $v) {
            $im[$v['opt_name']] = $v[opt_value];
        }

        $aq = "INSERT INTO `twx`.`user_config` (
                    `parameter` ,
                    `key_id`
                )VALUES (
                     '" . serialize($im) . "' , 
                     '$key'
               );";

        if (!$this->dbconn->query($aq))
            die(_('ERROR_IMPORT_SETTINGS_WHILE_UPGRADE'));
        //ends import settings


        echo _("done");
        echo "<br/>";
        $this->flushData(3); 
        echo ("<script type=\"text/javascript\">
            <!--
                
                window.location = \"./\"
            
            //-->
        </script>");

        echo "</div>";
    }

    private function GenerateMask() {
        //sleep(1);
        return substr(md5(rand(0, 10000000000)), -10);
    }

    private function sqlUpdate($where, $data, $exclude, $packed) {
        $updateStat = "UPDATE %TABLE% SET ";
        $sqlAct = $updateStat;


        $sqlAct = preg_replace("!%TABLE%!si", "`" . $this->config['db'] . "`.`" . $where . "`", $sqlAct);


        foreach ($data as $s => $d) {

            if (!is_int(array_search(trim($s), $exclude))) { //if we havent find exclude element
                if (is_int(array_search(trim($s), $packed))) {
                    $serData[$s] = $this->oneLine($d);
                    //$serData[$s] = $this->$d;
                    continue;
                }

                $sqlAct .= "`" . $s . "` = '" . $d . "', ";
            }
        }

        if (isset($serData))
            $sqlAct .= "`task_data` = '" . serialize($serData) . "', ";

        $sqlAct = substr($sqlAct, 0, -2);
        if ($where == "user_config") {
            $sqlAct .= " WHERE `key_id` = '" . $data['key_id'] . "';";
        } else {
            $sqlAct .= " WHERE `id` = '" . $data['id'] . "';";
        }
        return $sqlAct;
    }

}

//$tmp = new CoreActions(null, '/var/www/demo');
//var_dump($tmp);
?>
