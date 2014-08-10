<?php

namespace Mey\Channels\Components;

use Cms\Classes\ComponentBase;
use Cms\Classes\CmsPropertyHelper;
use Mey\Channels\Models\Channel as ChannelModel;
use Mey\Channels\Support\Collection;
use Request;
use Redirect;
use App;

class Entry extends ComponentBase
{
    public $entries;

    public function componentDetails()
    {
        return [
            'name'        => 'Entry',
            'description' => 'Return a single entry'
        ];
    }

    public function defineProperties()
    {
        return [
            'name' => [
                'title' => 'The short name of the slug',
                'default' => ':slug',
                'type'=> 'string',
            ],
            'channelName' => [
                'title' => 'Channel Name',
                'description' => 'Name of the channel to find the entry',
                'type'=>'dropdown',
                'options' => ChannelModel::all()->lists('name', 'short_name'),
            ],
        ];
    }

    public function onRun()
    {
        $name = $this->propertyOrParam('name');
        $currentChannel = ChannelModel::where('short_name', '=', $this->property('channelName'))->first();
        if ($currentChannel) {
            $entry = $currentChannel
                ->entries()
                ->with([ 'fields', 'fields.field' ])
                ->where('published', '=', 1)
                ->where('short_name', '=', $name)
                ->orWhere(function($query){
                    $query->where('published_at', '<=', date("Y-m-d H:i:s"))
                    ->whereNull('published_at');
                })
                ->first();

            if ($entry) {
                foreach ($entry->toArrayWithFields() as $key => $value) {
                    $this->$key = $value;
                }
            }
        }
    }



}
