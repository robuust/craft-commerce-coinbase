<?php

namespace robuust\coinbase\gateways;

use Craft;
use craft\commerce\models\payments\BasePaymentForm;
use craft\commerce\omnipay\base\OffsiteGateway;
use craft\commerce\Plugin;
use Omnipay\Coinbase\Gateway as OmnipayGateway;
use Omnipay\Common\AbstractGateway;

/**
 * Coinbase gateway.
 */
class Gateway extends OffsiteGateway
{
    // Properties
    // =========================================================================

    /**
     * @var string
     */
    public $apiKey;

    // Public Methods
    // =========================================================================

    /**
     * {@inheritdoc}
     */
    public static function displayName(): string
    {
        return Craft::t('commerce', 'Coinbase');
    }

    /**
     * {@inheritdoc}
     */
    public function getPaymentTypeOptions(): array
    {
        return [
            'purchase' => Craft::t('commerce', 'Purchase (Authorize and Capture Immediately)'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getSettingsHtml()
    {
        return Craft::$app->getView()->renderTemplate('commerce-coinbase/gatewaySettings', ['gateway' => $this]);
    }

    /**
     * {@inheritdoc}
     */
    public function populateRequest(array &$request, BasePaymentForm $paymentForm = null)
    {
        parent::populateRequest($request, $paymentForm);
        $request['name'] = $request['description'];
        $request['pricing_type'] = 'fixed_price';
        $request['redirect_url'] = $request['returnUrl'];
        $request['metadata'] = [];

        if (empty($request['code']) && !empty($request['commerceTransactionId'])) {
            $transaction = Plugin::getInstance()->transactions->getTransactionById($request['commerceTransactionId']);
            $request['code'] = $transaction->code;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = parent::rules();
        $rules[] = ['paymentType', 'compare', 'compareValue' => 'purchase'];

        return $rules;
    }

    // Protected Methods
    // =========================================================================

    /**
     * {@inheritdoc}
     */
    protected function createGateway(): AbstractGateway
    {
        /** @var OmnipayGateway $gateway */
        $gateway = static::createOmnipayGateway($this->getGatewayClassName());

        $gateway->setApiKey(Craft::parseEnv($this->apiKey));

        return $gateway;
    }

    /**
     * {@inheritdoc}
     */
    protected function getGatewayClassName()
    {
        return '\\'.OmnipayGateway::class;
    }
}
