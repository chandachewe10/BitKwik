<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailSubscriptionMail;
use App\Mail\ContactUsMail;
use Filament\Notifications\Notification;

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

return Notification::make()
            ->success()
            ->title('Message Sent')
            ->body('Your message has been sent successfully');
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

         return Notification::make()
            ->success()
            ->title('Subscribed')
            ->body('You have subscribed to our monthly email notifications on Bitcoin');


    }
}
