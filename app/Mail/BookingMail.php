<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Service\TicketPdfGenerator;

class BookingMail extends Mailable
{
    use Queueable, SerializesModels;

    public $mail;
    
    public $action_url;
    public $subject;
    public $event_start_date;
    public $event_end_date;
    public $tickets;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mail)
    {
        $this->mail             = $mail;
        $this->action_url       = $this->mail['action_url'];
        $this->subject          = $this->mail['mail_subject'];
        $this->event_start_date = $this->mail['event_start_date'];
        $this->event_end_date   = $this->mail['event_end_date'];
        $this->tickets          = $this->mail['tickets'];
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        /* Generate Tickets to attach to email */
        // init TicketPdfGenerator
        $this->pdf_generator    = new TicketPdfGenerator;
        
        // generate ticket pdf files
        $files                  = [];

        foreach($this->mail['mail_data'] as $key => $value)
            $files[]            = $this->pdf_generator->generateTicketPdf($value['id'], $value['order_number']);
            
        $mail = $this->from(setting('mail.mail_sender_email'), setting('mail.mail_sender_name'))->view('email_templates.booking');

        
        $files[count($files)] = $this->mail['invoices'];

        // attach tickets PDF
        if(!empty($files)) 
            foreach($files as $file)
                $mail->attach($file);
            
        return $mail;

    }
}
