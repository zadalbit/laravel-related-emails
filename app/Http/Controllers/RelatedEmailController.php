<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\SendEmail;
use App\Models\RelatedEmails;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class RelatedEmailController extends Controller
{
	public function __construct()
    {
        $this->middleware('auth')->except('update');
    }
	
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
		return User::find(Auth::id())->related_emails;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
		$user_id = Auth::id();
		$token = str_random(10).sha1(time());
		
		$info = RelatedEmails::create([
			'user_id' => $user_id,
			'token' => $token,
			'email' => "",
			'updated_utc' => time()
		]);

		return $info->id;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
		//
	}

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
		//
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {	
		$attempts_max = 3;
		$time_sec_max = 180;
		$email = base64_decode($request->email);
		$time = time();
		
		$info = RelatedEmails::findOrFail($id);
			
		if(!empty($info->email))
		{
			if($info->email == $email)
			{
				$updated = $info->updated_utc;
				$diff = $time - $updated;
					
				if($diff > $time_sec_max && $info->attempts < $attempts_max)
				{
					$info->attempts += 1;
					$info->updated_utc = $time;
					$info->save();

					dispatch(new SendEmail('email',$info->user,$info->token,$email));
						
					return 0;
				}
							
				return $diff;
			}
			
			return "Forbidden.";
		}
		else
		{
			$info->attempts += 1;
			$info->email = $email;
			$info->updated_utc = $time;
			$info->save();

			dispatch(new SendEmail('email',$info->user,$info->token,$email));
						
			return 0;
		}
	}

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
		User::find(Auth::id())->related_emails()->findOrFail($id)->delete();
    }
}
