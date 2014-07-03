<?php

namespace Mey\Channels\Models;

use Str;
use Model;

class Field extends Model
{
    protected $softDelete = true;

    public $table = 'mey_fields';

    /*
     * Validation
     */
    public $rules = [
        'name' => 'required',
        'short_name' => 'unique:mey_fields',
    ];

    protected $guarded = [];

    /*
     * October Relationships
     */
    public $belongsTo = [
        'fieldType' => ['Mey\Channels\Models\FieldType'],
        'channels' => ['Mey\Channels\Models\Channel']
    ];

    public $belongsToMany = [
        'fieldGroups' => ['Mey\Channels\Models\FieldGroups']
    ];

    public $hasMany = [
        'entryField' => ['Mey\Channels\Models\EntryField']
    ];

    public function entries()
    {
        return $this->hasMany('Mey\Channels\Models\EntryField', 'field_id');
    }

    public function channels()
    {
        return $this->belongsTo('Mey\Channels\Models\Channel', 'channel_id');
    }

    public function fieldGroups()
    {
        return $this->belongsToMany('Mey\Channels\Models\FieldGroup', 'mey_field_field_group');
    }

    public function fieldType()
    {
        return $this->belongsTo('Mey\Channels\Models\FieldType', 'field_type_id');
    }

}
