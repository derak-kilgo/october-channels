<?php

namespace Mey\Channels\Models;

use Str;
use Model;

class FieldType extends Model
{
    protected $softDelete = true;

    public $table = 'mey_field_types';

    /*
     * Validation
     */
    public $rules = [
    ];

    protected $guarded = [];

    public function fields()
    {
        return $this->hasMany('Mey\Channels\Models\Field', 'field_type_id');
    }
}
