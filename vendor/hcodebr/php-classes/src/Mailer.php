<?php

//Namespace:
namespace Hcode;

use Rain\Tpl;

class Mailer {

	//Atributos:
	private $email;

	//Constantes:
	const USERNAME = '';
	const PASSWORD = '';
	const NAME_FROM = 'Hcode Store';


	//Métodos: 
	 public function __construct($toAddress, $toName, $subject, $tplName,$data = array())
	 {


	 	// config
		$config = array(
			"tpl_dir"  => $_SERVER["DOCUMENT_ROOT"]."/views/email/",
			"cache_dir" =>$_SERVER["DOCUMENT_ROOT"]."/views-cache/",
			"debug"     => false // set to false to improve the speed
			);

		Tpl::configure( $config );

		$tpl = new Tpl;

		foreach ($data as $key => $value) {
			$tpl->assign($key,$value);
		}

		$html = $tpl->draw($tplName, true);

	 	$this->email = new \PHPMailer();

		//Tell PHPMailer to use SMTP
		$this->email->isSMTP();

		//Enable SMTP debugging
		//SMTP::DEBUG_OFF = off (for production use)
		//SMTP::DEBUG_CLIENT = client messages
		//SMTP::DEBUG_SERVER = client and server messages
		$this->email->SMTPDebug = 0;

		//Set the hostname of the mail server
		$this->email->Host = 'smtp.gmail.com';
		//Use `$mail->Host = gethostbyname('smtp.gmail.com');`
		//if your network does not support SMTP over IPv6,
		//though this may cause issues with TLS

		//Set the SMTP port number:
		// - 465 for SMTP with implicit TLS, a.k.a. RFC8314 SMTPS or
		// - 587 for SMTP+STARTTLS
		$this->email->Port = 587;

		//Set the encryption mechanism to use:
		// - SMTPS (implicit TLS on port 465) or
		// - STARTTLS (explicit TLS on port 587)
		$this->email->SMTPSecure = 'tls';

		//Whether to use SMTP authentication
		$this->email->SMTPAuth = true;

		//Username to use for SMTP authentication - use full email address for gmail
		$this->email->Username = Mailer::USERNAME;

		//Password to use for SMTP authentication
		$this->email->Password = Mailer::PASSWORD;

		//Set who the message is to be sent from
		//Note that with gmail you can only use your account address (same as `Username`)
		//or predefined aliases that you have configured within your account.
		//Do not use user-submitted addresses in here
		$this->email->setFrom(Mailer::USERNAME, Mailer::NAME_FROM);

		//Set an alternative reply-to address
		//This is a good place to put user-submitted addresses
		//$this->email->addReplyTo('replyto@example.com', 'First Last');

		//Set who the message is to be sent to
		$this->email->addAddress($toAddress, $toName);

		//Set the subject line
		$this->email->Subject = $subject;

		//Read an HTML message body from an external file, convert referenced images to embedded,
		//convert HTML into a basic plain-text alternative body
		$this->email->msgHTML($html);

		//Replace the plain text body with one created manually
		$this->email->AltBody = 'This is a plain-text message body';

		//Attach an image file
		//$mail->addAttachment('images/phpmailer_mini.png');

		

	 }

	 public function send()
	 {

	 	return $this->email->send();

	 }



}


?>