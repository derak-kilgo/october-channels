<?php

namespace Mey\Channels\Controllers;

use BackendMenu;
use Backend\Classes\Controller;

class Channels extends Controller
{

    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController'
    ];

    public $formConfig = [
        'name' => 'Channels',
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
                        'data-input-preset' => 'input[name="Channel[name]"]',
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
            ],
            'secondaryTabs' => [
                'stretch' => true,
                'fields' => [
                    'fields' => [
                        'tab' => 'Fields',
                        'type' => 'relation',
                        'commentAbove' => 'Choose the fields belonging to this Channel',
                        'placeholder' => 'No Fields'
                    ],
                ]
            ]
        ],
        'modelClass' => 'Mey\Channels\Models\Channel',

        'defaultRedirect' => 'mey/channels/channels',
        'create' => [
            'redirect' => 'mey/channels/channels/update/:id',
            'redirectClose' => 'mey/channels/channels',
        ],
        'update' => [
            'redirect' => 'mey/channels/channels',
            'redirectClose' => 'mey/channels/channels'
        ]
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
                    'fields' => [
                        'label' => 'Fields',
                        'relation' => 'fields',
                        'select' => 'mey_fields.name',
                    ],
                ],
            ],
            'modelClass' => 'Mey\Channels\Models\Channel',
            'title' => 'Manage Channels',
            'recordUrl' => 'mey/channels/channels/update/:id',
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

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Mey.Channels', 'channels', 'channels');
    }
}
