<?php

namespace Ashamnx\Acl;

use Illuminate\Database\Eloquent\Model;

class Action extends Model
{
    protected $fillable = ['oauth_clients_id', 'description'];
    protected $hidden = ['oauth_clients_id', 'created_at', 'updated_at'];

    public function permissions()
    {
        return $this->hasMany(Permission::class);
    }

    public function scopeOfCode($query, $actionCode)
    {
        return $query->whereActionCode($actionCode);
    }

    public function scopeOfClient($query, $clientId)
    {
        return $query->whereClientId($clientId);
    }
}