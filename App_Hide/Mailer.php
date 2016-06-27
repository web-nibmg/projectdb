<?php

	//Checks whether the user is logged in or not...
	session_start();
	if($_SESSION['id'] == ''){
		header('Location: '.$_SESSION['login'].'?flag=3');
		exit();
	}
	//Login Check ends...

	require_once '/vendor/swiftmailer/swiftmailer/lib/swift_required.php';

	// Create the Transport
	$transport = Swift_SmtpTransport::newInstance()
		->setHost('smtp.gmail.com')
		->setPort(465)
		->setEncryption('ssl')
		->setUsername('<your_mail_id>')
		->setPassword('"<your_password>"')
	;

	// Create the Mailer using your created Transport
	$mailer = Swift_Mailer::newInstance($transport);
	
	// Create a message
	$message = Swift_Message::newInstance('Wonderful Subject')
		->setFrom(array('drcforyou.nibmg@gmail.com'=>'Dibyendu'))
		->setTo(array($email=>$user_name))
		->setSubject($subject)
		->setBody($body)
	;
	
	// Send the message
	$result = $mailer->send($message);

?>
