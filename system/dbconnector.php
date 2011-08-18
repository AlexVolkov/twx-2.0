<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class DBConn {

    public $db;

    function __construct($path) {
        try { 
            include_once $path.'settings.php';
            define(DBNAME, $dbs['db']);
            
            define(DBSERVER, $dbs['server']);
            define(DBUSER, $dbs['user']);
            define(DBPASS, $dbs['password']);
            define(DIRECTORY, $scriptRoot);
            define(AUTHURL, $authurl);
            $this->db = new PDO("mysql:host=" . DBSERVER . ";dbname=" . DBNAME, DBUSER, DBPASS);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo $e->getMessage(), "\n";
        }
    }

}

?>
