<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
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
		$verifyEmail = User::find(Auth::id())->related_emails()->where('token', $token)->first();
		
        if(isset($verifyEmail) ) {
            if(!$verifyEmail->activated) 
			{
				$verifyEmail->activated = 1;
				$verifyEmail->save();
				
                $status = "Your e-mail ". $verifyEmail->email ." is verified.";
            }
			else {
                $status = "Your e-mail ". $verifyEmail->email ." is already verified.";
            }
        }
		else {
			return redirect('/profile')->with('warning', 'Sorry your email cannot be identified.');		
        }
		
    	return redirect('/profile')->with('status', $status);		
    }
}
