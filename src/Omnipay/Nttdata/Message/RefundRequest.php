<?php

namespace Omnipay\Nttdata\Message;

use Omnipay\Common\Message\ResponseInterface;

class RefundRequest extends AbstractRequest
{
    protected $liveRefundEndpoint = 'https://sandbox.nttdpay.com/ePayment/WebService/RefundAPI/RefundRequest.asmx';
    protected $testRefundEndpoint = 'https://sandbox.nttdpay.com/ePayment/WebService/RefundAPI/RefundRequest.asmx';

    /**
     * {@inheritdoc}
     */
    protected function getEndpoint()
    {
        return $this->getTestMode() ? $this->liveEndpoint : $this->testEndpoint;
    }

    public function getData()
    {
        $this->validate('MerchantCode', 'REFUND_CASH_AMOUNT', 'Signature', 'TransId');

        $data = array();
        $data['REFUND_CASH_AMOUNT'] = $this->getAmount();

        return $data;
    }

    public function sendData($data)
    {
        $url = $this->getEndpoint() . '?' . http_build_query($data);
        $httpResponse = $this->httpClient->get(html_entity_decode($url))->send();

        $xml = $httpResponse->xml();
        return $this->createResponse($xml);
    }
}
