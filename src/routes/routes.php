<?php

namespace Ashamnx\Acl;

use Illuminate\Support\Facades\Route;

Route::middleware('acl')->as('api.')->prefix('api')->group(function () {
    Route::get('test', function () {
        return 'Test';
    });

    Route::resource('actions', ActionController::class, array('index', 'show', 'update', 'store'));
    Route::resource('resources', ResourceController::class, array('index', 'show', 'update', 'store'));
    Route::resource('groups', GroupController::class, array('index', 'show', 'update', 'store'));

    Route::resource('permissions', PermissionController::class, array('index', 'show', 'update', 'store'));
    Route::resource('permissible', PermissibleController::class, array('show', 'update', 'store'));

    Route::get('get_all_permissions', ['uses' => 'Ashamnx\Acl\ResourceController@getResources', 'as' => 'permissions.get_resources']);
    Route::get('get_resources_and_actions', ['uses' => 'Ashamnx\Acl\ResourceController@getResourcesAndActions', 'as' => 'resources.get_resources_and_actions']);

    Route::post('permissions/add', ['uses' => 'Ashamnx\Acl\PermissionController@addPermissions', 'as' => 'permissions.add_permission']);
    Route::post('groups/change', ['uses' => 'Ashamnx\Acl\GroupController@change_group', 'as' => 'groups.change_group']);
});