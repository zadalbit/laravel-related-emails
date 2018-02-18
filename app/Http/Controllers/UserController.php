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
    	return view('user.profile');		
    }
	
	public function verify_email($token)
	{
		$relatedEmail = User::find(Auth::id())->related_emails()->where('token', $token)->first();
		
        if(isset($relatedEmail) ) {
            if(!$relatedEmail->activated) 
			{
				$relatedEmail->activated = 1;
				$relatedEmail->save();
				
                $status = "Your e-mail ". $relatedEmail->email ." is verified.";
            }
			else {
                $status = "Your e-mail ". $relatedEmail->email ." is already verified.";
            }
        }
		else {
			return redirect('/profile')->with('warning', 'Sorry your email cannot be identified.');		
        }
		
    	return redirect('/profile')->with('status', $status);		
    }
}
