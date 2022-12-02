<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscription extends Model
{
    use SoftDeletes;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'channel_id',
        'subscription_id', 'subscribed_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'user_id',
        'channel_id',
        'subscription_id',
        'created_at', 'updated_at', 'deleted_at'
    ];

    /**
     * Get the user that owns the subscription.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the channel that owns the subscription.
     */
    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    /**
     * Create the model.
     *
     * @param  array
     * @return \App\Models\Chennel
     */
    public static function create($attributes)
    {
        $model                          = new static;

        $model->user_id                 = $attributes['user_id'];
        $model->channel_id              = $attributes['channel_id'];

        $model->subscription_id         = $attributes['subscription_id'];
        $model->subscribed_at           = $attributes['subscribed_at'];

        $model->save();
        
        return $model;
    }
}
