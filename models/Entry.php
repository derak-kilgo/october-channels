<?php

namespace Mey\Channels\Models;

use Str;
use Model;

class Entry extends Model
{

    protected $softDelete = true;

    public $table = 'mey_entries';

    /**
     * The attributes that should be mutated to dates.
     * @var array
     */
    protected $dates = ['published_at'];

    /*
     * Validation
     */
    public $rules = [
        'name' => 'required',
    ];

    /**
     * October relationships
     *
     */
    public $hasMany = [
        'fields' => ['Mey\Channels\Models\Field']
    ];

    public $belongsTo = [
        'channel' => ['Mey\Channels\Models\Channel']
    ];

    protected $guarded = [];

    public function fields()
    {
        return $this->hasMany('Mey\Channels\Models\EntryField', 'entry_id');
    }

    public function channel()
    {
        return $this->belongsTo('Mey\Channels\Models\Channel', 'channel_id');
    }

}
