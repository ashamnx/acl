<?php

namespace Ashamnx\Acl;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Input;
use Symfony\Component\HttpFoundation\Response as Status;

class ResourceController extends AclController
{

    /**
     * Display a listing of the resource.
     * GET /resource
     *
     * @return Response
     */
    public function index()
    {
        $resource = Resource::with('permissions', 'permissions.action')->paginate(Input::get('limit', self::PER_PAGE));
        return Response::json(['message' => 'Resource loaded.', 'data' => $resource], Status::HTTP_OK);
    }

    /**
     * Show the form for creating a new resource.
     * GET /resource/create
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     * POST /resource
     *
     * @return Response
     */
    public function store()
    {
        $inputs = Input::all();
        $inputs['client_id'] = $this->client->id;
        $resource = new Resource($inputs);
        if ($resource->save()) {
            return Response::json(['message' => 'Created successfully.', 'data' => $resource], Status::HTTP_CREATED);
        } else {
            return Response::json(['errors' => $resource->getErrors()], Status::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Display the specified resource.
     * GET /resource/{id}
     *
     * @param  int $id
     * @return Response
     */
    public function show($id)
    {
        $resource = Resource::find($id);
        if ($resource instanceOf Resource && $this->client->id == $resource->client_id) {
            return Response::json(['data' => $resource], Status::HTTP_OK);
        } else {
            return Response::json(['message' => 'Resource not found.'], Status::HTTP_NOT_FOUND);
        }
    }

    /**
     * Show the form for editing the specified resource.
     * GET /resource/{id}/edit
     *
     * @param  int $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     * PUT /resource/{id}
     *
     * @param  int $id
     * @return Response
     */
    public function update($id)
    {
        $resource = Resource::find($id);
        if ($resource instanceOf Resource && $resource->client_id == $this->client->id) {
            !Input::has('resource_code') ?: $resource->resource_code = Input::get('resource_code');
            !Input::has('resource_name') ?: $resource->resource_name = Input::get('resource_name');
            $resource->setRules($this->client->id, $resource->id); //for validation
            if ($resource->save()) {
                return Response::json(['message' => 'Updated successfully.', 'data' => $resource], Status::HTTP_OK);
            } else {
                return Response::json(['errrors' => $resource->getErrors()], Status::HTTP_UNPROCESSABLE_ENTITY);
            }
        } else {
            return Response::json(['message' => 'Resource not found.'], Status::HTTP_NOT_FOUND);
        }
    }

    /**
     * Remove the specified resource from storage.
     * DELETE /resource/{id}
     *
     * @param  int $id
     * @return Response
     */
    public function destroy($id)
    {
        $resource = Resource::find($id);
        if ($resource instanceOf Resource && $resource->client_id == $this->client->id) {
            if ($resource->delete()) {
                return Response::json(['message' => 'Deleted successfully.'], Status::HTTP_NO_CONTENT);
            } else {
                return Response::json(['message' => 'Oops! Something went wrong.'], Status::HTTP_BAD_REQUEST);
            }
        } else {
            return Response::json(['message' => 'Resource not found.'], Status::HTTP_NOT_FOUND);
        }
    }

    public function getResources()
    {
        $arr = [];
        if ($group = Group::find(Input::get('group'))) {
            $resource = Resource::with(['permissions.action', 'client'])->where('client_id', $group->client_id)->orderBy('resource_name', 'ASC')->get();
            foreach ($resource as $items) {
                foreach ($items->permissions as $item) {
                    $counts = Permissible::where('permissible_id', Input::get('group'))->where('permission_id', $item->id)->count();
                    $item->selected = $counts ? true : false;
                }
            }
            $arr['permissions'] = $resource;
        }
        return Response::json([
            'data' => $arr,
            'message' => 'Resources Loaded'
        ], Status::HTTP_OK);
    }

    public function getResourcesAndActions()
    {
        $resources = Resource::all();
        $actions = Action::all();
        return Response::json([
            'data' => ['resources' => $resources, 'actions' => $actions],
            'message' => 'Resources Loaded'
        ], Status::HTTP_OK);
    }

}