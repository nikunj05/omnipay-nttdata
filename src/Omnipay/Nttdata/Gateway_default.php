<?php
namespace Omnipay\Nttdata;

use Omnipay\Common\AbstractGateway;

/**
 * NTT Data Gateway
 *
 * @author    Nikunj Goriya <nikunj.goriya@payrexx.com>
 * @copyright 2013-2014 Cherry Ltd.
 * @license   http://opensource.org/licenses/mit-license.php MIT
 * @version   2.0.0
 */
class Gateway extends AbstractGateway
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Nttdata';
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultParameters()
    {
        return [
            'merchantCode'      => '',
            'merchantKey'  => '',
            'testMode'   => false,
        ];
    }

    /**
     * Get the merchant's code.
     *
     * @return string code
     */
    public function getMerchantId()
    {
        return $this->getParameter('merchant_id');
    }

    /**
     * Set the merchant's code.
     *
     * @param string $value code
     *
     * @return self
     */
    public function setMerchantId($value)
    {
        return $this->setParameter('merchant_id', $value);
    }

    /**
     * Get the URL to which the transaction details will be posted after the payment
     * process is complete.
     *
     * @return string notify url
     */
    public function getNotifyUrl()
    {
        return $this->getParameter('notifyUrl');
    }

    /**
     * Set the URL to which the transaction details will be posted after the payment
     * process is complete.
     *
     * Alternatively you may specify an email address to which you would like to receive
     * the results. If the notify url is omitted, no transaction details will be sent to
     * the merchant.
     *
     * @param string $value notify url
     *
     * @return self
     */
    public function setNotifyUrl($value)
    {
        return $this->setParameter('notifyUrl', $value);
    }

    /**
     * Create a new charge.
     *
     * @param  array $parameters request parameters
     *
     * @return Message\PaymentResponse               response
     */
    public function purchase(array $parameters = [])
    {
        return $this->createRequest('Omnipay\Nttdata\Message\PaymentRequest', $parameters);
    }

    /**
     * Finalises a payment (callback).
     *
     * @param  array $parameters request parameters
     *
     * @return Message\PaymentResponse               response
     */
    public function completePurchase(array $parameters = [])
    {
        return $this->createRequest('Omnipay\Nttdata\Message\CompletePurchaseRequest', $parameters);
    }

    /**
     * Authorize and prepare a refund.
     *
     * @param  array $parameters request parameters
     *
     * @return Message\AuthorizeResponse               response
     */
    public function refund(array $parameters = [])
    {
        return $this->createRequest('Omnipay\Nttdata\Message\RefundRequest', $parameters);
    }
}
