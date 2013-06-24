<?php

namespace JSomerstone\Feikki\TupasBundle\Tests\Model;

use JSomerstone\Feikki\TupasBundle\Model\TupasRequest;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TupasRequestTest extends WebTestCase
{
    protected $request;

    public  function setUp()
    {
        $this->request = new TupasRequest();
    }

    public function invalidVersionProvider()
    {
        return array(
            'empty' => array(''),
        );
    }

    /**
     * @test
     * @dataProvider invalidVersionProvider
     * @expectedException Symfony\Component\Validator\Exception\InvalidArgumentException
     */
    public function setVersionThrowsExceptionOnInvalid($invalidVersion)
    {
       $this->request->setVersion($invalidVersion);
    }
}
