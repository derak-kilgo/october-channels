<?php

namespace Mey\Channels\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use Mey\Channels\Models\Entry;
use Mey\Channels\Models\Field;
use Mey\Channels\Models\EntryField;
use Request;
use Input;

class Entries extends Controller
{
    protected $entryFields = [];

    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController'
    ];

    public $needsDefault;

    public $formConfig = [];

    public $bodyClass = 'compact-container';

    public $entryDefaults = [
        'description' => [
            'tab' => 'Manage',
            'label' => 'Description',
            'type' => 'textarea',
            'span' => 'left',
        ],
        'published_at' => [
            'tab' => 'Manage',
            'label' => 'Publish On',
            'type' => 'datepicker',
            'span' => 'right',
        ],
        'published' => [
            'tab' => 'Manage',
            'label' => 'Publish',
            'type' => 'checkbox',
            'span' => 'right',
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
                    'description' => [
                        'label' => 'Description'
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
            $this->initForm($entryId);
        }

        $this->formConfig = $this->buildFormConfig();

        parent::__construct();

        //This enable the default values to be populated in the backend
        \Event::listen('backend.form.extendFields', function($widget) {
            // This should reference your controller
            $controller = $widget->getController();
            if (!$controller instanceof \Mey\Channels\Controllers\Entries) {
                return;
            }

            $fields = $controller->needsDefault;
            if (!empty($fields)) {
                foreach ($controller->needsDefault as $field => $value) {
                    $widget->allFields[$field]->value = $value;
                }
            }
        });

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
                            'data-input-preset-type' => 'camel',
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

    public function initForm($id)
    {
        $entry = Entry::with('fields', 'channel', 'channel.fields')->where('id', '=', $id)->first();
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
            }
            if (!empty($entryFields)) {
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
        $entryId = Request::segment(6);
        $entry = Entry::find($entryId);
        $entryFieldValues = $inputs['entryField'];
        unset($inputs['entryField']);
        foreach ($inputs as $property => $value) {
            $entry->$property = $value;
        }

        $entry->save();

        foreach ($entryFieldValues as $shortName => $values) {
            foreach ($values as $key => $value) {
                if ($key === 'new') {
                    $fieldId = array_keys($value)[0];
                    $fieldValue = array_shift($value);
                    $field = Field::find($fieldId);
                    $entryField = EntryField::create([
                            'field_id' => $field->id,
                            'entry_id' => $entryId,
                            'value' => $fieldValue
                        ]
                    );
                } else {
                    $entryField = EntryField::find($key);
                    $entryField->value = $value;
                }
                $entryField->save();
            }
        }

        \Flash::success('Entry Fields Saved Successfully');
    }
}
