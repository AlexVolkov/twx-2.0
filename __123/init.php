<?php

/**
 * class init
 * This class used for loading main script config,
 * establish connection with db,
 * list all aviable classes for posting,
 * check key and account up-to-date condition
 */
class init {
    /** Aggregations: */
    /** Compositions: */
    /*     * * Attributes: ** */

    /**
     * 
     * @access public
     */
    public $db;
    /**
     * 
     * @access public
     */
    public $configPath;
    /**
     * listClasses - basic initialize of all classes, which realize posting in services
     * @access public
     */
    public $listClasses;

    /**
     * Check user key and update it by server request
     *
     * @param string key 

     * @return bool
     * @access public
     */
    public function CheckKey($key) {
        
    }

// end of member function CheckKey

    /**
     * Check user just sign in and haven't account yet
     *
     * @param string key 

     * @return 
     * @access public
     */
    public function CheckFirstTime($key) {
        
    }

// end of member function CheckFirstTime

    /**
     * perform check for new version of database
     *
     * @param string key 

     * @return bool
     * @access public
     */
    public function CheckUpdate($key) {
        
    }

// end of member function CheckUpdate

    /**
     * 
     *
     * @param string key 

     * @return 
     * @access public
     */
    public function UpgradeAccount($key) {
        
    }

// end of member function UpgradeAccount

    /**
     * 
     *
     * @param string key 

     * @return array
     * @access public
     */
    public function LoadSettings($key) {
        
    }

// end of member function LoadSettings

    /**
     * upgrade db, insert sample data
     *
     * @param string key 

     * @return bool
     * @access public
     */
    public function Install($key) {
        
    }

// end of member function Install

    /**
     * 
     *
     * @param string key 

     * @return bool
     * @access public
     */
    public function BackUpData($key) {
        
    }

// end of member function BackUpData

    /**
     * 
     *
     * @param  null 

     * @return array
     * @access public
     */
    public function LoadBaseConf() {
        
    }

// end of member function LoadBaseConf
}

// end of init
?>
