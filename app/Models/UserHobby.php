<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserHobby extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_hobbies';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];
}
