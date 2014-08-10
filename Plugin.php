<?php

namespace Mey\Channels;

use Backend;
use Controller;
use System\Classes\PluginBase;

/**
 * Channels Plugin Information File
 */
class Plugin extends PluginBase
{

    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'Channels',
            'description' => 'Add Channel/Field type resources to your application',
            'author'      => 'Jared Meyering',
            'icon'        => 'icon-plus-square-o'
        ];
    }

    public function registerComponents()
    {
        return [
            'Mey\Channels\Components\Channel' => 'channel',
            'Mey\Channels\Components\Entry' => 'entry',
        ];
    }


    public function registerNavigation()
    {
        return [
            'channels' => [
                'label'       => 'Channels',
                'url'         => Backend::url('mey/channels/channels'),
                'icon'        => 'icon-cube',
                'permissions' => ['channels.*'],
                'order'       => 212,
                'sideMenu' => [
                    'channels' => [
                        'label'       => 'Channels',
                        'icon'        => 'icon-cube',
                        'url'         => Backend::url('mey/channels/channels'),
                        'permissions' => ['channels.access_channels'],
                    ],
                    'fields' => [
                        'label'       => 'Fields',
                        'icon'        => 'icon-plus-square',
                        'url'         => Backend::url('mey/channels/fields'),
                        'permissions' => ['channels.access_fields'],
                    ],
                ],
            ],
        ];
    }

    public function registerFormWidgets() {
        return [
            'Mey\Channels\FormWidgets\Asset' => [
                'label' => 'Asset',
                'alias' => 'asset'
            ]
        ];
    }


}
