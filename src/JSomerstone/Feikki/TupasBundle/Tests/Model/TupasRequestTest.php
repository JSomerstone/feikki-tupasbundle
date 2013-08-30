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
            'negative' => array(-1),
            'too long' => array('12345'),
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

    public function invalidServiceProviderProvider()
    {
        return array(
            'empty' => array(''),
            '1 char' => array('1'),
            '9 chars' => array('123456789'),
            'too long' => array('1234567890123456'),
        );
    }

    /**
     * @test
     * @dataProvider invalidServiceProviderProvider
     * @expectedException \InvalidArgumentException
     */
    public function setServiceProviderThowrsExceptionOnInvalid($provider)
    {
        $this->request->setServiceProvider($provider);
    }

    public function invalidLanguageProvider()
    {
        return array(
            'empty' => array(''),
            '1 char' => array('F'),
            'unsupported' => array('RU'),
            'too long' => array('FII'),
        );
    }

    /**
     * @test
     * @dataProvider invalidLanguageProvider
     * @expectedException \InvalidArgumentException
     */
    public function setLanguageThowrsExceptionOnInvalid($language)
    {
        $this->request->setLanguage($language);
    }

    public function invalidRequestIdProvider()
    {
        return array(
            'empty' => array(''),
            'Specials' => array('f@bår'),
            'too long' => array('1234567')
        );
    }

    /**
     * @test
     * @dataProvider invalidRequestIdProvider
     * @expectedException \InvalidArgumentException
     */
    public function setRequestIdThowrsExceptionOnInvalid($requestId)
    {
        $this->request->setRequestId($requestId);
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

    /**
     * @test
     */
    public function gettingAlgorithmCodeWorks()
    {
        $algs = array(
            'md5' => '01',
            'sha1' => '02',
            'sha256' => '03',
        );

        foreach ($algs as $algorithm => $expectedCode){
            $this->request->setAlgorithm($algorithm);

            $this->assertEquals(
                $expectedCode,
                $this->request->getAlgorithmCode(),
                "Algorithm '$algorithm' did not return expected alcorithm-code"
            );
        }
    }

    /**
     * @test
     */
    public function settingInvalidArgorithmThrowsException()
    {
        $this->setExpectedException('\InvalidArgumentException');
        $this->request->setAlgorithm('fuubar');
    }

    /**
     * @test
     */
    public function settingInvalidIdTypeThrowsException()
    {
        $this->setExpectedException('\InvalidArgumentException');
        $this->request->setIdType('fuubar');
    }
}
