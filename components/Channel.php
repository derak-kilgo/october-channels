<?php

namespace Mey\Channels\Components;

use Cms\Classes\ComponentBase;
use Cms\Classes\CmsPropertyHelper;
use Mey\Channels\Models\Entry;
use Mey\Channels\Models\Channel as ChannelModel;
use Request;
use Redirect;
use App;

class Channel extends ComponentBase
{
    public $entries;

    public function componentDetails()
    {
        return [
            'name'        => 'Channel Entries',
            'description' => 'Displays a list of The channel entries available'
        ];
    }

    public function defineProperties()
    {
        return [
            'limit' => [
                'title' => '# of entries per page',
                'default' => '10',
                'type'=>'string',
                'validationPattern'=>'^[0-9]+$',
                'validationMessage'=>'Channel Entries "limit" is not being set properly'
            ],
            'channelName' => [
                'title' => 'Channel Name',
                'description' => 'Name of the channel',
                'type'=>'dropdown',
                'options' => ChannelModel::all()->lists('name', 'short_name'),
            ],
            'sortBy' => [
                'title' => 'Sort By',
                'description' => 'How to sort the entries',
                'type'=>'string',
                'default'=>'created_at',
            ],
            'orderBy' => [
                'title' => 'Order By',
                'description' => 'Order of the channel entries',
                'type'=>'dropdown',
                'default'=>'desc',
                'options' => [
                    'asc' => 'Ascending',
                    'desc' => 'Descending'
                ],
            ],
        ];
    }

    public function onRun()
    {
        $sortBy = $this->property('sortBy');
        $orderBy = $this->property('orderBy');
        $limit = $this->property('limit');
        $currentChannel = ChannelModel::where('short_name', '=', $this->property('channelName'))->firstOrFail();
        $entries = $currentChannel
            ->entries()
            ->orderBy($sortBy, $orderBy)
            ->limit($limit)
            ->with([ 'fields', 'fields.field' ])
            ->where('published', '=', 1, 'AND')
            ->where('published_at', '<=', date("Y-m-d H:i:s"))
            ->get();
        $this->entries = $this->organizeEntryFields($entries);
    }

    private function organizeEntryFields($entryCollection)
    {
        $collection = [];
        $fields = [];
        foreach ($entryCollection as $entry) {
            $entryAttributes = get_object_vars($entry)['attributes'];
            foreach ($entryAttributes as $attributeName => $attributeValue) {
                $fields[$attributeName] = $attributeValue;
            }

            foreach ($entry->fields as $field) {
                $fields[$field->field->short_name] = $field->value;
            }
            $collection[$entry->short_name] = $fields;
        }
        return $collection;
    }
}
