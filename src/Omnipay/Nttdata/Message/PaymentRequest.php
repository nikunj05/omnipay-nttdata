<?php
namespace Omnipay\Nttdata\Message;

use DateTime;
use Omnipay\Common\Message\AbstractRequest;

/**
 * NTT Data Payment Request
 *
 * @author    Nikunj Goriya <nikunj.goriya@payrexx.com>
 * @copyright 2013-2014 Cherry Ltd.
 * @license   http://opensource.org/licenses/mit-license.php MIT
 * @version   2.0.0
 */
class PaymentRequest extends AbstractRequest
{
    protected $liveEndpoint = 'https://www.nttdpay.com/epayment/entry.asp';
    protected $testEndpoint = 'https://sandbox.nttdpay.com/epayment/entry.asp';

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
     * Get the RefNo.
     *
     * @return varchar RefNo
     */
    public function getRefNo()
    {
        return $this->getParameter('RefNo');
    }

    /**
     * Set the RefNo.
     *
     * @param varchar $value merchant code
     *
     * @return $this
     */
    public function setRefNo($value)
    {
        return $this->setParameter('RefNo', $value);
    }

    /**
     * Get the email address of the customer who is making the payment.
     *
     * @return string customer's email
     */
    public function getUserEmail()
    {
        return $this->getParameter('UserEmail');
    }

    /**
     * Set the email address of the customer who is making the payment.
     *
     * If left empty, the customer has to enter their email address.
     *
     * @param string $value customer's email
     *
     * @return $this
     */
    public function setUserEmail($value)
    {
        return $this->setParameter('UserEmail', $value);
    }

    /**
     * Get the payment amount.
     *
     * @return varchar
     */
    public function getAmount()
    {
        $amount = $this->getParameter('Amount');
        if ($amount !== null) {
            if (!is_float($amount) &&
                $this->getCurrencyDecimalPlaces() > 0 &&
                false === strpos((string) $amount, '.')) {
                throw new InvalidRequestException(
                    'Please specify amount as a string or float, ' .
                    'with decimal places (e.g. \'10.00\' to represent $10.00).'
                );
            }

            return $this->formatCurrency($amount);
        }
    }

    /**
     * Sets the payment amount.
     *
     * @param varchar $value
     * @return AbstractRequest Provides a fluent interface
     */
    public function setAmount($value)
    {
        return $this->setParameter('Amount', $value);
    }

    /**
     * Get the payment currency code.
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->getParameter('Currency');
    }

    /**
     * Sets the payment currency code.
     *
     * @param string $value
     * @return AbstractRequest Provides a fluent interface
     */
    public function setCurrency($value)
    {
        return $this->setParameter('Currency', strtoupper($value));
    }

    /**
     * Get the request product description.
     *
     * @return string
     */
    public function getProdDesc()
    {
        return $this->getParameter('ProdDesc');
    }

    /**
     * Sets the request product description.
     *
     * @param string $value
     * @return AbstractRequest Provides a fluent interface
     */
    public function setProdDesc($value)
    {
        return $this->setParameter('ProdDesc', $value);
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
     * Get the 2-letter code of the language used for NTT Data's pages.
     *
     * @return string language
     */
    public function getLang()
    {
        return $this->getParameter('Lang');
    }

    /**
     * Set the 2-letter code of the language used for NTT Data's pages.
     *
     * Can be any of EN, DE, ES, FR, IT, PL, GR, RO, RU, TR, CN, CZ, NL, DA, SV or FI.
     *
     * @param string $value language
     *
     * @return $this
     */
    public function setLang($value)
    {
        return $this->setParameter('Lang', $value);
    }

    /**
     * Get the request Remark.
     *
     * @return string
     */
    public function getRemark()
    {
        return $this->getParameter('Remark');
    }

    /**
     * Sets the request Remark.
     *
     * @param string $value
     * @return AbstractRequest Provides a fluent interface
     */
    public function setRemark($value)
    {
        return $this->setParameter('Remark', $value);
    }

    /**
     * Get the URL of the logo which you would like to appear at the top of the Nttdata
     * page.
     *
     * @return string logo url
     */
    public function getLogoUrl()
    {
        return $this->getParameter('logoUrl');
    }

    /**
     * Set the URL of the logo which you would like to appear at the top of the Nttdata
     * page.
     *
     * The logo must be accessible via HTTPS or it will not be shown. For best results
     * use logos with dimensions up to 200px in width and 50px in height.
     *
     * @param string $value logo url
     *
     * @return $this
     */
    public function setLogoUrl($value)
    {
        return $this->setParameter('logoUrl', $value);
    }

    /**
     * Get the payment id of selected payment method.
     *
     * @return integer payment id
     */
    public function getPaymentId()
    {
        return $this->getParameter('PaymentId');
    }

    /**
     * Set he payment id of selected payment method.
     *
     * @param integer $value payment id
     *
     * @return $this
     */
    public function setPaymentId($value)
    {
        return $this->setParameter('PaymentId', $value);
    }

    /**
     * Get the request Backend URL.
     *
     * @return string
     */
    public function getBackendURL()
    {
        return $this->getParameter('BackendURL');
    }

    /**
     * Sets the request Backend URL.
     *
     * @param string $value
     * @return AbstractRequest Provides a fluent interface
     */
    public function setBackendURL($value)
    {
        return $this->setParameter('BackendURL', $value);
    }

    /**
     * Get the customer's name.
     *
     * @return string customer's name
     */
    public function getUserName()
    {
        return $this->getParameter('UserName');
    }

    /**
     * Set the customer's first name.
     *
     * @param string $value customer's first name
     *
     * @return $this
     */
    public function setUserName($value)
    {
        return $this->setParameter('UserName', $value);
    }

    /**
     * Get the customer's address. (e.g. town)
     *
     * @return string customer's address
     */
    public function getCustomerAddress2()
    {
        return $this->getParameter('customerAddress2');
    }

    /**
     * Set the customer's address. (e.g. town)
     *
     * @param string $value customer's address
     *
     * @return $this
     */
    public function setCustomerAddress2($value)
    {
        return $this->setParameter('customerAddress2', $value);
    }

    /**
     * Get the customer's phone number.
     *
     * @return string customer's phone
     */
    public function getUserContact()
    {
        return $this->getParameter('UserContact');
    }

    /**
     * Set the customer's phone number.
     *
     * Only numeric values are accepted.
     *
     * @param string $value customer's phone
     *
     * @return $this
     */
    public function setUserContact($value)
    {
        return $this->setParameter('UserContact', $value);
    }

    /**
     * Get the Signature.
     *
     * @return varchar customer's phone
     */
    public function getSignature()
    {
        return $this->getParameter('Signature');
    }

    /**
     * Set the Signature.
     *
     *
     * @param varchar $value Signature
     *
     * @return $this
     */
    public function setSignature($value)
    {
        $merchantKey = $this->getMerchantKey();
        $merchantCode = $this->getMerchantCode();
        $refNo = $this->getRefNo();
        $amount = preg_replace('/[.,]/', '', $this->getAmount());
        $currency = $this->getCurrency();
        $signatureData = array($merchantKey, $merchantCode, $refNo, $amount, $currency);
        $signatureDataHash = implode("", $signatureData);
        
        $value = sha1($signatureDataHash);

        return $this->setParameter('Signature', $value);
    }

    /**
     * Get the Token Id.
     *
     * @return varchar Token Id
     */
    public function getTokenId()
    {
        return $this->getParameter('TokenId');
    }

    /**
     * Set the Token Id.
     *
     *
     * @param varchar $value Token Id
     *
     * @return $this
     */
    public function setTokenId($value)
    {
        return $this->setParameter('TokenId', $value);
    }

    /**
     * Get the Type of action for card tokenization
     *
     * @return string action type
     */
    public function getActionType()
    {
        return $this->getParameter('ActionType');
    }

    /**
     * Set the Type of action for card tokenization
     *
     * @param  string $value action type
     *
     * @return self
     */
    public function setActionType(array $value)
    {
        return $this->setParameter('ActionType', $value);
    }

    /**
     * Get the data for this request.
     *
     * @return array request data
     */
    public function getData()
    {
        // make sure we have the mandatory fields
        $this->validate('MerchantCode', 'RefNo', 'UserEmail', 'Amount', 'Currency', 'ProdDesc', 'UserName', 'UserContact', 'Signature', 'ResponseURL', 'BackendURL');

        // merchant details
        $data['MerchantCode'] = $this->getMerchantCode();
        $data['Lang'] = $this->getLang();
        $data['MerchantLogo'] = $this->getLogoUrl();
        $data['Remark'] = $this->getRemark();
        $data['Signature'] = $this->getSignature();
        
        // customer details
        $data['UserEmail'] = $this->getUserEmail();
        $data['UserName'] = $this->getUserName();
        $data['UserContact'] = $this->getUserContact();
        
        // payment details
        $data['Amount'] = $this->getAmount();
        $data['Currency'] = $this->getCurrency();
        $data['ProdDesc'] = $this->getProdDesc();
        $data['PaymentId'] = $this->getPaymentId();
        $data['ActionType'] = $this->getActionType();
        $data['ResponseURL'] = $this->getResponseURL();
        $data['BackendURL'] = $this->getBackendURL();
        
        return $data;
    }

    /**
     * @param  array $data payment data to send
     *
     * @return PaymentResponse         payment response
     */
    public function sendData($data)
    {
        $httpResponse = $this->httpClient->post($this->getEndpoint(), null, $data)->send();
        
        return $this->response = new PaymentResponse($this, $httpResponse);
    }

    /**
     * Get the endpoint for this request.
     *
     * @return string endpoint
     */
    public function getEndpoint()
    {
        return $this->getTestMode() ? $this->liveEndpoint : $this->testEndpoint;
    }
}
