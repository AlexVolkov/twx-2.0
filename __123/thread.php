<?php

require_once 'dataHandle.php';

/**
 * class thread
 * Single thread class
 * __construct performs restrict for max instances
 */
class thread extends dataHandle {
    /** Aggregations: */
    /** Compositions: */
    /*     * * Attributes: ** */

    /**
     * 
     * @static
     * @access public
     */
    public static $instances;
    /**
     * 
     * @access public
     */
    public $db;
    /**
     * 
     * @access public
     */
    public $thParams;
    /**
     * 
     * @access public
     */
    public $taskParams;

    /**
     * 
     *
     * @return 
     * @access public
     */
    public function __construct() {
        
    }

// end of member function __construct

    /**
     * 
     *
     * @return string
     * @access public
     */
    public function ReloadProxy() {
        
    }

// end of member function ReloadProxy

    /**
     * 
     *
     * @param bool UseErrors Var indicates using accounts with errors. Reads from task config

     * @return array
     * @access public
     */
    public function LoadAccs($UseErrors) {
        
    }

// end of member function LoadAccs

    /**
     * 
     *
     * @param int value Current position of progress marker of this thread

     * @return 
     * @access public
     */
    public function ChangeProgress($value) {
        
    }

// end of member function ChangeProgress
}

// end of thread
?>
