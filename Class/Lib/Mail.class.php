<?php
class Mail
{
	/*
	list of To addresses
	@var	array
	*/
	var $sendto = array();
	/*
	@var	array
	*/
	var $acc = array();
	/*
	@var	array
	*/
	var $abcc = array();
	/*
	paths of attached files
	@var array
	*/
	var $aattach = array();
	/*
	list of message headers
	@var array
	*/
	var $xheaders = array();
	/*
	message priorities referential
	@var array
	*/
	var $priorities = array( '1 (Highest)', '2 (High)', '3 (Normal)', '4 (Low)', '5 (Lowest)' );
	/*
	character set of message
	@var string
	*/
	var $charset = "UTF-8";
	var $ctencoding = "8bit";
	var $receipt = 0;
	

/*

	Mail contructor
	
*/

function Mail()
{
	$this->autoCheck( true );
	$this->boundary= "--" . md5( uniqid("myboundary") );
}


/*		

activate or desactivate the email addresses validator
ex: autoCheck( true ) turn the validator on
by default autoCheck feature is on

@param boolean	$bool set to true to turn on the auto validation
@access public
*/
function autoCheck( $bool )
{
	if( $bool )
		$this->checkAddress = true;
	else
		$this->checkAddress = false;
}


/*

Define the subject line of the email
@param string $subject any monoline string

*/
function Subject( $subject )
{
	$this->xheaders['Subject'] = (strtr( $subject, "\r\n" , "  " ));
}


/*

set the sender of the mail
@param string $from should be an email address

*/
 
function From( $from )
{

	if( ! is_string($from) ) {
		echo "Class Mail: error, From is not a string";
		//exit;
	}
	$this->xheaders['From'] = trim($from);
}

/*
 set the Reply-to header 
 @param string $email should be an email address

*/ 
function ReplyTo( $address )
{

	if( ! is_string($address) ) 
		return false;
	
	$this->xheaders["Reply-To"] = trim($address);
		
}


/*
add a receipt to the mail ie.  a confirmation is returned to the "From" address (or "ReplyTo" if defined) 
when the receiver opens the message.

@warning this functionality is *not* a standard, thus only some mail clients are compliants.

*/
 
function Receipt()
{
	$this->receipt = 1;
}


/*
set the mail recipient
@param string $to email address, accept both a single address or an array of addresses

*/

function To( $to )
{

	// TODO : test validit� sur to
	if( is_array( $to ) )
		$this->sendto= trim($to);
	else 
		$this->sendto[] = trim($to);

	if( $this->checkAddress == true )
		$this->CheckAdresses( $this->sendto );

}


/*		Cc()
 *		set the CC headers ( carbon copy )
 *		$cc : email address(es), accept both array and string
 */

function Cc( $cc )
{
	if( is_array($cc) )
		$this->acc= trim($cc);
	else 
		$this->acc[]= trim($cc);
		
	if( $this->checkAddress == true )
		$this->CheckAdresses( $this->acc );
	
}



/*		Bcc()
 *		set the Bcc headers ( blank carbon copy ). 
 *		$bcc : email address(es), accept both array and string
 */

function Bcc( $bcc )
{
	if( is_array($bcc) ) {
		$this->abcc = trim($bcc);
	} else {
		$this->abcc[]= trim($bcc);
	}

	if( $this->checkAddress == true )
		$this->CheckAdresses( $this->abcc );
}


/*		Body( text [, charset] )
 *		set the body (message) of the mail
 *		define the charset if the message contains extended characters (accents)
 *		default to us-ascii
 *		$mail->Body( "m�l en fran�ais avec des accents", "iso-8859-1" );
 */
function Body($body)
{
	$this->body = $body;
	
	if( $this->charset != "" ) {
		$this->charset = strtolower($this->charset);
		if( $this->charset != "iso-8859-1" )
			$this->ctencoding = "8bit";
	}
}


/*		Organization( $org )
 *		set the Organization header
 */
 
function Organization( $org )
{
	if( trim( $org != "" )  )
		$this->xheaders['Organization'] = $org;
}


/*		Priority( $priority )
 *		set the mail priority 
 *		$priority : integer taken between 1 (highest) and 5 ( lowest )
 *		ex: $mail->Priority(1) ; => Highest
 */
 
function Priority( $priority )
{
	if( ! intval( $priority ) )
		return false;
		
	if( ! isset( $this->priorities[$priority-1]) )
		return false;

	$this->xheaders["X-Priority"] = $this->priorities[$priority-1];
	
	return true;
	
}


/*	
 Attach a file to the mail
 
 @param string $filename : path of the file to attach
 @param string $filetype : MIME-type of the file. default to 'application/x-unknown-content-type'
 @param string $disposition : instruct the Mailclient to display the file if possible ("inline") or always as a link ("attachment") possible values are "inline", "attachment"
 */

function Attach( $filename, $nomclair="", $filetype = "", $disposition = "attachment" )
{
	// TODO : si filetype="", alors chercher dans un tablo de MT connus / extension du fichier
	if( $filetype == "" ) $filetype = "application/x-unknown-content-type";
	if( $nomclair == "" ) $nomclair= $filename;
		
	$this->aattach[] = $filename;
	$this->anomclair[] = $nomclair;
	$this->actype[] = $filetype;
	$this->adispo[] = $disposition;
}

/*

Build the email message

@access protected

*/
function BuildMail()
{

	// build the headers
	$this->headers = "";
//	$this->xheaders['To'] = implode( ", ", $this->sendto );
	
	if( count($this->acc) > 0 )
		$this->xheaders['CC'] = implode( ", ", $this->acc );
	
	if( count($this->abcc) > 0 ) 
		$this->xheaders['BCC'] = implode( ", ", $this->abcc );
	

	if( $this->receipt ) {
		if( isset($this->xheaders["Reply-To"] ) )
			$this->xheaders["Disposition-Notification-To"] = $this->xheaders["Reply-To"];
		else 
			$this->xheaders["Disposition-Notification-To"] = $this->xheaders['From'];
	}
	
	if( $this->charset != "" ) {
		$this->xheaders["Mime-Version"] = "1.0";
		$this->xheaders["Content-Type"] = "text/html; charset=$this->charset";
		$this->xheaders["Content-Transfer-Encoding"] = $this->ctencoding;
	}

	$this->xheaders["X-Mailer"] = "Php/libMailv1.3";
	
	// include attached files
	if( count( $this->aattach ) > 0 ) {
		$this->_build_attachement();
	} else {
		$this->fullBody = $this->body;
	}

	reset($this->xheaders);
	while( list( $hdr,$value ) = each( $this->xheaders )  ) {
		if( $hdr != "Subject" )
			$this->headers .= "$hdr: $value\n";
	}
	

}

/*		
	fornat and send the mail
	@access public
	
*/ 
function Send()
{
	$this->BuildMail();
	$this->strTo = implode( ", ", $this->sendto );
	//echo $this->Get();
	if(is_object($GLOBALS["Systeme"]->CurrentSkin)) {
		$this->fullBody = $GLOBALS["Systeme"]->CurrentSkin->ProcessLang($this->fullBody);
		$this->xheaders['Subject'] = $GLOBALS["Systeme"]->CurrentSkin->ProcessLang($this->xheaders['Subject']);
	}
	// envoie du mail
	$res = mail( $this->strTo, $this->xheaders['Subject'], $this->fullBody, $this->headers );

}



/*
 *		return the whole e-mail , headers + message
 *		can be used for displaying the message in plain text or logging it
 */

function Get()
{
	$this->BuildMail();
	$mail = "To: " . $this->strTo . "\n";
	$mail .= $this->headers . "\n";
	$mail .= $this->fullBody;
	return $mail;
}


/*
	check an email address validity
	@access public
	@param string $address : email address to check
	@return true if email adress is ok
 */
 
function ValidEmail($address)
{
	if( preg_match( "#.*<(.+)>#", $address, $regs ) ) {
		$address = $regs[1];
	}
 	if(preg_match( "#^[^@  ]+@([a-zA-Z0-9\-]+\.)+([a-zA-Z0-9\-]{2}|net|com|gov|mil|org|edu|int|fr)\$#",$address) ) 
 		return true;
 	else
 		return false;
}


/*

	check validity of email addresses 
	@param	array $aad - 
	@return if unvalid, output an error message and exit, this may -should- be customized

 */
 
function CheckAdresses( $aad )
{
	for($i=0;$i< count( $aad); $i++ ) {
		if( ! $this->ValidEmail( trim($aad[$i])) ) {
			return "Class Mail, method Mail : invalid address $aad[$i]";	
		}
	}
}


/*
 check and encode attach file(s) . internal use only
 @access private
*/

function _build_attachement()
{

	$this->xheaders["Content-Type"] = "multipart/mixed;\n boundary=\"$this->boundary\"";

	$this->fullBody = "This is a multi-part message in MIME format.\n--$this->boundary\n";
	$this->fullBody .= "Content-Type: text/html; charset=$this->charset\nContent-Transfer-Encoding: $this->ctencoding\n\n" . $this->body ."\n";
	
	$sep= chr(13) . chr(10);
	
	$ata= array();
	$k=0;
	
	// for each attached file, do...
	for( $i=0; $i < count( $this->aattach); $i++ ) {
		
		$filename = $this->aattach[$i];
		$basename =$this->anomclair[$i];
		$ctype = $this->actype[$i];	// content-type
		$disposition = $this->adispo[$i];
		
		if( ! file_exists( $filename) ) {
			echo "Class Mail, method attach : file $filename can't be found"; exit;
		}
		$subhdr= "--$this->boundary\nContent-type: $ctype;\n name=\"$basename\"\nContent-Transfer-Encoding: base64\nContent-Disposition: $disposition;\n  filename=\"$basename\"\n";
		$ata[$k++] = $subhdr;
		// non encoded line length
		$linesz= filesize( $filename)+1;
		$fp= fopen( $filename, 'r' );
		$ata[$k++] = chunk_split(base64_encode(fread( $fp, $linesz)));
		fclose($fp);
	}
	$this->fullBody .= implode($sep, $ata);
}


} // class Mail

?>