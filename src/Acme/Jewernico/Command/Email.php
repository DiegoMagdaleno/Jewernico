<?php

namespace Acme\Jewernico\Command;

use \PHPMailer\PHPMailer\PHPMailer;

class Email
{
    private string $from;
    private string $to;
    private string $subject;
    private string $content;
    private PHPMailer $mail;
    public function __construct($to, $content, $subject)
    {
        $this->to = $to;
        $this->subject = $subject;
        $this->content = $content;
        $this->mail = new PHPMailer(true);
        $this->mail->Host = "smtp.gmail.com";
        $this->mail->SMTPAuth = true;
        $this->mail->Username = "nicoleflorestorres27@gmail.com";
        $this->mail->Password = "bapy xxuj lroe ysxn";
        $this->mail->SMTPSecure = 'ssl';
        $this->mail->Port = 465;
        $this->mail->setFrom("nicoleflorestorres27@gmail.com");
        $this->mail->addAddress($this->to);
        $this->mail->isHTML(true);
        $this->mail->Subject = $subject;
        $this->mail->Body = $this->content;
    }

    public function send(): void
    {
        $this->mail->send();
    }
}