<?php

/*
 * The main script for pingability-lite. 
 * 
 * - Set up error handling (which must be robust for this kind of app)
 * - Load the settings
 * - Set up the mailer
 * - Set up the SQLite log database
 * - If we're not running a check then output the recent log entries
 * - Otherwise run a check
 */
define('IN_CODE',true);

require('error.php');
require('settings.php');
require('mail.php');
require('db.php');
require('beep.php');

if( !isset($_GET['runCheck']) )
{
	// We're not running a check; just output the recent logs
	require('log.php');
}
else 
{
	// Before running the check see if this process needs to send an e-mail to indicate that the process is still running
	// (since a long silence could otherwise be due either to success or due to the script breaking)
	$timeLastNonSuccess = $db->querySingle("SELECT MAX(Timestamp) FROM Log WHERE NOT EventType='Success'");
	if( $timeLastNonSuccess < (time()-$settings['Email frequency']) )
	{
		// The last non-success happened longer ago than the minimum e-mail frequency
		
		// Get the number of successes since that threshold:
		$successCount = $db->querySingle("SELECT COUNT(*) FROM Log WHERE EventType = 'Success' AND Timestamp > ".$timeLastNonSuccess);
	
		// It has been the minimum e-mail frequency since the last non-success event; send an e-mail.
		log_event('AllIsWellEmail', 'Sent an e-mail confirming that all has been well with '.$successCount.' successful checks since the minimum e-mail frequency period.');
		
		send_email(
			"all-clear",
			"Since ".date(DATE_RFC822, time()-$settings['Email frequency'])." there have been ".$successCount." successful website tests."
		);
	}
	
	
	define('EMAIL_ERRORS',true); // This indicates to the error handler that any errors which occur from this point on need to be reported
	// Exceptions thrown will be caught by the universal error handler, and the above definition will tell it to send an e-mail about it using the 
	
	// Get the page being tested:
	$ch = curl_init($settings['URL']);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); // Return the results, do not save to a file
	$page = curl_exec($ch);
	if( !$page || curl_errno($ch) != 0 ) // cURL error
		throw new Exception('cURL error #'.curl_errno($ch).' when fetching page: '.curl_error($ch));
	curl_close($ch);
	
	// Find the status code within the page:
	$matches = array();
	//<p style="font-size:6pt">Status code: pSxqXCuhhq (normal)</p>
	if( !preg_match($settings['Status pattern'], $page, $matches) )
		throw new Exception("Could not parse status code");
		
	$code = $matches[1];
	$code_name = $matches[2];
		
	// Check the status code:
	if( $code != $settings['Normal code'] )
		throw new Exception("Abnormal status code: ".$code." (".$code_name.")");
	
	print 'All is well';
	log_event('Success', 'All is well');
}
