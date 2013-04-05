<?php
/*
 * This file will cause the computer to beep, if it has been enabled in the settings.
 * 
 * This is useful for if you are asleep, have the web server nearby, and want it to wake you if it detects that the 
 * website is down (this was the main selling point of pingability.com's automated texts/calls; if you're not asleep
 * you check your e-mail fairly regularly, but if you're asleep you need an extra alert).
 * 
 * To get this working on FreeBSD I had to kldload speaker (and add speaker_load="YES" to the /boot/loader.conf), and 
 * then set chgrp www /dev/speaker , and chown g+w /dev/speaker , so that the www account would be able to play sounds
 * on the speaker.
 */
if( !defined('IN_CODE') ) die();

// Do a single beep
function single_beep() {
	global $settings;
	
	if( $settings['CanBeep'] && is_writable("/dev/speaker") )
	{
		$fh=fopen("/dev/speaker", "w");
		fwrite($fh, "f"); // The letter sent changes the tone played
		fclose($fh);
	}
}

// Do a lengthy beep over the given number of seconds, optionally with a delay between beeps.
function beepRepeat($seconds, $delay) {
	
	$startTime = time();
	
	while( time() < $startTime + $seconds)
	{
		single_beep();
		sleep($delay);
	}
}

// The generic beep; the only one which should be run from outside this script
function beep() {
	global $db, $settings;

	// Make sure we aren't beeping too frequently; we should only beep every 
	$latestBeep = $db->querySingle("SELECT MAX(Timestamp) FROM Log WHERE EventType='Beep'");
	if( !is_null($latestBeep) && $latestBeep > time()-$settings['BeepInterval'])
		return; // Too soon to beep again
	
	log_event('Beep', 'Beeped');
	
	beepRepeat(10, 0.5);
}
