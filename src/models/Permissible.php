<?php

namespace Ashamnx\Acl;

use Illuminate\Database\Eloquent\Model;

class Permissible extends Model
{
    protected $fillable = ['permission_id', 'permissible_id', 'permissible_type'];

    public function permission()
    {
        return $this->belongsTo(Permission::class);
    }

}