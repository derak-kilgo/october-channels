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

    public $formConfig = [
        'name' => 'Fields',
        'form' =>  [
            'fields' => [
                'name' => [
                    'label' => 'Name',
                    'span' => 'left'
                ],
                'short_name' => [
                    'label' => 'Short Name',
                    'span' => 'right',
                    'attributes' => [
                        'data-input-preset' => 'input[name="Field[name]"]',
                        'data-input-preset-type' => 'slug',
                        'data-input-preset-closest-parent' => 'form',
                    ]
                ],
                'description' => [
                    'label' => 'Description',
                    'stretch' => true,
                    'type' => 'textarea',
                    'options' =>[
                        'fontSize' => 20,
                        'margin' => 15
                    ],
                ],
                'fieldType' => [
                    'label' => 'Field Type',
                    'stretch' => true,
                    'type' => 'relation',
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
    }

}
