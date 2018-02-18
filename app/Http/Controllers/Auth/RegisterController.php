<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\RelatedEmail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use App\Jobs\SendEmail;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        $user = User::create([
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
		
		$token = str_random(10).sha1(date(time()));

		$relatedEmail = new RelatedEmail([
			'email' => $data['email'],
            'token' => $token,
			'as_login' => 1,
			'attempts' => 1,
			'updated_utc' => time()
        ]);
		
		$user->related_emails()->save($relatedEmail);
		
		dispatch(new SendEmail("user",$user,$token,$data['email']));
		
		return $user;
    }
	
	public function verify_user($user_id,$token)
    {
        $relatedEmail = RelatedEmail::where([['user_id',$user_id],['token', $token]])->first();
        if(isset($relatedEmail) ){
            $user = $relatedEmail->user;
            if(!$user->activated) {
				$relatedEmail->activated = 1;
				$relatedEmail->save();
				
                $relatedEmail->user->activated = 1;
                $relatedEmail->user->save();
				
                $status = "Your e-mail is verified. You can now login.";
            }else{
                $status = "Your e-mail is already verified. You can now login.";
            }
        }else{
            return redirect('/login')->with('warning', "Sorry your email cannot be identified.");
        }
 
        return redirect('/login')->with('status', $status);
    }
	
	protected function registered(Request $request, $user)
    {
		$relatedEmail = $user->related_emails()->where('as_login', 1)->first();
		
		$request->session()->flash('id_email', $relatedEmail->id);
		$request->session()->flash('email', $user->email);
		$request->session()->flash('att_used', 1);
		$request->session()->flash('updated_utc', time());
		
        $this->guard()->logout();
		
        return redirect('/login')->with('status', 'We sent you an activation code. Check your email and click on the link to verify.');
    }
}
