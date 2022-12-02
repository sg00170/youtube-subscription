<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'role',
        'email', 'password', 
        'access_token', 'access_token_expired_at',
        'refresh_token', 'scopes'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 
        'access_token', 'access_token_expired_at',
        'refresh_token', 'scope',
        'created_at', 'updated_at', 'deleted_at'
    ];

    // /**
    //  * The attributes that should be cast to native types.
    //  *
    //  * @var array
    //  */
    // protected $casts = [];

    /**
     * User roles.
     */
    const ROLES = [
        'member' => 0,
        'youtuber' => 1000
    ];

    /**
     * Get the channel for the user.
     */
    public function channel()
    {
        return $this->hasOne(Channel::class);
    }

    /**
     * Get the subscriptions for the user.
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Create the model.
     *
     * @param  array
     * @return \App\Models\User
     */
    public static function create($attributes)
    {
        $model                              = new static;

        $model->role                        = $attributes['role'] ?? User::role('youtuber');

        $model->email                       = $attributes['email'];
        $model->password                    = $attributes['password'];

        $model->access_token                = $attributes['access_token'];
        $model->access_token_expired_at     = $attributes['access_token_expired_at'];
        $model->refresh_token               = $attributes['refresh_token'];
        $model->scopes                      = $attributes['scopes'];

        $model->save();
        
        return $model;
    }

    /**
     * Get the role of user.
     *
     * @param string
     * @return int
     */
    public static function role($name)
    {
        return static::ROLES[$name];
    }
}
