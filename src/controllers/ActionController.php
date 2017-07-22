<?php

namespace Ashamnx\Acl;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Input;
use Symfony\Component\HttpFoundation\Response as Status;

class ActionController extends AclController
{

    /**
     * Display a listing of the resource.
     * GET /action
     *
     * @return Response
     */
    public function index()
    {
        $actions = Action::paginate(Input::get('limit', self::PER_PAGE));
        return Response::json([
            'message' => 'Actions Loaded',
            'data' => $actions
        ], Status::HTTP_OK);

    }

    /**
     * Show the form for creating a new resource.
     * GET /action/create
     *
     * @return Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     * POST /action
     *
     * @return Response
     */
    public function store()
    {
        $inputs = Input::all();
        $inputs['client_id'] = $this->client->id;
        $action = new Action($inputs);
        if ($action->save()) {
            return Response::json(['msg' => 'Created successfully.', 'data' => $action], Status::HTTP_CREATED);
        } else {
            return Response::json(['errors' => $action->getErrors()], Status::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Display the specified resource.
     * GET /action/{id}
     *
     * @param  int $id
     * @return Response
     */
    public function show($id)
    {
        $action = Action::find($id);
        if ($action instanceOf Action && $action->client_id == $this->client->id) {
            return Response::json(['data' => $action], Status::HTTP_OK);
        } else {
            return Response::json(['msg' => 'Resource not found.'], Status::HTTP_NOT_FOUND);
        }
    }

    /**
     * Show the form for editing the specified resource.
     * GET /action/{id}/edit
     *
     * @param  int $id
     * @return Response
     */
    public function edit($id)
    {

    }

    /**
     * Update the specified resource in storage.
     * PUT /action/{id}
     *
     * @param  int $id
     * @return Response
     */
    public function update($id)
    {
        $action = Action::find($id);
        if ($action instanceOf Action && $action->client_id == $this->client->id) {
            !Input::has('action_code') ?: $action->action_code = Input::get('action_code');
            !Input::has('description') ?: $action->description = Input::get('description');

            $action->setRules($this->client->id, $action->id); // for validation.
            if ($action->save()) {
                return Response::json(['msg' => 'Updated successfully.', 'data' => $action], Status::HTTP_OK);
            } else {
                return Response::json(['msg' => 'Validation error.', 'errors' => $action->getErrors()], Status::HTTP_UNPROCESSABLE_ENTITY);
            }
        } else {
            return Response::json(['msg' => 'Resource not found.'], Status::HTTP_NOT_FOUND);
        }
    }

    /**
     * Remove the specified resource from storage.
     * DELETE /action/{id}
     *
     * @param  int $id
     * @return Response
     */
    public function destroy($id)
    {
        $action = Action::find($id);
        if ($action instanceOf Action && $action->client_id == $this->client->id) {
            if ($action->delete()) {
                return Response::json(['msg' => 'Deleted successfully.'], Status::HTTP_NO_CONTENT);
            } else {
                return Response::json(['msg' => 'Oops! Something went wrong.'], Status::HTTP_BAD_REQUEST);
            }
        } else {
            return Response::json(['msg' => 'Resource not found.'], Status::HTTP_NOT_FOUND);
        }
    }

}