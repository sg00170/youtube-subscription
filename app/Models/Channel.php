<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Channel extends Model
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
        'title', 'description', 'thumbnails'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'user_id',
        'channel_id',
        'created_at', 'updated_at', 'deleted_at'
    ];

    /**
     * Get the user that owns the channel.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the subscriptions for the channel.
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
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

        $model->title                   = $attributes['title'];
        $model->description             = $attributes['description'];
        $model->thumbnails              = $attributes['thumbnails'];

        $model->save();
        
        return $model;
    }
}
