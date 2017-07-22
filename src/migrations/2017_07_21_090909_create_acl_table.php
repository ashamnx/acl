<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAclTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('groups', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('oauth_clients_id')->unsigned();
            $table->integer('parent_id')->unsigned()->nullable();
            $table->string('name', 20);
            $table->timestamps();

            $table->foreign('oauth_clients_id')->references('id')->on('oauth_clients')->onDelete('cascade');
            $table->foreign('parent_id')->references('id')->on('groups');
        });

        Schema::create('user_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('group_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->timestamps();

            $table->foreign('group_id')->references('id')->on('groups');
            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::create('actions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('oauth_clients_id')->unsigned();
            $table->string('description', 50);
            $table->timestamps();

            $table->foreign('oauth_clients_id')->references('id')->on('oauth_clients')->onDelete('cascade');
        });

        Schema::create('resources', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('oauth_clients_id')->unsigned()->index();
            $table->integer('parent_id')->unsigned()->index()->nullable();
            $table->string('resource_name');
            $table->timestamps();

            $table->foreign('oauth_clients_id')->references('id')->on('oauth_clients')->onDelete('cascade');
            $table->foreign('parent_id')->references('id')->on('resources')->onDelete('cascade');
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('resource_id')->unsigned();
            $table->integer('action_id')->unsigned();
            $table->timestamps();

            $table->foreign('resource_id')->references('id')->on('resources');
            $table->foreign('action_id')->references('id')->on('actions');

        });

        Schema::create('permissibles', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('permission_id');
            $table->morphs('permissible');

            $table->timestamps();

        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('permissibles');
        Schema::drop('permissions');
        Schema::drop('resources');
        Schema::drop('actions');
        Schema::drop('user_groups');
        Schema::drop('groups');
        Schema::drop('clients');
    }
}