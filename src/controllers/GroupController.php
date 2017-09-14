<?php

namespace Ashamnx\Acl;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\Response as Status;

class GroupController extends AclController
{

    /**
     * Display a listing of the resource.
     * GET /group
     *
     * @return Response
     */
    public function index()
    {
        $groups = Group::all();

        return Response::json([
            'message' => 'Groups Loaded',
            'data' => $groups
        ], Status::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     * POST /group
     *
     * @return Response
     */
    public function store()
    {
        $inputs = Input::all();
        $inputs['oauth_clients_id'] = 1;
        $group = new Group($inputs);
        if ($group->save()) {
            return \Response::json(['msg' => 'Created successfully.', 'data' => $group], Status::HTTP_CREATED);
        } else {
            return \Response::json(['errors' => $group->getErrors()], Status::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Display the specified resource.
     * GET /group/{id}
     *
     * @param  int $id
     * @return Response
     */
    public function show($id)
    {
        $group = Group::with(['permissions.resource','permissions.action'])->find($id);
        if ($group instanceOf Group) {
            return \Response::json(['data' => $group], Status::HTTP_OK);
        } else {
            return \Response::json(['msg' => 'Resource not found.'], Status::HTTP_NOT_FOUND);
        }
    }

    /**
     * Show the form for editing the specified resource.
     * GET /group/{id}/edit
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
     * PUT /group/{id}
     *
     * @param  int $id
     * @return Response
     */
    public function update($id)
    {
        $group = Group::find($id);
        if ($group instanceOf Group && $group->client_id == $this->client->id) {
            if (Input::has('name')) {
                $group->name = Input::get('name');
                $group->setRules($this->client->id, $group->id);
                if ($group->save()) {
                    return \Response::json(['msg' => 'Updated successfully.', 'data' => $group], Status::HTTP_OK);
                } else {
                    return \Response::json(['errors' => $group->getErrors()], Status::HTTP_UNPROCESSABLE_ENTITY);
                }
            } else {
                return \Response::json(['msg' => 'Nothing\'s to be changed.'], Status::HTTP_OK);
            }
        } else {
            return \Response::json(['msg' => 'Resource not found.'], Status::HTTP_NOT_FOUND);
        }
    }

    /**
     * Remove the specified resource from storage.
     * DELETE /group/{id}
     *
     * @param  int $id
     * @return Response
     */
    public function destroy($id)
    {
        $group = Group::find($id);
        if ($group instanceOf Group && $group->client_id == $this->client->id) {
            if ($group->delete()) {
                return \Response::json(['msg' => 'Successfully deleted.'], Status::HTTP_NO_CONTENT);
            } else {
                return \Response::json(['msg' => 'Oops! Something went wrong.'], Status::HTTP_BAD_REQUEST);
            }
        } else {
            return \Response::json(['msg' => 'Resource not found.'], Status::HTTP_NOT_FOUND);
        }
    }

    public function assign()
    {
        $rules = [
            'user_id' => 'required|numeric|exists:users,id',
            'group_id' => 'required|numeric|exists:groups,id,client_id,' . $this->client->id
        ];
        $validator = \Validator::make(Input::all(), $rules);
        if ($validator->passes()) {
            $user = User::find(Input::get('user_id'));
            $group = Group::find(Input::get('group_id'));
            $exists = $user->groups->find($group);
            if (!$exists instanceOf Group) {
                $user->groups()->attach($group);
                return \Response::json(['msg' => 'Assigned successfully.'], Status::HTTP_OK);
            } else {
                return \Response::json(['msg' => 'Aready assigned.'], Status::HTTP_OK);
            }
        } else {
            return \Response::json(['msg' => 'Resource not found.'], Status::HTTP_NOT_FOUND);
        }

    }

    public function unassign()
    {
        $rules = [
            'user_id' => 'required|numeric|exists:users,id',
            'group_id' => 'required|numeric|exists:groups,id,client_id,' . $this->client->id
        ];
        $validator = \Validator::make(Input::all(), $rules);
        if ($validator->passes()) {
            $user = User::find(Input::get('user_id'));
            $group = Group::find(Input::get('group_id'));
            $exists = $user->groups->find($group);
            if ($exists instanceOf Group) {
                $user->groups()->detach($group);
                return \Response::json(['msg' => 'Unassigned successfully.'], Status::HTTP_OK);
            } else {
                return \Response::json(['msg' => 'Aready unassigned.'], Status::HTTP_OK);
            }
        } else {
            return \Response::json(['msg' => 'Resource not found.'], Status::HTTP_NOT_FOUND);
        }
    }

    public function change_group()
    {
        // return Input::all();
        $rules = [
            'user_id' => 'required|numeric|exists:users,id',
            'group_id' => 'required|numeric|exists:groups,id'
        ];
        $validator = \Validator::make(Input::all(), $rules);
        if ($validator->passes()) {
            $ug = UserGroup::where('user_id', Input::get('user_id'))->where('group_id', Input::get('group_id'))->first();
            if ($ug && Input::get('selection') == false) {
                $ug->delete();
                return $ug;
            } else {
                $ug = new UserGroup(['user_id' => Input::get('user_id'), 'group_id' => Input::get('group_id')]);
                $ug->save();
            }
        } else {
            return \Response::json(['msg' => 'Resource not found.'], Status::HTTP_NOT_FOUND);
        }

    }

    private function getCurrentClient()
    {
        return Client::where('client_id', getenv('client_id'))->first();
    }
}