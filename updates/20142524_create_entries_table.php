<?php

namespace Mey\Channels\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateEntriesTable extends Migration
{

    public function up()
    {
        Schema::create('mey_entries', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->primaryKey();
            $table->string('name');
            $table->string('short_name');
            $table->text('description')->nullable();
            $table->integer('channel_id')->unsigned();
            //$table->foreign('channel_id')->references('id')->on('mey_channels');
            $table->timestamp('published_at')->nullable();
            $table->boolean('published')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('mey_entry_fields', function($table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->primaryKey();
            $table->integer('entry_id')->unsigned();
            //$table->foreign('mey_entry_id')->references('id')->on('mey_entries');
            $table->integer('field_id')->unsigned();
            //$table->foreign('mey_field_id')->references('id')->on('mey_fields');
            $table->longText('value');
            $table->timestamps();
            $table->softDeletes();
        });
    }


    public function down()
    {
        Schema::drop('mey_entries');
        Schema::drop('mey_entry_fields');
    }

}
