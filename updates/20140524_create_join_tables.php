<?php

namespace Mey\Channels\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateJoinTables extends Migration
{

    public function up()
    {
        Schema::create('mey_field_field_group', function($table) {
            $table->integer('field_group_id')->unsigned();
            //$table->foreign('mey_field_group_id')->references('id')->on('mey_field_groups');
            $table->integer('field_id')->unsigned();
            //$table->foreign('mey_field_id')->references('id')->on('mey_fields');
            $table->timestamps();
            $table->softDeletes();
        });


        Schema::create('mey_channel_field', function($table) {
            $table->integer('channel_id')->unsigned();
            //$table->foreign('mey_channel_id')->references('id')->on('mey_channels');
            $table->integer('field_id')->unsigned();
            //$table->foreign('mey_field_id')->references('id')->on('mey_fields');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::drop('mey_field_field_group');
        Schema::drop('mey_channel_field');
    }

}
