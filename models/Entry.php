<?php

namespace Mey\Channels\Models;

use Str;
use Model;

class Entry extends Model
{

    protected $softDelete = true;

    public $table = 'mey_entries';

    public $validationErrors;

    /**
     * The attributes that should be mutated to dates.
     * @var array
     */
    protected $dates = ['published_at'];

    protected $sortable = [
        'name',
        'short-name',
        'description',

    ];

    /*
     * Validation
     */
    public $rules = [
        'name' => 'required',
        'short_name' => 'uniqueInChannel:mey_entries',
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

    public function __construct()
    {
        $this->registerInputValidators();
    }

    public function fields()
    {
        return $this->hasMany('Mey\Channels\Models\EntryField', 'entry_id');
    }

    public function channel()
    {
        return $this->belongsTo('Mey\Channels\Models\Channel', 'channel_id');
    }

    private function registerInputValidators()
    {
        $this->validationErrors = new \Illuminate\Support\MessageBag;
        $model = $this;
        \Validator::extend('uniqueInChannel', function($attribute, $value, $parameters) use ($model) {
            $shortName = strtolower($value);
            $channel = Channel::with('entries')->where('id', '=', $model->channel->id)->first();
            foreach ($channel->entries as $entry) {
                if (strtolower($entry->short_name) === $shortName) {
                    return false;
                }
            }
            return true;
        });
    }

    public function toArrayWithFields()
    {
        $entryAttributes = get_object_vars($this)['attributes'];
        $fields = [];
        foreach ($entryAttributes as $attributeName => $attributeValue) {
            $fields[$attributeName] = $attributeValue;
        }

        foreach ($this->fields as $field) {
            $fields[$field->field->short_name] = $field->value;
        }
        return $fields;
    }

}
