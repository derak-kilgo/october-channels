<?php

namespace Mey\Channels\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use Mey\Channels\Models\Entry;
use Mey\Channels\Models\Channel;
use Mey\Channels\Models\Field;
use Mey\Channels\Models\EntryField;
use October\Rain\Support\Markdown;
use Request;
use Input;

class Entries extends Controller
{
    protected $entryFields = [];

    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController'
    ];

    public $entry;

    public $needsDefault;

    public $formConfig = [];

    public $bodyClass = 'compact-container';

    public $entryDefaults = [
        'published_at' => [
            'tab' => 'Manage',
            'label' => 'Publish On',
            'type' => 'datepicker',
            'span' => 'left',
        ],
        'published' => [
            'tab' => 'Manage',
            'label' => 'Publish',
            'type' => 'checkbox',
            'default' => 1,
            'span' => 'left',
            'cssClass' => 'checkbox-align'
        ],
    ];

    public $listConfig = [
        'list' => [
            'list' => [
                'columns' => [
                    'name' => [
                        'label' => 'Name'
                    ],
                    'short_name' => [
                        'label' => 'Short Name'
                    ],
                    'channels' => [
                        'label' => 'Channel',
                        'relation' => 'channel',
                        'select' => 'mey_channels.name',
                    ],
                    'published_at' => [
                        'label' => 'Published',
                        'type' => 'date'
                    ],
                ],
            ],
            'modelClass' => 'Mey\Channels\Models\Entry',
            'title' => 'Manage Entries',
            'recordUrl' => 'mey/channels/entries/update/:id',
            'noRecordsMessage' => 'backend::lang.list.no_records',
            'recordsPerPage' => 25,
            'toolbar' => [
                'buttons' => 'list_toolbar',
                'search' => [
                    'prompt' => 'backend::lang.list.search_prompt'
                ]
            ]
        ]
    ];


    public function __construct()
    {
        $entryId = Request::segment(6);

        if (!empty($entryId)) {
            $this->entry = Entry::with('fields', 'channel', 'channel.fields')->where('id', '=', $entryId)->first();
            $this->initForm();
        }

        $this->registerEventListeners();
        $this->formConfig = $this->buildFormConfig();

        parent::__construct();

        BackendMenu::setContext('Mey.Channels', 'channels', 'entries');
        $this->addCss('/plugins/mey/channels/assets/css/mey.channels.main.css');
    }

    public function buildFormConfig()
    {
        return [
            'name' => 'Entries',
            'form' =>  [
                'fields' => [
                    'channel' => [
                        'label' => 'Channel',
                        'type' => 'relation',
                        'tab' => 'Manage',
                        'span' => 'left',
                        'context' => 'create',
                    ],
                    'name' => [
                        'label' => 'Name',
                        'placeholder' => 'Entry Name',
                        'span' => 'left',
                    ],
                    'short_name' => [
                        'label' => 'Short Name',
                        'placeholder' => 'Entry Short Name',
                        'span' => 'right',
                        'attributes' => [
                            'data-input-preset' => 'input[name="Entry[name]"]',
                            'data-input-preset-type' => 'slug',
                            'data-input-preset-closest-parent' => 'form',
                        ]
                    ],
                    'toolbar' => [
                        'type' => 'partial',
                        'path' => 'entry_toolbar',
                        'cssClass' => 'collapse-visible',
                    ]
                ],
                'secondaryTabs' => [
                    'fields' => $this->entryDefaults + $this->entryFields
                ],
            ],
            'modelClass' => 'Mey\Channels\Models\Entry',

            'defaultRedirect' => 'mey/channels/entries',
            'create' => [
                'redirect' => 'mey/channels/entries/update/:id',
                'redirectClose' => 'mey/channels/entries',
            ],
            'update' => [
                'redirect' => 'mey/channels/entries',
                'redirectClose' => 'mey/channels/entries'
            ]
        ];
    }

    public function initForm()
    {
        $entry = $this->entry;
        $channel = $entry->channel;

        $channelFields = $channel->fields;
        $entryFields = $entry->fields;
        $formConfig = [];

        if ($channelFields->count() >= 1) {
            foreach ($channelFields as $channelField) {
                $validChannelFields[] = $channelField->short_name;
                $fieldType = $channelField->fieldType()->first();
                $formConfig["entryField"][$channelField->short_name]["new"][$channelField->id] = [
                    'label' => $channelField->name,
                    'type' => $fieldType->short_name,
                    'tab' => 'Fields',
                ];
                //Markdown hack
                if ($fieldType->short_name === 'markdown') {
                    $formConfig["entryField"][$channelField->short_name]["new"][$channelField->id]['type'] = 'codeeditor';
                    $formConfig["entryField"][$channelField->short_name]["new"][$channelField->id]['language'] = 'markdown';
                }
            }
            if ($entryFields->count() >= 1) {
                foreach ($entryFields as $entryField) {
                    $field = $entryField->field()->first();
                    if ($field instanceof Field && in_array($field->short_name, $validChannelFields)) {
                        $fieldType = $field->fieldType()->first();
                        $formConfig["entryField"][$field->short_name][$entryField->id] = [
                            'label' => $field->name,
                            'tab' => 'Fields',
                            'type' => $fieldType->short_name,
                            'default' => $entryField->value,
                        ];
                        if ($fieldType->short_name === 'markdown') {
                            $formConfig["entryField"][$field->short_name][$entryField->id]['type'] = 'codeeditor';
                            $formConfig["entryField"][$field->short_name][$entryField->id]['language'] = 'markdown';
                        }
                    }
                }
            }
            foreach ($formConfig['entryField'] as $fieldName => $fieldConfig) {
                if (count($fieldConfig) > 1) {
                    unset($fieldConfig['new']);
                    $existingFieldKey = array_keys($fieldConfig)[0];
                    $existingFieldValue = array_shift($fieldConfig);
                    $formConfig["entryField][{$fieldName}][{$existingFieldKey}]"] = $existingFieldValue;
                    $this->needsDefault["entryField][{$fieldName}][{$existingFieldKey}]"] = $existingFieldValue['default'];
                } else {
                    $newFieldConfig = array_shift($fieldConfig);
                    $newFieldKey = array_keys($newFieldConfig)[0];
                    $newFieldValue = array_shift($newFieldConfig);
                    $formConfig["entryField][{$fieldName}][new][{$newFieldKey}]"] = $newFieldValue;
                }
                unset($formConfig['entryField']);
            }
        }

        $this->entryFields = $formConfig;
    }

    public function update($recordId, $context = null)
    {
        if (Request::getMethod() === 'post') {
            echo "post"; exit;
        }
        return $this->getClassExtension('Backend.Behaviors.FormController')->update($recordId, $context);
    }

    public function update_onSave()
    {
        $inputs = \Input::all()['Entry'];
        $entry = $this->entry;
        $this->saveEntryFields($inputs['entryField']);
        unset($inputs['entryField']);

        //Override default behavior to not set published if left blank
        if (!isset($inputs['published'])) {
            $inputs['published'] = 0;
        }
        foreach ($inputs as $property => $value) {
            $entry->$property = $value;
        }

        $entry->save();


        \Flash::success('Entry Fields Saved Successfully');
    }

    private function registerEventListeners()
    {
        //This enable the default values to be populated in the backend
        \Event::listen('backend.form.extendFields', function($widget) {
            // This should reference your controller
            $controller = $widget->getController();
            if (!$controller instanceof \Mey\Channels\Controllers\Entries) {
                return;
            }

            if ($this->entry instanceof Entry) {
                //Helps with published default values
                if ($this->entry->published != 1) {
                    $widget->getField('published')->value = 0;
                }
            }

            if (is_null($widget->getField('published_at')->value)) {
                $date = new \DateTime;
                $widget->getField('published_at')->value = $date->format('Y-m-d H:i:s');
            }

            $fields = $controller->needsDefault;
            if (!empty($fields)) {
                foreach ($fields as $field => $value) {
                    $widget->getField($field)->value = $value;
                }
            }
        });
    }

    /**
     * Saves or creates a new entryField for each entryField input that comes back
     * for the entry
     *
     * @param array $entryFieldValues
     */
    private function saveEntryFields($entryFieldValues)
    {
        foreach ($entryFieldValues as $shortName => $values) {
            foreach ($values as $key => $value) {
                if ($key === 'new') {
                    $fieldId = array_keys($value)[0];
                    $field = Field::where('id', '=', $fieldId)->with('fieldType')->first();
                    $fieldType = $field->fieldType;
                    $fieldValue = array_shift($value);

                    if ($fieldType->short_name === 'markdown') {
                        $processedValue = $this->formatHtml($fieldValue);
                    }

                    $entryField = EntryField::create(
                        [
                            'field_id' => $fieldId,
                            'entry_id' => $this->entry->id,
                            'value' => $fieldValue,
                            'processed_value' => $processedValue ? $processedValue : null
                        ]
                    );
                } else {
                    $entryField = EntryField::where('id', '=', $key)->with('field', 'field.fieldtype')->first();
                    $fieldType = $entryField->field->fieldType;

                    if ($fieldType->short_name === 'markdown') {
                        $entryField->processed_value = $this->formatHtml($value);
                    }

                    $entryField->value = $value;
                    $entryField->save();
                }
            }
        }
        return;
    }

    public static function formatHtml($input)
    {
        $result = Markdown::parse(trim($input));

        return $result;
    }
}
