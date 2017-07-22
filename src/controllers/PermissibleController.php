<?php

namespace Ashamnx\Acl;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\Response as Status;

class PermissibleController extends AclController
{

    public function show($id)
    {
        return Permissible::with(['permission.resource', 'permission.action'])->where('permissible_id', $id)->get();
    }

    public function store(Request $request)
    {
        if (!$request->get('permissibile_id')) {
            return Response::json(['message' => 'Please select user group ! ', 'data' => []], 400);
        }

        $Permissible = Permissible::where('permission_id', $request->get('id'))
            ->where('permissible_id', $request->get('permissibile_id'))->first();
        if ($Permissible) {
            $Permissible->delete();
        } else {
            $Permissible = new Permissible();
            $Permissible->permission_id = $request->get('id');
            $Permissible->permissible_id = $request->get('permissibile_id');
            $Permissible->permissible_type = 'Group';
            $Permissible->save();
        }

        return Response::json([
            'message' => 'Permission Updated',
            'data' => []
        ], Status::HTTP_OK);

    }


}