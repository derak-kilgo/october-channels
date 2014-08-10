<?php

namespace Mey\Channels\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateChannelTable extends Migration
{

    public function up()
    {
        Schema::create('mey_channel_types', function($table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->primaryKey();
            $table->string('name');
            $table->string('short_name');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('mey_channels', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->primaryKey();
            $table->string('name');
            $table->string('short_name');
            $table->integer('channel_type_id')->unsigned();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::drop('mey_channels');
        Schema::drop('mey_channel_types');
    }

}
