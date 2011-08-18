<?php

require_once 'init.php';


/**
 * class dataHandle
 * Basic text operations, save and load actions.
 */
class dataHandle
{

/** Aggregations: */
var $m_;

/** Compositions: */
/* * * Attributes: ** */

/**
 * 
 * @access public
 */
public $db;


/**
 * 
 *
 * @param string table Defines from what table data will be fetched

 * @param string mask Unique task identifier

 * @return array
 * @access public
 */
public function LoadData( $table, $mask) {
} // end of member function LoadData

/**
 * 
 *
 * @param string table What table to save in

 * @param string mask Unique task identifier

 * @return bool
 * @access public
 */
public function SaveData( $table, $mask ) {
} // end of member function SaveData

/**
 * 
 *
 * @param string type Which type of message window will be returned: error, success, info.

 * @param string message text message to display in window

 * @return string
 * @access public
 */
public function ShowWindow( $type, $message ) {
} // end of member function ShowWindow

/**
 * 
 *
 * @param array data List of urls

 * @return array
 * @access public
 */
public function LoadSitemap( $data ) {
} // end of member function LoadSitemap

/**
 * 
 *
 * @param array data List of urls

 * @return array
 * @access public
 */
public function GrabTitles( $data ) {
} // end of member function GrabTitles

/**
 * 
 *
 * @param array data List of urls

 * @return array
 * @access public
 */
public function StripLinks( $data ) {
} // end of member function StripLinks

/**
 * 
 *
 * @param int limit Length of future string

 * @param string string String to cut of

 * @return string
 * @access public
 */
public function CutString( $limit, $string ) {
} // end of member function CutString

/**
 * 
 *
 * @param string feedlink Check for feed correct name/consistence

 * @return bool
 * @access public
 */
public function IsFeed( $feedlink ) {
} // end of member function IsFeed

/**
 * 
 *
 * @param array data 

 * @return array
 * @access public
 */
public function CheckReceivedParams( $data ) {
} // end of member function CheckReceivedParams

/**
 * Loads part of task content from currentPosition
 *
 * @param int currentPosition 

 * @return array
 * @access public
 */
public function LoadFromPosition( $currentPosition ) {
} // end of member function LoadFromPosition

/**
 * writes down log record
 *
 * @param char date 

 * @param string message 

 * @param int loglevel 

 * @param char from from script or from cron

 * @return 
 * @access public
 */
public function WriteLog( $date, $message, $loglevel, $from ) {
} // end of member function WriteLog

/**
 * 
 *
 * @param array taskData must be an url

 * @param char mask task identifier

 * @return array
 * @access public
 */
public function GetNewItems( $taskData, $mask ) {
} // end of member function GetNewItems

/**
 * Feed, which will be converted into task array
 *
 * @param string url url of feed

 * @return array
 * @access public
 */
public function LoadFeed( $url ) {
} // end of member function LoadFeed

/**
 * combine all data and split it into chunks for threads
 *
 * @param array taskContent all task lines to post in

 * @param array taskSettings all task settings

 * @return array
 * @access public
 */
public function SplitData( $taskContent, $taskSettings ) {
} // end of member function SplitData





} // end of dataHandle
?>
