<?php

namespace Ashamnx\Acl;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class Group extends Model
{
    protected $fillable = ['oauth_clients_id', 'parent_id', 'name'];
     protected $hidden = ['oauth_clients_id'];
    protected $morphClass = 'Group';

    public function children()
    {
        return $this->hasMany(Group::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(Group::class, 'parent_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function users()
    {
        return $this->hasMany(Config::get('auth.providers.users.model'), 'user_groups');
    }

    public function permissions()
    {
        return $this->morphToMany(Permission::class, 'permissible');
    }

    public function scopeOfClient($query, $clientId)
    {
        return $query->whereClientId($clientId);
    }

    public function request_acl()
    {
        return $this->hasMany('\Portus\Modules\Requests\RequestAcl', 'user_group_id');
    }


}