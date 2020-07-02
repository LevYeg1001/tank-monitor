<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewUserRegistered extends Mailable
{
    use Queueable, SerializesModels;

    protected $newUser;

    /**
     * NewUserRegistered constructor.
     * @param $mailData
     */
    public function __construct($mailData)
    {
        $this->newUser = $mailData;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->view('mail.registerNewUser', ['user' => $this->newUser])
            ->subject('Welcome to Tank Monitor');
    }
}
