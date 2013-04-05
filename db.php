<?php

/*
 * Set up the SQLite database object, which allows the logging of various events. A simple log table.
 */
if( !defined('IN_CODE') ) die();

global $db;
$db = new SQLite3 ('log.sqlite');

if ( !$db->exec("CREATE TABLE IF NOT EXISTS Log (Id INTEGER PRIMARY KEY, EventType varchar(50), EventDetails TEXT, Timestamp INTEGER )") )
trigger_error("Error creating default table in SQLite database: ".$db->lastErrorMsg());

function log_event($eventType, $eventDetails)
{
	global $db;
	$eventType=$db->escapeString($eventType);
	$eventDetails=$db->escapeString($eventDetails);
	if( !$db->exec("INSERT INTO Log(EventType, EventDetails, Timestamp) VALUES ('".$eventType."','".$eventDetails."', '".time()."')") )
	trigger_error("Error while logging event: ".$db->lastErrorMsg());
}
