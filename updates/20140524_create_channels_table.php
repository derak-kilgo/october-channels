<?php

namespace Mey\Channels\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateChannelTable extends Migration
{

    public function up()
    {
        Schema::create('mey_channels', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->primaryKey();
            $table->string('name');
            $table->string('short_name');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::drop('mey_channels');
    }

}
