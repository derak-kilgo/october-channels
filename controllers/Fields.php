<?php

namespace Mey\Channels\Controllers;

use BackendMenu;
use Backend\Classes\Controller;

class Fields extends Controller
{

    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController',
    ];

    public $bodyClass = 'compact-container';

    public $formConfig = [
        'name' => 'Fields',
        'form' =>  [
            'fields' => [
                'name' => [
                    'label' => 'Name',
                    'placeholder' => 'Field Name',
                    'span' => 'left'
                ],
                'short_name' => [
                    'label' => 'Short Name',
                    'placeholder' => 'Field Short Name',
                    'span' => 'right',
                    'attributes' => [
                        'data-input-preset' => 'input[name="Field[name]"]',
                        'data-input-preset-type' => 'camel',
                        'data-input-preset-closest-parent' => 'form',
                    ]
                ],
                'toolbar' => [
                    'type' => 'partial',
                    'path' => 'field_toolbar',
                    'cssClass' => 'collapse-visible',
                ],
            ],
            'secondaryTabs' => [
                'fields' => [
                    'fieldType' => [
                        'label' => 'Field Type',
                        'tab' => 'Manage',
                        'span' => 'left',
                        'type' => 'relation',
                    ],
                    'description' => [
                        'label' => 'Description',
                        'tab' => 'Manage',
                        'span' => 'right',
                        'type' => 'textarea',
                    ],
                ],
            ],
        ],
        'modelClass' => 'Mey\Channels\Models\Field',

        'defaultRedirect' => 'mey/channels/fields',
        'create' => [
            'redirect' => 'mey/channels/fields/update/:id',
            'redirectClose' => 'mey/channels/fields',
        ],
        'update' => [
            'redirect' => 'mey/channels/fields',
            'redirectClose' => 'mey/channels/fields'
        ]
    ];

    public $listConfig = [
        'list' => [
            'list' => [
                'columns' => [
                    'name' => [
                        'label' => 'Name'
                    ],
                    'fieldType' => [
                        'label' => 'Field Type',
                        'relation' => 'fieldType',
                        'select' => 'mey_field_types.name',
                    ],
                    'description' => [
                        'label' => 'Description'
                    ],
                    'short_name' => [
                        'label' => 'Short Name'
                    ],
                ],
            ],
            'modelClass' => 'Mey\Channels\Models\Field',
            'title' => 'Manage Fields',
            'recordUrl' => 'mey/channels/fields/update/:id',
            'noRecordsMessage' => 'backend::lang.list.no_records',
            'recordsPerPage' => 10,
            'toolbar' => [
                'buttons' => 'list_toolbar',
                'search' => [
                    'prompt' => 'backend::lang.list.search_prompt'
                ]
            ]
        ]
    ];

    public function getFieldTypeOptions()
    {
        return ['helo' => 'test'];
    }

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Mey.Channels', 'channels', 'fields');
        $this->addCss('/plugins/mey/channels/assets/css/mey.channels.main.css');
    }

}
