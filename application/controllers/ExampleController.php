<?php
/**
 * Controller class that send an e-mail with the iCalendar attachment
 *
 * @author Adler Medrado
 */
class ExampleController extends Zend_Controller_Action
{

	public function indexAction()
	{
		$this->_helper->viewRenderer->setNoRender();
		throw new Exception('You must use the sendAction() instead indexAction() :-)');
	}


	/**
	 * Send the e-mail
	 *
	 * @throws Exception
	 * @author Adler Medrado
	 */
	public function sendAction() 
	{		
		/*
		For testing purposes, i just disable the viewRenderer
		*/
		$this->_helper->viewRenderer->setNoRender();
		
		/**
		 * e-mail sender's info
		 */
		$senderName     = 'Adler Medrado';
		$senderEmail    = 'adlermedrado@gmail.com';
		$senderPassword = 'minhasenhalinda';
		
		/*
		e-mail recipient's info
		*/
		$toName  = 'Adlerzão malvadão';
		$toEmail = 'adler@adlermedrado.com.br';

		/*
		This code is used to config this app to send e-mail using gmail servers. If you don't want to use it you must remove it
		 */
		$smtpHost = 'smtp.gmail.com';
		$smtpConf = array(
		'auth' => 'login',
		'ssl' => 'ssl',
		'port' => '465',
		'username' => $senderEmail,
		'password' => $senderPassword
		);
		$transport = new Zend_Mail_Transport_Smtp($smtpHost, $smtpConf);

		//Create email
		$email = new Zend_Mail('UTF-8');
		$email->setFrom($senderEmail, $senderName);
		$email->addTo($toEmail, $toName);
		$email->setSubject('URGENTE - Confirmação de Evento');
		$email->setBodyText('Favor confirmar este evento importantíssimo');
		$timestamp = date('Ymd').'T'.date('His');
		$uid = md5($timestamp.example).'@exemplo.com.br';
		$dtCreated = date('Ymd').'T'.date('His');
		$dtStart = '20110701T080000';
		$dtEnd   = '20110701T124500';
		
		/*
		iCalendar block - Read carefully and replace sample to data to your own data
		 */
		$ical = <<<ICALENDAR_DATA
BEGIN:VCALENDAR
PRODID:-//Seu sistema//Sua organizacao//EN
VERSION:2.0
CALSCALE:GREGORIAN
METHOD:REQUEST
BEGIN:VEVENT
DTSTART:{$dtStart}
DTEND:{$dtEnd}
DTSTAMP:{$timestamp}
UID:{$uid}
SUMMARY:Sucesso Total
DESCRIPTION:Forrózão hoje. Vamos ralá nossos bucho!
CREATED:{$dtCreated}
LAST-MODIFIED:{$dtCreated}
LOCATION:Forró pé sujo
SEQUENCE:0
STATUS:CONFIRMED
TRANSP:OPAQUE
ORGANIZER:MAILTO:adlermedrado@gmail.com
BEGIN:VALARM
ACTION:DISPLAY
DESCRIPTION:Lembrete do evento
TRIGGER:-P0DT0H10M0S
END:VALARM
END:VEVENT
END:VCALENDAR
ICALENDAR_DATA;

		/*
		To attach the icalendar file we need to create an Zend_Mime_Part object.
		Important: disposition must be DISPOSITION_INLINE to enable the email clients to open it automatically
		*/
		$attach = new Zend_Mime_Part($ical);
		$attach->type = 'text/calendar';
		$attach->disposition = Zend_Mime::DISPOSITION_INLINE;
		$attach->encoding = Zend_Mime::ENCODING_8BIT;
		$attach->filename = 'calendar.ics';

		$email->addAttachment($attach);

		try {
			$email->send($transport);
			Zend_Debug::dump($email);
		}
		catch (Exception $e) {
			Zend_Debug::dump($e);
		}
		
	}
	
}