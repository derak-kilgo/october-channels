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
        $currentChannel = ChannelModel::where('short_name', '=', $this->property('channelName'))->first();
        if ($currentChannel) {
            $entries = $currentChannel
                ->entries()
                ->limit($limit)
                ->with([ 'fields', 'fields.field' ])
                ->where('published', '=', 1, 'AND')
                ->orWhere(function($query){
                    $query->where('published_at', '<=', date("Y-m-d H:i:s"))
                    ->whereNull('published_at');
                })
                ->get();

            $this->entries = $this->sortArray($this->organizeEntryFields($entries), $sortBy, $orderBy);
        }
    }

    /**
     * Takes all entry properties and entryfield values and creates an array
     * of key=>values pairs for the view.
     *
     * @param \Illuminate\Database\Eloquent\Collection $entryCollection
     *
     * @return array
     */
    private function organizeEntryFields(\Illuminate\Database\Eloquent\Collection $entryCollection)
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

    /**
     * Sort the array collection by the sort and order values specified in the
     * component settings
     *
     * @param array $collection  The array collection
     * @param mixed $sortValue   What value in the array should we sort on
     * @param string $orderValue The order to sort the array
     */
    private function sortArray (array $collection, $sortValue, $orderValue = 'asc')
    {
        $descendingValues = [
            'desc',
            'des',
        ];

        //ascending or descending
        $descending = in_array(strtolower($orderValue), $descendingValues);

        usort($collection, function($a, $b) use ($sortValue, $descending) {
            //Check for empty values in the array
            if (!isset($a[$sortValue]) || !isset($b[$sortValue])) {
                return;
            }
            if ($descending) {
                return strcasecmp($b[$sortValue], $a[$sortValue]);
            } else {
                return strcasecmp($a[$sortValue], $b[$sortValue]);
            }
        });
        return $collection;
    }
}
