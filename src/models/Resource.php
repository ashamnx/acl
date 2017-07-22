<?php

namespace Ashamnx\Acl;

use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    protected $fillable = ['oauth_clients_id', 'parent_id', 'resource_name', 'resource_code'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function permissions()
    {
        return $this->hasMany(Permission::class);
    }

    public function parent()
    {
        return $this->belongsTo(Resource::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Resource::class, 'parent_id');
    }

    public function scopeOfClient($query, $clientId)
    {
        return $query->whereClientId($clientId);
    }
}