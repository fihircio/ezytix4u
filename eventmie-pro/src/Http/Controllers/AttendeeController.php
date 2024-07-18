<?php

namespace Classiebit\Eventmie\Http\Controllers;

use App\Http\Controllers\Controller; 
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Classiebit\Eventmie\Models\User;
use Classiebit\Eventmie\Notifications\MailNotification;

class AttendeeController extends Controller
{

    public function __construct()
    {
        $this->middleware('common');
       
        // authenticate except 
        $this->middleware('organiser');
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function add_attendee(Request $request)
    {
        //dd($request->all());
        $request->validate([
            'name'            => 'required|max:512',
            'email'           => 'required|email|unique:users,email',
            'phone'           => 'required|string|max:255',
        ]);

        /*
        if(!empty(setting('apps.twilio_sid')) && !empty(setting('apps.twilio_auth_token')) && !empty(setting('apps.twilio_number')))
        {
            $request->validate([
                'phone'           => 'required|string|max:255',
            ]);        
        }
        */

        $user   = User::create([
                    'name'      => $request->name,
                    'email'     => $request->email,
                    'phone'     => $request->phone,
                    'password'  => Hash::make(\Str::random(8)),
                    'role_id'  => 2,
                ]);

        $user->roles()->attach(2);        

        // Send welcome email
        if(!empty($user->id))
        {
            // ====================== Notification ====================== 
            $mail['mail_subject']   = __('eventmie-pro::em.congrats').' '.__('eventmie-pro::em.register').' '.__('eventmie-pro::em.successful');
            $mail['mail_message']   = __('eventmie-pro::em.get_tickets');
            $mail['action_title']   = __('eventmie-pro::em.login');
            $mail['action_url']     = route('eventmie.login');
            $mail['n_type']         = "user";

            /* Guest password reset notification */
            $msg                    = [];
            $msg[]                  = __('eventmie-pro::em.guest_password_reset');
            $mail['extra_lines']    = $msg;

            // notification for
            $notification_ids       = [
                1, // admin
                $user->id, // new registered user
            ];
            
            $users = User::whereIn('id', $notification_ids)->get();
            
            \App\Jobs\RegistrationEmailJob::dispatch($mail, $users, 'register')->delay(now()->addSeconds(10));
            /* Send email verification link */
            $user->sendEmailVerificationNotification();
            /* Send email verification link */
            // ====================== Notification ======================     
        }
        $user->name = $user->name.' ('.$user->email.')'; 

        $customer_options[] = $user->only(['id', 'name', 'phone', 'email']);
        return response()->json(['status' => true, 'attendee' => $user->only(['id', 'name', 'phone', 'email']), 'customer_options' => $customer_options]);
        
    }
}
