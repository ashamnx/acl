<?php

namespace Ashamnx\Acl;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class UserGroup extends Model
{
    protected $fillable = ['group_id', 'user_id'];
    protected static $rules = [
        'group_id' => 'integer',
        'user_id' => 'integer'
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function user()
    {
        return $this->belongsTo(Config::get('auth.providers.users.model'));
    }

}