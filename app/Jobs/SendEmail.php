<?php

namespace App\Jobs;

use Mail;
use App\Mail\VerifyMail;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

	protected $verify;
	protected $user;
	protected $token;
	protected $email;
	
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($verify,$user,$token,$email)
    {
        $this->verify = $verify;
		$this->user = $user;
		$this->token = $token;
		$this->email = $email;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $email = new VerifyMail($this->verify, $this->user, $this->token);
		Mail::to($this->email)->send($email);
    }
}
