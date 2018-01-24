<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RelatedEmails;
use Auth;

class UserController extends Controller
{
    //
	
	public function __construct()
	{
		$this->middleware('auth');
	}
	
	public function profile()
	{
    	return view('profile', array('user' => Auth::user()));		
    }
	
	public function verify_email($token)
	{
		$verifyEmail = RelatedEmails::where('token', $token)->first();
		
        if(isset($verifyEmail) ) {
            if(!$verifyEmail->activated) 
			{
				$verifyEmail->activated = 1;
				$verifyEmail->save();
				
                $status = "Your e-mail is verified.";
            }
			else {
                $status = "Your e-mail is already verified.";
            }
        }
		else {
			return redirect('/profile')->with('warning', 'Sorry your email cannot be identified.');		
        }
		
    	return redirect('/profile')->with('status', $status);		
    }
}
