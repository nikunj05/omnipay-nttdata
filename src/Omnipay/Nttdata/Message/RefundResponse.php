<?php

namespace Omnipay\Nttdata\Message;

class RefundResponse extends AbstractResponse
{
    public function getTransactionReference()
    {
        return $this->data['payment']['id'];
    }

    public function getTransactionId()
    {
        return $this->data['id'];
    }
}
