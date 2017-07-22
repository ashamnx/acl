<?php

namespace Ashamnx\Acl\Commands;

use Ashamnx\Acl\Action;
use Ashamnx\Acl\Group;
use Ashamnx\Acl\Permissible;
use Ashamnx\Acl\Permission;
use Ashamnx\Acl\Resource;
use Ashamnx\Acl\UserGroup;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;

class InitCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'acl:init {client?} {user?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize Acl';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if ($this->hasArgument('client')) {
        } else {
            $this->comment('Client Not Defined! Will attempt to select first client!');
        }

        if (!$user = Config::get('auth.providers.users.model')::first()) {
            dd('NO FRIKIN USERS');
        }

        $routes = Route::getRoutes()->getRoutesByName();
        $oauth_clients_id = 1;
        $user_id = 1;
        $group = Group::firstOrCreate(['name'=>'Administrator', 'oauth_clients_id'=> $oauth_clients_id]);
        $user_group = UserGroup::firstOrCreate(['group_id' => $group->id, 'user_id' => $user_id]);
        foreach ($routes as $key => $value) {
            if (strpos($key, 'api.') !== false) {
                $route_name  = explode('.', $value->action['as']);
                if (count($route_name) == 3) {
                    $resource = Resource::firstOrCreate([
                        'resource_name' => $route_name[1],
                        'oauth_clients_id' => $oauth_clients_id
                    ]);
                    $action = Action::firstOrCreate(['description' => $route_name[2], 'oauth_clients_id' => $oauth_clients_id]);
                    $permission = Permission::firstOrCreate(['resource_id' => $resource->id, 'action_id' => $action->id]);
                    $permissable = Permissible::firstOrCreate([
                        'permission_id' => $permission->id,
                        'permissible_id' => $group->id,
                        'permissible_type' => Group::class
                    ]);
                }
            }
        }

        $this->info('Done!');
        return true;
    }
}