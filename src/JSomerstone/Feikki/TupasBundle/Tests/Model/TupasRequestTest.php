<?php

namespace JSomerstone\Feikki\TupasBundle\Tests\Model;

use JSomerstone\Feikki\TupasBundle\Model\TupasRequest;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @group model
 */
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
     * @expectedException \InvalidArgumentException
     */
    public function setVersionThrowsExceptionOnInvalid($invalidVersion)
    {
       $this->request->setVersion($invalidVersion);
    }

    public function setterAndGetterProvider()
    {
        return array(
            //[NameOfProperty, validValue]
            array('ServiceProvider',    'totallyfakeone'),
            array('Version',            1),
            array('Language',           'FI'),
            array('IdType',             '02'),
            array('ReturnLink',         'http://feikki.fi/dev/null'),
            array('CancelLink',         'http://feikki.fi/dev/null'),
            array('RejectedLink',       'http://feikki.fi/dev/null'),
            array('KeyVersion',         1234),
            array('Algorithm',          'md5'),
        );

    }

    /**
     *
     * @test
     * @dataProvider setterAndGetterProvider
     */
    public function setterAndGetterWorks($property, $validValue)
    {
        $setter = "set$property";
        $getter = "get$property";
        $this->assertInstanceOf(
            'JSomerstone\Feikki\TupasBundle\Model\TupasRequest',
            $this->request->$setter($validValue)
        );
        $this->assertEquals(
            $validValue,
            $this->request->$getter()
        );
    }

    /**
     * @test
     */
    public function setRequestIdSetsStamp()
    {
        $this->request->setRequestId(123);
        $timestamp = date('Ymdhis');
        $this->assertSame($timestamp . '000123', $this->request->getStamp());

        $this->assertRegExp(
            '/^20[\d]{2}[01]\d[0-3]\d[0-2]\d[0-6]\d[0-6]\d000123$/',
            $this->request->getStamp()
        );
    }

    /**
     * @test
     */
    public function hashCalculationWorks()
    {
        $action = '701';
        $version = '2';
        $serviceProvider = 'feikkidotfi';
        $language = 'FI';
        $requestId = '667';
        $stamp = date('Ymdhis') . "000$requestId";
        $idType = '01';
        $returnLink = 'http://feikki.fi/dev/null';
        $cancelLink = 'http://feikki.fi/dev/null';
        $rejectedLink = 'http://feikki.fi/dev/null';
        $keyVersion = 1;
        $algcode = '03'; // = sha-256
        $secret = 'S3cr370fF31kk1dotf1';

        $this->request->setVersion($version)
            ->setServiceProvider($serviceProvider)
            ->setLanguage($language)
            ->setRequestId($requestId)
            ->setIdType($idType)
            ->setReturnLink($returnLink)
            ->setCancelLink($cancelLink)
            ->setRejectedLink($rejectedLink)
            ->setKeyVersion($keyVersion)
            ->setAlgorithm('sha256')
            ->setSecret($secret);

        $string = "$action&000$version&$serviceProvider&$language&$stamp&$idType&$returnLink&$cancelLink&$rejectedLink"
                . "&000$keyVersion&$algcode&$secret&";
        $expectedHash = hash('sha256', $string);

        $this->assertEquals($expectedHash, $this->request->getMac());
    }
}
