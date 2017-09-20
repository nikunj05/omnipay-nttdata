<?php
namespace Omnipay\Nttdata\Message;

use Omnipay\Common\Message\AbstractResponse;

/**
 * Nttdata Payment Response
 *
 * This is the associated response to our PaymentRequest where we get Nttdata's session,
 * and thus the URL to where we shall redirect users to the payment page.
 *
 * @author Joao Dias <joao.dias@cherrygroup.com>
 * @copyright 2013-2014 Cherry Ltd.
 * @license http://opensource.org/licenses/mit-license.php MIT
 * @version 6.5 Nttdata Payment Gateway Integration Guide
 */
class PaymentResponse extends AbstractResponse
{
    /**
     * @return false
     */
    public function isSuccessful()
    {
        return false;
    }
    /**
     * Get the skrill status of this response.
     *
     * @return string status
     */
    public function getStatus()
    {
        return (string) $this->data->getHeader('X-Nttdata-Status');
    }

    /**
     * Get the status message.
     *
     * @return string|null status message
     */
    public function getMessage()
    {
        $statusTokens = explode(':', $this->getStatus());
        return array_pop($statusTokens) ?: null;
    }

}
