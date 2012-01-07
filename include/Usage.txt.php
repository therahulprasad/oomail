<?php

/**
  I am jotting down how the object will be used then I will start working on implementation.
  This way I will make sure that I wont divert if I get new feature.
  I will add those ideas in this file. Which will be implemented later.
**/

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

?>