<?php

namespace Mey\Channels\Models;

use Str;
use Model;

class Channel extends Model
{

    protected $softDelete = true;

    public $table = 'mey_channels';

    /*
     * Validation
     */
    public $rules = [
        'name' => 'required',
        'short_name' => 'unique:mey_channels',
    ];

    /**
     * October relationships
     *
     */
    public $belongsToMany = [
        'fields' => ['Mey\Channels\Models\Field']
    ];

    public $belongsTo = [
        'channelType' => ['Mey\Channels\Models\ChannelType'],
    ];

    public $hasMany = [
        'entries' => ['Mey\Channels\Models\Entry']
    ];

    protected $guarded = [];

    public function fields()
    {
        return $this->belongsToMany('Mey\Channels\Models\Field', 'mey_channel_field');
    }

    public function entries()
    {
        return $this->hasMany('Mey\Channels\Models\Entry', 'channel_id');
    }

    public function channelType()
    {
        return $this->belongsTo('Mey\Channels\Models\ChannelType', 'channel_type_id');
    }

    public function isSingle()
    {
        return $this->channelType()->first()->short_name === 'single';
    }
}
