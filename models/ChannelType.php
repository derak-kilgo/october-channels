<?php

namespace Mey\Channels\Models;

use Str;
use Model;

class ChannelType extends Model
{
    protected $softDelete = true;

    public $table = 'mey_channel_types';

    /*
     * Validation
     */
    public $rules = [
    ];

    protected $guarded = [];

    public function fields()
    {
        return $this->hasMany('Mey\Channels\Models\Channel', 'channel_type_id');
    }
}
