<?php

namespace Mey\Channels\Support;

use Illuminate\Support\Collection as BaseCollection;

class Collection extends BaseCollection
{

    /**
     * Converts an existing collection of objects to a new Collection
     *
     * @param \Illuminate\Database\Eloquent\Collection $items
     */
    public static function buildFromCollection(\Illuminate\Database\Eloquent\Collection $items)
    {
        $collection = [];
        foreach ($items as $item) {
            $collection[] =  $item;
        }
        return new static($collection);
    }

    /**
     * Add an item to the collection.
     *
     * @param  mixed  $item
     * @return \Mey\Channels\Support\Collection
     */
    public function add($item)
    {
        $this->items[] = $item;
        return $this;
    }

    /**
     * Takes all entry properties and entryfield values and creates an array
     * of key=>values pairs for the view.
     *
     *
     * @return \Mey\Channels\Support\Collection
     */
    public function organizeEntryFields()
    {
        $collection = [];
        $fields = [];
        foreach ($this->items as $entry) {
            $entryAttributes = get_object_vars($entry)['attributes'];
            foreach ($entryAttributes as $attributeName => $attributeValue) {
                $fields[$attributeName] = $attributeValue;
            }

            foreach ($entry->fields as $field) {
                $fields[$field->field->short_name] = $field->value;
            }
            $collection[$entry->short_name] = $fields;
        }
        $this->items = $collection;
        return $this;
    }

    /**
     * Sort the array collection by the sort and order values specified in the
     * component settings
     *
     * @param mixed $sortValue   What value in the array should we sort on
     * @param string $orderValue The order to sort the array
     */
    public function sortValues ($sortValue, $orderValue = 'desc')
    {
        //ascending or descending
        $ascending = $orderValue === 'asc';

        $this->sort(function($a, $b) use ($sortValue, $ascending) {
            //Check for empty values in the array
            if (!isset($a[$sortValue]) || !isset($b[$sortValue])) {
                return;
            }
            if (!$ascending) {
                return strcasecmp($b[$sortValue], $a[$sortValue]);
            } else {
                return strcasecmp($a[$sortValue], $b[$sortValue]);
            }
        });
        return $this;
    }

    /**
     * Handles the limiting of the entry collection
     *
     * @param mixed $limit
     */
    public function limit($limit)
    {
        return array_splice($this->items, 0, $limit);
    }
}
