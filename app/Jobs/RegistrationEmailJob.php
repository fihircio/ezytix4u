<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Notifications\MailNotification;
use App\Mail\RegisterMail;
use App\Mail\EditBookingMail;
use App\Mail\EventMail;
use App\Mail\BecomeOrganizerMail;
use App\Mail\ContactMail;
use App\Mail\ReviewMail;
use Mail;

class RegistrationEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
     
    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 120;

    protected $mail;
    protected $extra_lines;
    protected $users;
    protected $type;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($mail = [], $users = null, $type = '')
    {
        $this->mail  = $mail;
        $this->extra_lines  = !empty($mail['extra_lines']) ? $mail['extra_lines'] : [];
        $this->users = $users;
        $this->type = $type;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if(checkMailCreds()) 
        {
            try {
                 
                // if($this->type == 'register')
                //     Mail::to($this->users)->send(new RegisterMail($this->mail));  
                    
                if($this->type == 'edit_booking')
                    Mail::to($this->users)->send(new EditBookingMail($this->mail));  

                if($this->type == 'event')
                    Mail::to($this->users)->send(new EventMail($this->mail));  

                
                if($this->type == 'become_organizer')
                    Mail::to($this->users)->send(new BecomeOrganizerMail($this->mail));  

                
                if($this->type == 'contact')
                    Mail::to($this->users)->send(new ContactMail($this->mail)); 

            
                if($this->type == 'review')
                    Mail::to($this->users)->send(new ReviewMail($this->mail)); 
                             
                \Notification::locale(\App::getLocale())->send($this->users, new MailNotification($this->mail, $this->extra_lines));
            } catch (\Exception $e) {
                
                
                $this->fail($e);
            }
        }
    }
}


