<?php

namespace Mey\Channels\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use Mey\Channels\Models\Channel;

class Channels extends Controller
{

    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController'
    ];

    public $bodyClass = 'compact-container';

    public $formConfig = [
        'name' => 'Channels',
        'form' =>  [
            'fields' => [
                'name' => [
                    'label' => 'Name',
                    'placeholder' => 'Channel Name',
                    'span' => 'left'
                ],
                'short_name' => [
                    'label' => 'Short Name',
                    'span' => 'right',
                    'placeholder' => 'Channel Short Name',
                    'attributes' => [
                        'data-input-preset' => 'input[name="Channel[name]"]',
                        'data-input-preset-type' => 'camel',
                        'data-input-preset-closest-parent' => 'form',
                    ]
                ],
                'toolbar' => [
                    'type' => 'partial',
                    'path' => 'channel_toolbar',
                    'cssClass' => 'collapse-visible',
                ]
            ],
            'secondaryTabs' => [
                'stretch' => true,
                'fields' => [
                    'description' => [
                        'label' => 'Description',
                        'tab' => 'Manage',
                        'span' => 'left',
                        'type' => 'textarea',
                    ],
                    'channelType' => [
                        'label' => 'Channel Type',
                        'tab' => 'Manage',
                        'span' => 'right',
                        'type' => 'relation',
                        'comment' => 'This can not be changed, choose wisely.',
                        'context' => 'create',
                    ],
                    'fields' => [
                        'label' => 'Channel Fields',
                        'tab' => 'Manage',
                        'span' => 'left',
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
                    'type' => [
                        'label' => 'Type',
                        'relation' => 'channelType',
                        'select' => 'mey_channel_types.name',
                    ],
                    'fields' => [
                        'label' => 'Fields',
                        'relation' => 'fields',
                        'select' => 'mey_fields.name',
                    ],
                    //'create_button' => [
                    //    'label' => 'New Entry',
                    //    'type' => 'partial',
                    //    'path' => 'channel_toolbar',
                    //],
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
        $this->addCss('/plugins/mey/channels/assets/css/mey.channels.main.css');
    }

    public function index()
    {
        $this->channels = Channel::with('channelType')->paginate(15);
    }
    public function entries($slug = '')
    {
        $this->channel = Channel::where('short_name', '=', $slug)->first();
        $this->entries = $this->channel->entries()->paginate(15);
    }
}
