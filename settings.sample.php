<?php
/*
 * Configurable settings
 */
if( !defined('IN_CODE') ) die();

global $settings;
$settings = array(
	'URL' => "http://yourwebserver.net/?showstatuscode=on",
	'Status pattern' => '/>Status code: ([^ ]+) \(([^\)]+)\)</', // $matches[1] needs to be the code checked below, and $matches[2] the friendly name
	'Normal code' => 'allisgoodcode', // The code which indicates everything is fine
	'Email frequency' => 24*60*60, // Time to go with no alerts before sending an e-mail to say that checks are still being run, to avoid silent failures
	'CanBeep' => false, // If false this disables any action taken in beep.php for any beep() functions
	'BeepInterval' => 2*60*60, // The minimum length of time between beeps (to avoid annoying people, if the site goes down while away from home)
	'MailerSettings' => array (
		'MailFrom' => 'pingability-lite@youremaildomain.com',
		'MailFromName' => 'Pingability lite',
		'MailTo' => 'you@youraddress.com',
		'MailToName' => 'Your Name',
		"Host"=>"smtphost.com",
		"Port"=>"465",
		"SMTPAuth"=>true,
/* If this is FALSE the two variables below are ignored */
		"Username"=>"smtpuser@smtphost.com",
		"Password"=>"smtppassword",
		'SMTPSecure'=>'ssl'
	)
);

date_default_timezone_set('Australia/Perth');