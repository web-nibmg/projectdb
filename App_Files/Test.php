<?php
/*	require_once "../../lib/php/Mail.php";

	$from = "Dibyendu Roy Chowdhury <drc1@nibmg.ac.in>";
	$to = "Dibyendu Roy Chowdhury <drc1@nibmg.ac.in>";
	$subject = "Test Mail!";
	$body = "Hi,\n\nThis is a test mail..?";
	
	$host = "mail.nibmg.ac.in";
	$username = "drc1@nibmg.ac.in";
	$password = "Satarupa@123";
	
	$headers = array ('From' => $from,
	  'To' => $to,
	  'Subject' => $subject);
	$smtp = Mail::factory('smtp', array(
		'host' => $host,
		'auth' => true,
		'username' => $username,
		'password' => $password));
	
	$mail = $smtp->send($to, $headers, $body);
	
	if(PEAR::isError($mail)){
		echo("<p>" . $mail->getMessage() . "</p>");
	}
	else{
		echo("<p>Message successfully sent!</p>");
	}
*/
	//=================================

/*	function SendMail($ToEmail, $MessageHTML, $MessageTEXT){
		//require_once('../../lib/php/class.phpmailer.php'); // Add the path as appropriate
		$Mail = new PHPMailer();
		$Mail->IsSMTP(); // Use SMTP
		$Mail->Host        = "smtp.gmail.com"; // Sets SMTP server
		$Mail->SMTPDebug   = 2; // 2 to enable SMTP debug information
		$Mail->SMTPAuth    = TRUE; // enable SMTP authentication
		$Mail->SMTPSecure  = "tls"; //Secure conection
		$Mail->Port        = 587; // set the SMTP port
		$Mail->Username    = 'drcforyou.nibmg@gmail.com'; // SMTP account username
		$Mail->Password    = '"Sharmili"'; // SMTP account password
		$Mail->Priority    = 1; // Highest priority - Email priority (1 = High, 3 = Normal, 5 = low)
		$Mail->CharSet     = 'UTF-8';
		$Mail->Encoding    = '8bit';
		$Mail->Subject     = 'Test Email Using Gmail';
		$Mail->ContentType = 'text/html; charset=utf-8\r\n';
		$Mail->From        = 'drcforyou.nibmg@gmail.com';
		$Mail->FromName    = 'GMail Test';
		$Mail->WordWrap    = 900; // RFC 2822 Compliant for Max 998 characters per line

		$Mail->AddAddress($ToEmail); // To:
		$Mail->isHTML(TRUE);
		$Mail->Body = $MessageHTML;
		$Mail->AltBody = $MessageTEXT;
		$Mail->Send();
		$Mail->SmtpClose();

		if($Mail->IsError()){ // ADDED - This error checking was missing
			return FALSE;
		}
		else{
			return TRUE;
		}
	}

	$ToEmail = 'drcforyou.nibmg@gmail.com';
	$ToName  = 'Dibyendu';
	$MessageHTML = "This is a test email...";
	$MessageTEXT = "This is a test email...";

	$Send = SendMail($ToEmail, $MessageHTML, $MessageTEXT);
	if($Send){
		echo "<h2> Sent OK</h2>";
	}
	else{
		echo "<h2> ERROR</h2>";
	}

	die;*/

	//=================================

	require_once '/vendor/swiftmailer/swiftmailer/lib/swift_required.php';

	// Create the Transport
	$transport = Swift_SmtpTransport::newInstance()
		->setHost('smtp.gmail.com')
		->setPort(465)
		->setEncryption('ssl')
		->setUsername('drcforyou.nibmg@gmail.com')
		->setPassword('"Sharmili"')
	;

	// Create the Mailer using your created Transport
	$mailer = Swift_Mailer::newInstance($transport);
	
	// Create a message
	$message = Swift_Message::newInstance('Wonderful Subject')
		->setFrom(array('drcforyou.nibmg@gmail.com'=>'Dibyendu'))
		->setTo(array('drc1@nibmg.ac.in'=>'Dibyendu Roy Chowdhury'))
		->setSubject('Test email...')
		->setBody('This is a test email...')
	;
	
	// Send the message
	$result = $mailer->send($message);
	if($result != 1){
		echo "\nNot 1";
	}
	else{
		echo $result;
	}

?>