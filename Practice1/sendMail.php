<?php

class Mail
{
    public $to, $subject, $message, $from;
    public function __construct(string $to, string $subject, string $message, string $from) {
        $this->to = $to;
        $this->subject = $subject;
        $this->message = $message;
        $this->from = $from;
    }
}

abstract class Sender {
    public static function send(Mail $mail) {
        return mail($mail->to, $mail->subject, $mail->message, $mail->from);
    }
}

$to = "a.o.drahun@student.khai.edu";
$subject = "test";
$message = "Hello world";
$from = "From: pokimosha186@gmail.com";

$mail = new Mail($to, $subject, $message, $from);

Sender::send($mail);