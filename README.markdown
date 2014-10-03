NOTE:<br>

1. This code is not unit tested hence not production ready, use it at your own risk. (<em>If you can write unit test cases, let me know we can still improve this code and make it production ready</em>)
2. <strong>It has some interesting features which are still lacking in latest email libraries</strong>
3. You can still use it for learning purpose

<h1>OOMail - Next Generation Email library for PHP</h1>
Ab object oriented approach to mails in PHP. It gives you granular level control for sending an email. You can even reply to a recieved email using this library.
<h2>Features</h2>
1. Basic features which are needed in an email client like adding To, CC, BCC etc.
2. You can reply to a recieved email (You will need FileHadle of received email file or RAW email)
3. For multiple TO address Send Separately with different message-id or same message-id OR Do not send separetly 
4. Security from header injection attack
5. Add a remote file as attachment using url of the file
6. Return-Path support (If delivery is failed which email should be notified)
7. Automatic MIME type detection of attachment
8. Automatic size detection of remote file before it is attached. If there is an attchement limit, oversized files wont be downloaded.

<h2>Usage</h2>
<pre>
// Adding from header along with name
$obj->from(array('Your name'=> yourname@example.com));
// Adding from header just email
$obj->from('yourname@example.com');
// In Reply to (When replying to a mail) when you know message it
$obj->inReplyTo('Id-of-old-message');
// In Reply to (When replying to a mail) when you have message saved
$fd = fopen('old-mail.eml', 'r');
$obj->inReplyTo($fd);
// In Reply to (When replying to a mail) when you have message content
$obj->inReplyTo('content of the mail'); // Will work only if message cotains proper headers
// To > Just email
$obj->to('yourname@example.com');
// To > Name and email
$obj->to(array('Your name' => 'yourname@example.com'));
// To > Multiple recepient > Name and email
$obj->to(array('Your name 1' => 'yourname1@example.com', 'Your name 2' => 'yourname2@example.com'));
// To > Multiple recepient > email
$obj->to(array('yourname1@example.com', 'yourname2@example.com'));
// Send separately to each TO-Recepient
$obj->sendSeparetely(TRUE);
// Do not Send separately to each TO-Recepient
$obj->sendSeparetely(FALSE);
// Send separately to each TO-Recepient & Regenerate message-id for each email
$obj->sendSeparetely(TRUE, TRUE);
// Send separately to each TO-Recepient & Keep same message-id for each email
$obj->sendSeparetely(TRUE, FALSE);
// Subject
$obj->subject('Your subject');
// CC > Just email
$obj->cc('yourname@example.com');
// CC > Name and email
$obj->cc(array('Your name' => 'yourname@example.com'));
// CC > Multiple recepient > Name and email
$obj->cc(array('Your name 1' => 'yourname1@example.com', 'Your name 2' => 'yourname2@example.com'));
// CC > Multiple recepient > email
$obj->cc(array('yourname1@example.com', 'yourname2@example.com'));
// BCC > Just email
$obj->bcc('yourname@example.com');
// BCC > Name and email
$obj->bcc(array('Your name' => 'yourname@example.com'));
// BCC > Multiple recepient > Name and email
$obj->bcc(array('Your name 1' => 'yourname1@example.com', 'Your name 2' => 'yourname2@example.com'));
// BCC > Multiple recepient > email
$obj->bcc(array('yourname1@example.com', 'yourname2@example.com'));
// Reply to > Just email
$obj->replyTo('yourname@example.com');
// Reply To > Name and email
$obj->replyTo('Your name', 'yourname@example.com');

// TODO: Complete DOCUMENTATION

</pre>
