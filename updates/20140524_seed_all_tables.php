<?php

namespace Mey\Channels\Updates;

use October\Rain\Database\Updates\Seeder;
use Mey\Channels\Models\Channel;
use Mey\Channels\Models\Entry;
use Mey\Channels\Models\Field;
use Mey\Channels\Models\EntryField;
use Mey\Channels\Models\FieldGroup;
use Mey\Channels\Models\ChannelType;

class SeedAllTables extends Seeder
{

    public function run()
    {
        FieldType::create([
            'name' => 'Text',
            'short_name' => 'text',
            'description' => 'Perfect for storing strings and other plain text information',
        ]);

        FieldType::create([
            'name' => 'Text Area',
            'short_name' => 'textarea',
            'description' => 'Perfect for storing large amounts of text',
        ]);

        FieldType::create([
            'name' => 'Date',
            'short_name' => 'datepicker',
            'description' => 'A Date Picker',
        ]);

        ChannelType::create([
            'name' => 'Single',
            'short_name' => 'single',
        ]);
    }
}
