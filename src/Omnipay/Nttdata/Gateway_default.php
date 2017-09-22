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
     * Get the merchant key of the merchant's NTT Data account.
     *
     * @return varchar merchant code
     */
    public function getMerchantKey()
    {
        return $this->getParameter('MerchantKey');
    }

    /**
     * Set the merchant key of the merchant's NTT Data account.
     *
     * @param varchar $value merchant code
     *
     * @return $this
     */
    public function setMerchantKey($value)
    {
        return $this->setParameter('MerchantKey', $value);
    }

    /**
     * Get the merchant code of the merchant's NTT Data account.
     *
     * @return varchar merchant code
     */
    public function getMerchantCode()
    {
        return $this->getParameter('MerchantCode');
    }

    /**
     * Set the merchant code of the merchant's NTT Data account.
     *
     * @param varchar $value merchant code
     *
     * @return $this
     */
    public function setMerchantCode($value)
    {
        return $this->setParameter('MerchantCode', $value);
    }

    /**
     * Get the URL to which the customer is returned once the payment is made.
     *
     * @return string return url
     */
    public function getResponseURL()
    {
        return $this->getParameter('ResponseURL');
    }

    /**
     * Set the URL to which the customer is returned once the payment is made.
     *
     * If this field is not filled, the Nttdata Gateway page closes automatically at the
     * end of the transaction and the customer is returned to the page on the merchant's
     * website from where they were redirected to Nttdata.
     *
     * @param string $value return url
     *
     * @return $this|\Omnipay\Common\Message\AbstractRequest
     */
    public function setResponseURL($value)
    {
        return $this->setParameter('ResponseURL', $value);
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
     * @param  array $parameters
     * @return \Omnipay\Nttdata\Message\RefundRequest
     */
    public function refund(array $parameters = [])
    {
        return $this->createRequest('Omnipay\Nttdata\Message\RefundRequest', $parameters);
    }
}
