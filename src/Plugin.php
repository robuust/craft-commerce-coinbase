<?php

namespace robuust\coinbase;

use craft\commerce\services\Gateways;
use craft\events\RegisterComponentTypesEvent;
use robuust\coinbase\gateways\Gateway;
use yii\base\Event;

/**
 * Coinbase integration plugin.
 */
class Plugin extends \craft\base\Plugin
{
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        Event::on(Gateways::class, Gateways::EVENT_REGISTER_GATEWAY_TYPES, function (RegisterComponentTypesEvent $event) {
            $event->types[] = Gateway::class;
        });
    }
}
