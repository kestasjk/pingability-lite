<?php

/*
 * The error handling code. This needs to ensure that errors are trapped and logged whenever possible, so there are no silent failures.
 */
if( !defined('IN_CODE') ) die();

set_error_handler('error_handler');
set_exception_handler('exception_handler');
error_reporting(E_STRICT | E_ALL | E_NOTICE);

function exception_handler(Exception $exception)
{
	$file = $exception->getFile();
	$trace = $exception->getTraceAsString();
	$line = $exception->getLine();

	trigger_error('A software exception was not caught: "'.$exception->getMessage().'"');
}
function error_handler($errno, $errstr, $errfile=false, $errline=false, $errcontext=false)
{
	$errorText = "#".$errno.", "
	.$errstr.", "
	.($errfile?"File: ".$errfile.", ":"")
	.($errline?"Line: ".$errline.", ":"");

	// Protect against recursive loops of errors while dealing with errors
	if ( defined('ERROR') )
		die("Error occurred while handling another error: ".$errorText);
	else
		define('ERROR',true);

	// If this error occurred while loading things up / outputting the logs etc then it isn't a 
	// concern; it can be logged for later. However if it occurs after EMAIL_ERRORS has been 
	// set that indicates the error happened during the site check, and so the error has to be 
	// e-mailed as an alert.
	
	if( defined('EMAIL_ERRORS') ) 
	{
		// This error occurred while checking the site
		print 'Exception occurred: '.$errorText.'.. Sending e-mail alert';
		log_event('Exception', $errorText);
		
		send_email(
			"alert",
			"An exception occurred while pingability-lite ran its routine check: ".$errorText
		);
	}
	else 
	{
		// This error occurred during non-critical code, i.e. it doesn't point to a site failure
		print "Error occurred: ".$errorText;
	
		log_event('Error',$errorText);
	}
}