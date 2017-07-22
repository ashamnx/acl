<?php

namespace Ashamnx\Acl;


use Illuminate\Routing\Route;

class Acl
{
    protected $auth;

    public static function check($user_id, Route $route)
    {
        $user_groups = UserGroup::whereUserId($user_id)->get();
        $resource = Resource::whereResourceName(self::getResourceName($route))->first();
        $action = Action::whereDescription(self::getActionName($route))->first();
        $permissionGroups = Permission::whereHas('groups', function ($q) use ($user_groups) {
            $q->whereIn('permissible_id', $user_groups->pluck('id'));
        })->whereResourceId($resource->id)->whereActionId($action->id)->count();
        $permissionUsers = Permission::whereHas('users', function ($q) use ($user_id) {
            $q->where('permissible_id', $user_id);
        })->whereResourceId($resource->id)->whereActionId($action->id)->count();


//        dd($user_groups, $route->action['as'], $resource, $action, $permissionGroups, $permissionUsers);
        return ($permissionGroups > 0) || ($permissionUsers > 0);
    }

    public static function fail()
    {
        return false;
    }

    private static function getResourceName(Route $route) {
        $route_as = $route->action['as'];
        if (strpos($route_as, 'api.') !== false) {
            $route_name  = explode('.', $route_as);
            if (count($route_name) == 3) {
                return $route_name[1];
            }
        }
        return false;
    }

    private static function getActionName(Route $route) {
        $route_as = $route->action['as'];
        if (strpos($route_as, 'api.') !== false) {
            $route_name  = explode('.', $route_as);
            if (count($route_name) == 3) {
                return $route_name[2];
            }
        }
        return false;
    }

}