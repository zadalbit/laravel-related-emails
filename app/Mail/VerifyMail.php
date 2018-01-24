<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class VerifyMail extends Mailable
{
    use Queueable, SerializesModels;
	
	public $verify;
	public $user;
	public $token;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($verify,$user,$token)
    {
		$this->verify = $verify;
		$this->user = $user;
		$this->token = $token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
		if($this->verify == "user")
			return $this->view('emails.verifyUser');
		else
			return $this->view('emails.verifyEmail');
    }
}
