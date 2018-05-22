<?php

namespace Cloudipsp;

use PHPUnit\Framework\TestCase;

class VerificationTest extends TestCase
{
    private $minTestData = [
        'currency' => 'USD',
        'amount' => 1000,
    ];
    private $mid = 1396424;
    private $secret_key = 'test';
    private $request_types = ['json', 'xml', 'form'];

    private function setTestConfig()
    {
        \Cloudipsp\Configuration::setMerchantId($this->mid);
        \Cloudipsp\Configuration::setSecretKey($this->secret_key);
        \Cloudipsp\Configuration::setApiVersion('1.0');
    }

    /**
     * @throws Exception\ApiException
     */
    public function testVerificationUrl()
    {
        $this->setTestConfig();
        foreach ($this->request_types as $type) {
            \Cloudipsp\Configuration::setRequestType($type);
            $result = \Cloudipsp\Verification::url($this->minTestData)->getData();
            $this->validateUrlResult($result);
        }
    }

    private function validateUrlResult($result)
    {
        $this->assertNotEmpty($result['checkout_url'], 'checkout_url is empty');
        $this->assertNotEmpty($result['payment_id'], 'payment_id is empty');
        $this->assertEquals($result['response_status'], 'success');
    }
}
