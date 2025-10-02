<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailSubscriptionMail;
use App\Mail\ContactUsMail;
use Filament\Notifications\Notification;
use RealRashid\SweetAlert\Facades\Alert;


class EmailSubscriptionAndContactUsController extends Controller
{
    /**
     * Handle email subscription form
     */
    public function emailSubscription(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        // Send notification to admin
        Mail::to('bitkwik@macroit.org')->send(new EmailSubscriptionMail($request->email));
Alert::success('Subscription', 'Your email has been subscribed successfully to our monthly email notifications on Bitcoin');
return view('welcome');
    
    }

    /**
     * Handle contact us form
     */
    public function contactUs(Request $request)
    {
        
        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string'
        ]);

        // Send contact form details to admin
        Mail::to('bitkwik@macroit.org')->send(
            new ContactUsMail(
                $request->name,
                $request->email,
                $request->subject,
                $request->message
            )
        );

        Alert::success('Message Sent', 'Your message has been sent successfully. We will get back to you soon.');
        return view('welcome');


    }
}
