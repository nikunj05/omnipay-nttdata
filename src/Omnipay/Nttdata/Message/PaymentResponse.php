<?php
namespace Omnipay\Nttdata\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RedirectResponseInterface;

/**
 * Nttdata Payment Response
 *
 * This is the associated response to our PaymentRequest where we get Nttdata's session,
 * and thus the URL to where we shall redirect users to the payment page.
 *
 * @author    Nikunj Goriya <nikunj.goriya@payrexx.com>
 * @copyright 2013-2014 Cherry Ltd.
 * @license   http://opensource.org/licenses/mit-license.php MIT
 * @version   2.0.0
 */
class PaymentResponse extends AbstractResponse implements RedirectResponseInterface
{
    /**
     * @return false
     */
    public function isSuccessful()
    {
        return false;
    }

    public function isRedirect()
    {
        return $this->getSessionId() !== null;
    }

    /**
     * @return string redirect url
     */
    public function getRedirectUrl()
    {
        return $this->getRequest()->getEndpoint();
    }

    /**
     * @return string redirect method
     */
    public function getRedirectMethod()
    {
        return 'GET';
    }

    /**
     * @return null
     */
    public function getRedirectData()
    {
        return null;
    }

    public function getMessage() 
    {
        return $this->data->ErrDesc;
    }

    public function getStatus()
    {
       return $this->data->Status;
    }
}
