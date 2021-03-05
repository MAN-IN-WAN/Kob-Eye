<?php
include 'Class/Lib/Phpmailer/class.phpmailer.php';
if (!defined('MAIL_TYPE'))define('MAIL_TYPE','mail');
if (!defined('MAIL_FROM'))define('MAIL_FROM','');
if (!defined('MAIL_SERVER'))define('MAIL_SERVER','');
if (!defined('MAIL_PORT'))define('MAIL_PORT','');
if (!defined('MAIL_USER'))define('MAIL_USER','');
if (!defined('MAIL_PASS'))define('MAIL_PASS','');
class Mail extends PHPMailer{
    public $From              = MAIL_FROM;
    public $Sender            = MAIL_FROM;
    public $Mailer            = MAIL_TYPE;
    public $Host          = MAIL_SERVER;
    public $Port          = MAIL_PORT;
    public $Username      = MAIL_USER;
    public $Password      = MAIL_PASS;
    public $SMTPDebug     = false;
	public $SMTPSecure	  = '';
	public $SMTPAuth	  = false;
	protected $exceptions = true;

    public function __construct($exceptions = null)
    {
       parent::__construct($exceptions);
       $this->CharSet = 'utf-8';
    }

    function Mail(){
		parent::__construct(false);
	}
	function Subject( $subject ){
		$this->Subject = $subject;
	}
	function From( $from ){
		$this->SetFrom($from, '');
	}
	function ReplyTo( $address ){
		$this->AddReplyTo($address);
	}
	function To( $to ){
		$this->AddAddress($to);
	}
	function Cc( $cc ){
		$this->AddCC($cc);
	}
	function Bcc( $bcc ){
		$this->addBCC($bcc);
	}
	function Body($body){
        $this->isHTML(true);
		$this->Body = $body;
        $this->AltBody = strip_tags($body);
	}
	function Attach( $filename, $nomclair="", $filetype = "", $disposition = "attachment" ){
		$this->AddAttachment($filename,$nomclair);
	}
	function EmbeddedImage($filename, $cid) {
		$this->AddEmbeddedImage($filename, $cid);
	}
    function BuildMail(){}
    function Priority(){}
	
}
?>