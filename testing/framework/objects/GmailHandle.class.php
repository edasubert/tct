<?php
if ( ! defined( 'PCAFW' ) ) 
	{
		echo 'This file can only be called via the main index.php file, and not directly';
		exit();
	}
	
	
require_once( "api/PHPMailer/class.phpmailer.php" );

class GmailHandle
{
	private $registry;
	
	private $mail;
	private $username;
	private $password;
	
	public function __construct( &$instance )
	{
		$this->registry = $instance;
		$this->mail = new PHPmailer();
	}
	
	public function setUser( $username, $password )
	{
		$this->username = $username;
		$this->password = $password;
	}
	// SEND ============================================================
	public function send( $to, $subject, $message, $from, $fromName = "")
	{
		$this->mail->CharSet = "UTF-8";
		$this->mail->IsSMTP();
		
		$this->mail->SMTPAuth = true;
		$this->mail->SMTPSecure = "tls";
		$this->mail->Host = "smtp.gmail.com";
		$this->mail->Port = 587;
		
		$this->mail->Username = $this->username;
		$this->mail->Password = $this->password;
		
		$this->mail->SetFrom( $from, $fromName );
		$this->mail->Subject = $subject;
		
		$this->mail->MsgHTML( $message );
		$this->mail->AddAddress( $to, "" );
		
		if ( !$this->mail->Send() )
		{
			echo "Mailer Error: " . $this->mail->ErrorInfo;
		}
	}
	// RECEIVE =========================================================
	public function get()
	{
		$hostname = '{imap.gmail.com:993/imap/ssl}INBOX';
		
		$inbox = imap_open($hostname,$this->username,$this->password) or die('Cannot connect to Gmail: ' . imap_last_error());
		
		$emails = imap_search($inbox,'ALL');

		if ($emails) 
		{
			
			rsort($emails);
			
			$output = array();
			
			foreach($emails as $email_number) 
			{	
				$overview = imap_fetch_overview($inbox,$email_number,0);
				
				if ( !$overview[0]->seen ) 
				{	
					$text = imap_fetchbody($inbox,$email_number,2);
					
					$match = explode("ID:", $text );
					
					$hash =  trim( explode( "\n", $match[count($match)-1] )[0] );
						
					$lines = array_map( "strip_tags", explode( "<br>", nl2br( quoted_printable_decode( $text ), FALSE ) ) );
					
					$o = 0;
					while ( strlen( $lines[$o] ) == 0 )
					{
						++$o;
					}
					
					$mail = imap_rfc822_parse_adrlist( $overview[0]->from, "gmail.com" );
					$mail = $mail[0]->mailbox."@".$mail[0]->host;
					
					array_push( $output, array( "hash" => $hash, "text" => $lines[$o], "mail" => $mail ) );
					
				}
				
			}
			return $output;
			
		} 
		return FALSE;
		/* close the connection */
		imap_close($inbox);
		
	}
}

?>
