<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Reviewmail extends Mailable
{
    use Queueable, SerializesModels;

    
    public $mail;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mail)
    {
        $this->mail           =  $mail;
       
    }


    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return  $this->from(setting('mail.mail_sender_email'), setting('mail.mail_sender_name'))
                ->subject($this->mail['mail_subject'])
                ->view('email_templates.review');
    }
}
