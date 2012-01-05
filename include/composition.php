<?php
// Liner      ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
$i = 47;
$type = "html";
$boundary = '-----=' . md5( uniqid ( rand() ) ); 


$myHeader = "";
$myHeader .= 'MIME-Version: 1.0' . PHP_EOL;
//$myHeader .= "Content-type: text/html; charset=iso-8859-1; boundary=\"$boundary\"" . PHP_EOL;
$myHeader .= "Content-Type: multipart/mixed; boundary=\"$boundary\"" . PHP_EOL;
$myHeader .= "To: Unexpected User <unexpecteduser@unexpectedserver.com>" . PHP_EOL;
$myHeader .= "From: ". "Ankit Prasad" ."<" . "ankit15march@gmail.com" . ">" . PHP_EOL;
$myHeader .= "Reply-To: ankit15march@gmail.com" . PHP_EOL;
//$myHeader .= "Bcc: " . "rahul.pache@gmail.com" . PHP_EOL;
$myHeader .= "Cc: " . "rahul.pache@gmail.com" . PHP_EOL;
//$myHeader .= "X-Priority: 1 (Higuest)\n"; 
//$myHeader .= "X-MSMail-Priority: High\n"; 
//$myHeader .= "Importance: High\n";
//$myHeader .= "Return-Path: return-path@rahulprasad.com" . PHP_EOL;
$myHeader .= "Return-Receipt-To: bounce@rahulprasad.com" . PHP_EOL;
$myHeader .= "X-Mailer: PHP/" . phpversion();

$theFile = "attachment.docx";
$self = file_get_contents("mailHeaders.php");
$preparedSelf = nl2br($self);
$content = file_get_contents($theFile);
$content_encode = chunk_split(base64_encode($content));

$htmlMessage = "";

$htmlMessage .= "--" . $boundary . PHP_EOL;
$htmlMessage .= 'Content-type: text/html; charset=iso-8859-1' . PHP_EOL . PHP_EOL;
//$htmlMessage .= "Content-Disposition: attachment; filename=\"bogus.txt\"" . PHP_EOL . PHP_EOL; 
$htmlMessage .= "
<html>
<head><title>What</title></head>
<body><strong>Return-Receipt-To: bounce@rahulprasad.com, Multipart mail with 3 parts 1st text/html content, 2nd Content type application/msword name=WordFile,  3rd part text/plain (Name = myPart3)+ Border-terminated</strong><br />{$preparedSelf}</body>
</html>
" . PHP_EOL;

$htmlMessage .= "--" . $boundary . PHP_EOL;
$htmlMessage .= "Content-type: application/msword; name=\"WordFile\"" . PHP_EOL;
$htmlMessage .= "Content-Transfer-Encoding: base64" . PHP_EOL . PHP_EOL;
$htmlMessage .= $content_encode . PHP_EOL;
$htmlMessage .= "--" . $boundary . PHP_EOL;
$htmlMessage .= "Content-type: text/plain; charset=us-ascii; name=\"myPart3\"" . PHP_EOL . PHP_EOL;

$htmlMessage .= "This is the 3rd part of the message. Its content type is specified as text/plain and charset as us-ascii" . PHP_EOL . PHP_EOL;

$htmlMessage .= "--" . $boundary . "--" . PHP_EOL;


//$htmlMessage .= "Content-Type: application/msword; name=\"my attachment\"" . PHP_EOL;
//$htmlMessage .= "Content-Type: " . mime_content_type($theFile) . "; name=\"my attachment\"" . PHP_EOL;
//$htmlMessage .= "Content-Transfer-Encoding: base64" . PHP_EOL; 
//$htmlMessage .= "Content-Disposition: attachment; filename=\"bogus.doc\"" . PHP_EOL; 
//$htmlMessage .= $content_encode . PHP_EOL . PHP_EOL;

//$htmlMessage .= "--" . $boundary . PHP_EOL . PHP_EOL;
//$htmlMessage .= "--" . $boundary . PHP_EOL;
//$htmlMessage .= "Content-Disposition: inline;". PHP_EOL. PHP_EOL; 

//$myMessage = wordwrap("Message Body \r\n{$documentation}}\r\n", 70);
echo mail(NULL, "Testing {$i}", $htmlMessage, $myHeader);

/*
Todo
1. Try sending message by adding To: email@address to $message
2. Add Return-Path and Return-Receipt-To to headers
3. Add support for foreign language
4. Correct support for file attachment 
4.1 File can be set by user
4.2 Decide content type on your own (Research) or let them choose
4.3 If text, offer user to choose disposition method as attachement or inline
4.4 Let user choose if they want to attach the written text as a text file.
*/

/*
Problems:
1. Priority
2. Let user decide PHP_EOL, /r/n or /n
3. Let user decide if they wanna use Name <email@address> or Just email@address
4. Let user choose if they wanna use Header to add TO or PHP's 1st parameter, PHP's Parameter may not accept Name <email@address> foormat
5. Let user decide if they want to use word wrap
6. Notice number of EOL in each line
7. Text and HTML format from http://www.php.net/manual/en/function.mail.php#57009
8. Multipart http://www.php.net/manual/en/function.mail.php#74873
9. Big example http://www.php.net/manual/en/function.mail.php#83491
*/
?>