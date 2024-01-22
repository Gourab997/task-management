<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ForgetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($token,$id)
    {
         $this->token = $token;
         $this->id = $id;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->from(env('MAIL_USERNAME'), env('MAIL_FROM_NAME')) // You can pass name as second parameter
            ->subject('Forget Password Mail')
            ->view('emails.forget_password')
            ->with('token',$this->token)
            ->with('id',$this->id)
            ->text('emails.forget_password_plain'); // Plain text version of the emails
    }
}
