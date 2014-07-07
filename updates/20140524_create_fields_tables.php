<?php

namespace Mey\Channels\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateFieldsTable extends Migration
{

    public function up()
    {
        Schema::create('mey_field_types', function($table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->primaryKey();
            $table->string('name');
            $table->string('short_name');
            $table->string('options')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('mey_field_groups', function($table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->primaryKey();
            $table->string('name');
            $table->string('short_name');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('mey_fields', function($table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->primaryKey();
            $table->string('name');
            $table->string('short_name');
            $table->string('config')->nullable();
            $table->text('description')->nullable();
            $table->integer('field_type_id')->unsigned();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::drop('mey_field_types');
        Schema::drop('mey_field_groups');
        Schema::drop('mey_fields');
    }

}
