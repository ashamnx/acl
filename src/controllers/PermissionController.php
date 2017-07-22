<?php

namespace Ashamnx\Acl;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Input;
use Symfony\Component\HttpFoundation\Response as Status;
use Illuminate\Support\Facades\Validator;


class PermissionController extends AclController
{

    public function addPermissions(Request $request)
    {
        if ($request->has('permissibile_id')) {
            foreach ($request->get('resources') as $item) {
                foreach ($item['permissions'] as $resource) {
                    $action_id = $resource['action']['id'];
                    $resource_id = $item['id'];

                    if (!($permission = Permission::whereActionId($action_id)->whereResourceId($resource_id)->first())) {
                        $permission = new Permission(['action_id' => $action_id, 'resource_id' => $resource_id]);
                        $permission->save();
                    }

                    if (!($permissible = Permissible::wherePermissionId($permission->id)->wherePermissibleId($request->get('permissibile_id'))->wherePermissibleType('Group')->first())) {
                        $permissible = new Permissible(['permission_id' => $permission->id, 'permissible_id' => $request->get('permissibile_id'), 'permissible_type' => 'Group']);
                        $permissible->save();
                    }
                }
            }
            return Response::json(['message' => 'Permission assigned.'], Status::HTTP_OK);
        }
        return Response::json(['message' => 'Please Select the Group from the list'], 400);

    }

    /**
     * Display a listing of the resource.
     * GET /resource
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $limit = Input::has('limit') ? Input::get('limit') : self::PER_PAGE;
        $resource = Permission::with('groups')->paginate($request->get('limit'));
        return Response::json(['message' => 'Permission loaded.', 'data' => $resource], Status::HTTP_OK);
    }

    public function assign()
    {
        $rules = [
            'resource' => 'required|alpha_dash|exists:resources,resource_name,client_id,' . $this->client->id,
            'action' => 'required|alpha_dash|exists:actions,action_code,client_id,' . $this->client->id,
            'group' => 'required_without:user_id|alpha_dash|exists:groups,name,client_id, ' . $this->client->id,
            'user_id' => 'required_without:group|numeric|exists:users,id'
        ];

        $validator = Validator::make(Input::all(), $rules);

        if ($validator->passes()) {
            $action = Action::ofClient($this->client->id)->where('action_code', Input::get('action'))->get()->first();
            $resource = Resource::ofClient($this->client->id)->where('resource_name', Input::get('resource'))->get()->first();
            $permission = Permission::ofResource($resource->id)->where('action_id', $action->id)->get()->first();
            if (!$permission instanceOf Permission) {
                $permission = new Permission();

                $resource->setrules($this->client->id);
                $permission->resource()->associate($resource);

                $action->setRules($this->client->id);
                $permission->action()->associate($action);
                if (!$permission->save()) {
                    return Response::json(['message' => 'Could not create permission.', 'errors' => $permission->getErrors()], Status::HTTP_UNPROCESSABLE_ENTITY);
                }
            }
            if (Input::has('group')) {
                $group = Group::ofClient($this->client->id)->where('name', Input::get('group'))->get()->first();
                $currentPermissions = $group->permissions()->ofResource($resource->id)->ofAction($action->id)->with('action')->get();
                if (count($currentPermissions) == 0) {
                    $permission->groups()->attach($group);
                    return Response::json(['message' => 'Permission assigned.'], Status::HTTP_OK);
                } else {
                    return Response::json(['message' => 'Permission already exists.'], Status::HTTP_OK);
                }

            } else {
                $user = $this->user::find(Input::get('user_id'));
                $currentPermissions = $user->permissions()->ofResource($resource->id)->ofAction($action->id)->with('action')->get();
                if (count($currentPermissions) == 0) {
                    $permission->users()->attach($user);
                    return Response::json(['message' => 'Permission assigned.'], Status::HTTP_OK);
                } else {
                    return Response::json(['message' => 'Permission already exists.'], Status::HTTP_OK);
                }
            }

        } else {
            return Response::json(['errors' => $validator->errors()], Status::HTTP_UNPROCESSABLE_ENTITY);
        }

    }

    public function unassign()
    {
        $rules = [
            'resource' => 'required|alpha_dash|exists:resources,resource_name,client_id,' . $this->client->id,
            'action' => 'required|alpha_dash|exists:actions,action_code,client_id,' . $this->client->id,
            'group' => 'required_without:user_id|alpha_dash|exists:groups,name,client_id, ' . $this->client->id,
            'user_id' => 'required_without:group|numeric|exists:users,id'
        ];

        $validator = Validator::make(Input::all(), $rules);

        if ($validator->passes()) {
            $action = Action::ofClient($this->client->id)->where('action_code', Input::get('action'))->get()->first();
            $resource = Resource::ofClient($this->client->id)->where('resource_name', Input::get('resource'))->get()->first();
            $permission = Permission::ofResource($resource->id)->where('action_id', $action->id)->get()->first();
            if (!$permission instanceOf Permission) {
                return Response::json(['message' => 'Associated permission not found.'], Status::HTTP_NOT_FOUND);
            }
            if (Input::has('group')) {
                $group = Group::ofClient($this->client->id)->where('name', Input::get('group'))->get()->first();
                $currentPermissions = $group->permissions()->ofResource($resource->id)->ofAction($action->id)->get();
                $assigned = $currentPermissions->find($permission);
                if ($assigned instanceOf Permission) {
                    $permission->groups()->detach($group);
                    return Response::json(['message' => 'Permission unassigned.'], Status::HTTP_OK);
                } else {
                    return Response::json(['message' => 'Associated permission not found.'], Status::HTTP_NOT_FOUND);
                }
            } else {
                $user = $this->user::find(Input::get('user_id'));
                $currentPermissions = $user->permissions()->with('action', 'resource')->ofResource($resource->id)->ofAction($action->id)->get();
                $assigned = $currentPermissions->find($permission);
                if ($assigned instanceOf Permission) {
                    $permission->users()->detach($user);
                    return Response::json(['message' => 'Permission unassigned.'], Status::HTTP_OK);
                } else {
                    return Response::json(['message' => 'Associated permission not found.'], Status::HTTP_NOT_FOUND);
                }
            }

        } else {
            return Response::json(['errors' => $validator->errors()], Status::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

}