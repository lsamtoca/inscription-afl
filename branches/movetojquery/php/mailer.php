<?php

// Remplace quoted_printable_encode, 
// disponible pour php > = 5.3 seulement
// Ce code telechargé de
// http://php.net/manual/fr/function.quoted-printable-encode.php

function my_quoted_printable_encode($input, $line_max = 75, $subject) {
         /**    The Quoted Printable encodes only with 75 characters per ligne.
        *    For encoding the subject of message, we must split the subject every 58 characters,
        *    because of adding =?iso-8859-1?Q? at the beginning and ?= at the end of each split.

            *    @param    string        $input            string to encode
            *    @param    int        $line_max            max number of char per ligne
            *    @param    string        $subject            specify if we encode a subject or any other string

            *    @access    public
            */
        $hex = array('0','1','2','3','4','5','6','7',
                            '8','9','A','B','C','D','E','F');
        $lines = preg_split("/(?:\r\n|\r|\n)/", $input);
        $linebreak = "\r\n";
        /* the linebreak also counts as characters in the mime_qp_long_line
        * rule of spam-assassin */
        $line_max = $line_max - strlen($linebreak);
        $escape = "=";
        $output = "";
        $cur_conv_line = "";
        $length = 0;
        $whitespace_pos = 0;
        $addtl_chars = 0;

        // iterate lines
        for ($j=0; $j<count($lines); $j++) {
            $line = $lines[$j];
            $linlen = strlen($line);

            // iterate chars
            for ($i = 0; $i < $linlen; $i++) {
                $c = substr($line, $i, 1);
                $dec = ord($c);

                $length++;

                if ($dec == 32) {
                    // space occurring at end of line, need to encode
                    if (($i == ($linlen - 1))) {
                        $c = "=20";
                        $length += 2;
                    }

                    $addtl_chars = 0;
                    $whitespace_pos = $i;
                } else if ( ($dec == 61) || ($dec < 32 ) || ($dec > 126) ) {
                    $h2 = floor($dec/16); $h1 = floor($dec%16);
                    $c = $escape . $hex["$h2"] . $hex["$h1"];
                    $length += 2;
                    $addtl_chars += 2;
                }

                // length for wordwrap exceeded, get a newline into the text
                if ($length >= $line_max) {
                    $cur_conv_line .= $c;

                    // read only up to the whitespace for the current line
                    $whitesp_diff = $i - $whitespace_pos + $addtl_chars;

                    /* the text after the whitespace will have to be read
                    * again ( + any additional characters that came into
                    * existence as a result of the encoding process after the whitespace)
                    *
                    * Also, do not start at 0, if there was *no* whitespace in
                    * the whole line */
                    if (($i + $addtl_chars) > $whitesp_diff) {
                        if ($subject == "subject") {
                            $output .= "=?ISO-8859-1?Q?".substr($cur_conv_line, 0,
                                    (strlen($cur_conv_line) - $whitesp_diff))."?=";
                        } else {
                            $output .= substr($cur_conv_line, 0,
                                    (strlen($cur_conv_line) - $whitesp_diff)).$linebreak;
                        }
                        $i = $i - $whitesp_diff + $addtl_chars;
                    } else {
                    /* emit continuation --mirabilos */
                        if ($subject == "subject") {
                            $output .= "=?ISO-8859-1?Q?".$cur_conv_line."?=";
                        } else {
                            $output .= $cur_conv_line. '=' . $linebreak;
                        }
                    }

                    $cur_conv_line = "";
                    $length = 0;
                    $whitespace_pos = 0;
                } else {
                // length for wordwrap not reached, continue reading
                    $cur_conv_line .= $c;
                }
            } // end of for

            $length = 0;
            $whitespace_pos = 0;
            if ($subject == "subject") {
                $output .= "=?ISO-8859-1?Q?".$cur_conv_line."?=";
            } else {
                $output .= $cur_conv_line;
                if ($j<=count($lines)-1) {
                    $output .= $linebreak;
                }
            }
            $cur_conv_line = "";

            } // end for

        return trim($output);
    } // end my_quoted_printable_encode 


function subject_encode_from_utf8($subject){
  return '=?UTF-8?B?'.base64_encode($subject).'?=';
}

function send_mail_text($sender,$to,$subject,$message,$cc='',$bcc=''){
    
    $ME = "inscriptions-afl@regateslaser.info";	

    // Composer les arguments de la fonction mail the 
    $TO = $to; 
	$SUBJECT = subject_encode_from_utf8($subject);
	$MESSAGE=$message;
	
	$headers  = "From: $sender\r\n" ;
    $headers .= "Reply-To: $sender\r\n";

    if($cc=="") 
	 $cc=$ME;
	else 
	 $cc =$ME.','.$cc;
    if($cc != "")
	   $headers .= "CC: $cc\r\n" ;

	if($bcc != "")
	 $headers .= "BCC: $bcc\r\n" ;

    $headers .= 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-Type: text/plain; charset="UTF-8"' . "\r\n";
	$headers .= 'X-Mailer: PHP/' . phpversion();

    return mail($TO, $SUBJECT, $MESSAGE, $headers);

}

function send_mail_text_attachement($sender,$to,$subject,$mesg,$cc='',$bcc=''){
    
    $ME = "inscriptions-afl@regateslaser.info";
//	$CC="inscriptions-afl@regateslaser.info";
	
	if($cc=="") 
	 $cc=$ME;
	else 
	 $cc .=','.$ME;

    $SUBJECT = subject_encode_from_utf8($subject);

	$headers  = "From: $sender\r\n" ;
    $headers .= "Reply-To: $sender\r\n";
    if($cc != "")
	 $headers .= "CC: $cc\r\n";
	if($bcc != "")
	 $headers .= "BCC: $bcc\r\n" ;
    $headers .= 'MIME-Version: 1.0' . "\r\n";
    //create a boundary string. It must be unique
    //so we use the MD5 algorithm to generate a random hash
    $random_hash = md5(date('r', time())); 
    $php_mixed="PHP-mixed-$random_hash";
    $php_alt="PHP-alt-$random_hash";
	$headers .= "Content-Type: multipart/mixed; boundary=\"$php_mixed\"" . "\r\n";
	$headers .= "This is a multi-part message in MIME format.\r\n"; 
	$headers .= 'X-Mailer: PHP/' . phpversion();	
    
    //define the body of the message.
    $message='--'.$php_mixed. "\r\n";
    $message.='Content-Type: text/plain; charset="UTF-8"'. "\r\n";
    $message.='Content-Transfer-Encoding: 8bit' . "\r\n\r\n";
    $message.= $mesg. "\r\n". "\r\n"; // Transforms \' to '

    if($_FILES['attachment']['error'] == 0){
      //echo 'piece jointe trouvée';
      $attachment_tmp_name=$_FILES['attachment']['tmp_name'];
      $attachment_name=$_FILES['attachment']['name'];
      $attachment_type=$_FILES['attachment']['type'];
      
      // The attachment
      //read the attachment file contents into a string,
      //encode it with MIME base64,
      //and split it into smaller chunks
      $attachment = chunk_split(base64_encode(file_get_contents($attachment_tmp_name)));

      $message.='--'.$php_mixed . "\r\n";
      $message.="Content-Type: $attachment_type; name=\"$attachment_name\"" . "\r\n";
      $message.='Content-Transfer-Encoding: base64' . "\r\n";
      $message.='Content-Disposition: attachment' . "\r\n"  . "\r\n";
      $message.=$attachment. "\r\n"  . "\r\n";
    }
   
    // Fin
    $message.='--'.$php_mixed.'--'. "\r\n";

    return mail($to, $SUBJECT, $message, $headers);
    
}

?>
