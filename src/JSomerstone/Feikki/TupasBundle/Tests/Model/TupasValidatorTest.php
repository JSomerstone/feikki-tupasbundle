<?php

namespace JSomerstone\Feikki\TupasBundle\Tests\Model;

use JSomerstone\Feikki\TupasBundle\Model\TupasValidator;
use JSomerstone\Feikki\TupasBundle\Model\TupasForm;
use JSomerstone\Feikki\TupasBundle\Model\TupasRequest;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @group model
 * @group validator
 */
class TupasValidatorTest extends WebTestCase
{
    protected $validator;

    public $requestedParameters = array(
        'A01Y_ACTION_ID',
        'A01Y_VERS',
        'A01Y_RCVID',
        'A01Y_LANGCODE',
        'A01Y_STAMP',
        'A01Y_IDTYPE',
        'A01Y_RETLINK',
        'A01Y_CANLINK',
        'A01Y_REJLINK',
        'A01Y_KEYVERS',
        'A01Y_ALG',
        'A01Y_MAC',
    );

    public function setUp()
    {
        $this->validator = new TupasValidator();
    }

    public function provideRequestWithFieldMissing()
    {
        $invalidRequests = array();
        foreach ($this->requestedParameters as $i => $mandatory)
        {
            $requestFields = $this->requestedParameters;
            unset($requestFields[$i]);
            $fakePost = array_flip($requestFields);
            $invalidRequests[] = array($fakePost);
        }
        return $invalidRequests;
    }

    /**
     * @test
     * @dataProvider provideRequestWithFieldMissing
     */
    public function failingToProvideRequiredParameterThworsException($fakePost)
    {
        $this->setExpectedException('InvalidArgumentException');
        $this->validator->validateTupasRequest($fakePost, 'foobar');
    }

    /**
     * @test
     */
    public function macMiscalculationIsDetected()
    {
        $this->setExpectedException('LogicException');
        $sharedSecret = 'S3cr370fF31kk1dotf1';
        $invalidRequest = $this->getValidTupasRequest($sharedSecret);
        //Now let's make it invalid
        $invalidRequest['A01Y_MAC'] = str_shuffle($invalidRequest['A01Y_MAC']);

        $this->validator->validateTupasRequest($invalidRequest, $sharedSecret);
    }

    /**
     * @test
     */
    public function validRequestIsPassed()
    {
        $sharedSecret = 'S3cr370fF31kk1dotf1';
        $validRequest = $this->getValidTupasRequest($sharedSecret);
        $this->assertTrue(
            $this->validator->validateTupasRequest($validRequest, $sharedSecret)
        );
    }

    private function getValidTupasRequest($secret)
    {
        $validRequest = array(
            'A01Y_ACTION_ID' => '701',
            'A01Y_VERS' => '2',
            'A01Y_RCVID' => 'feikkidotfi',
            'A01Y_LANGCODE' => 'FI',
            'A01Y_STAMP' => date('Ymdhis') . uniqid(),
            'A01Y_IDTYPE' => '01',
            'A01Y_RETLINK' => 'http://feikki.fi/dev/null',
            'A01Y_CANLINK' => 'http://feikki.fi/dev/null',
            'A01Y_REJLINK' => 'http://feikki.fi/dev/null',
            'A01Y_KEYVERS' => 1,
            'A01Y_ALG' => '03', //' => sha-256
        );
        $hash = hash(
            'sha256',
            implode('&', $validRequest) . '&' . $secret . '&'
        );
        $validRequest['A01Y_MAC'] = $hash;
        return $validRequest;
    }
}