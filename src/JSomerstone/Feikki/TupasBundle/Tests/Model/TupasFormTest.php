<?php

namespace JSomerstone\Feikki\TupasBundle\Tests\Model;

use JSomerstone\Feikki\TupasBundle\Model\TupasForm;
use JSomerstone\Feikki\TupasBundle\Model\TupasRequest;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @group model
 */
class TupasFormTest extends WebTestCase
{
    protected $form;

    public  function setUp()
    {
        $request = new TupasRequest();
        $this->form = new TupasForm($request, 'dev/null');
    }

    /**
     * @test
     */
    public function getRequestReturnsTupasRequest()
    {
        $this->assertInstanceOf(
            'JSomerstone\Feikki\TupasBundle\Model\TupasRequest',
            $this->form->getRequest()
        );
    }

    /**
     * @test
     */
    public function getUrlReturnsUrlSetInConstructor()
    {
        $this->assertEquals(
            'dev/null',
            $this->form->getUrl()
        );
    }

    /**
     * @test
     */
    public function buttonUrlCanBeSetAndGet()
    {
        $this->form->setButtonUrl('feikki.fi/fuubar.png');
        $this->assertEquals(
            'feikki.fi/fuubar.png',
            $this->form->getButtonUrl()
        );
    }

    /**
     * @test
     */
    public function buttonTextCanBeSetAndGet()
    {
        $this->form->setButtonText('Facebank');
        $this->assertEquals(
            'Facebank',
            $this->form->getButtonText()
        );
    }
}