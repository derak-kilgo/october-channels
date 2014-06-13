<?php

namespace Mey\Channels\Models;

use Str;
use Model;

class EntryField extends Model
{

    protected $softDelete = true;

    public $table = 'mey_entry_fields';

    /*
     * Validation
     */
    public $rules = [
        'value' => 'required',
    ];

    protected $guarded = [];

    public function field()
    {
        return $this->belongsTo('Mey\Channels\Models\Field', 'field_id');
    }

    public function entry()
    {
        return $this->belongsTo('Mey\Channels\Models\Entry', 'entry_id');
    }

}
