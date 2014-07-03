<?php

namespace Mey\Channels\Models;

use Str;
use Model;

class FieldGroup extends Model
{

    protected $softDelete = true;

    public $table = 'mey_field_groups';

    /*
     * Validation
     */
    public $rules = [
        'name' => 'required',
        'short_name' => 'unique:mey_field_groups',
    ];

    protected $guarded = [];

    public function fields()
    {
        return $this->belongsToMany('Mey\Channels\Models\Field');
    }

}
