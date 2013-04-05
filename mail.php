<?php

/*
 * Set up mail using PHPMailer in the contrib folder, using the provided settings, and set up helper functions
 * to make sending basic e-mails easy.
 */

if( !defined('IN_CODE') ) die();

require_once('contrib/PHPMailer_v5.1/class.phpmailer.php');
// Set up the mailer first, so the system will immidiately fail if it can't even configure the mailer successfully:
global $mail;
$mail = new PHPMailer();
$mail->AddAddress($settings['MailerSettings']['MailTo'], $settings['MailerSettings']['MailToName']);

$mail->SetFrom($settings['MailerSettings']['MailFrom'], $settings['MailerSettings']['MailFromName']);
$mail->AddReplyTo($settings['MailerSettings']['MailFrom'], $settings['MailerSettings']['MailFromName']);
$mail->WordWrap = 50;
$mail->IsHTML(true);

$mail->IsSMTP();

$SMTPSettings = array('Host','Port','SMTPAuth','Username','Password','SMTPSecure');
foreach($SMTPSettings as $SMTPSetting)
{
	if ( isset($settings['MailerSettings'][$SMTPSetting]) )
	$mail->{$SMTPSetting} = $settings['MailerSettings'][$SMTPSetting];
}

function send_email($subject, $message)
{
	global $mail;

	$mail->Subject = "pingability-lite: ".$subject;
	$mail->Body = "Hello ".$settings['MailerSettings']['MailToName'].
			",<br /><br />This is a message relating to the pingability-lite tested URL: ".$settings['URL'].':<br /><br />'.
	$message.
			"<br /><br />Kind regards,<br />".$settings['MailerSettings']['MailFromName'];

	$mail->AltBody =
	preg_replace('/<[^>]*>/','', // Get rid of any other HTML
	preg_replace('/<\/?p>/',"\n\n", // Convert paragraph ends to newlines
	preg_replace('/<br ?\/?>/',"\n", // Convert breaks to newlines
	preg_replace("/\n/","", // Get rid of newlines
	$mail->Body))));

	$mail->Send();

	if ( $mail->IsError() )
		trigger_error("Mailer error: ".$mail->ErrorInfo);
}