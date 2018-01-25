<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RelatedEmails extends Model
{
   /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
	
	protected $fillable = ['email','user_id','token','as_login','attempts','updated_utc'];
	
	/**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'token', 'user_id',
    ];
	
	/**
    * Get the user that owns the email.
    */
	public function user()
    {
        return $this->belongsTo(User::class);
    }
}
