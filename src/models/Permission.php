<?php

namespace Ashamnx\Acl;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class Permission extends Model
{
    protected $fillable = ['resource_id', 'action_id'];

    public function resource()
    {
        return $this->belongsTo(Resource::class);
    }

    public function permissibles()
    {
        return $this->morphToMany();
    }

    public function users()
    {
        return $this->morphedByMany(Config::get('auth.providers.users.model'), 'permissible');
    }

    public function groups()
    {
        return $this->morphedByMany(Group::class, 'permissible');
    }

    public function action()
    {
        return $this->belongsTo(Action::class);
    }

    public function scopeOfResource($query, $resourceId)
    {
        return $query->whereResourceId($resourceId);
    }

    public function scopeOfAction($query, $actionId)
    {
        return $query->whereActionId($actionId);
    }
}
