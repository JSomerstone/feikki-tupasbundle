<?php

namespace JSomerstone\Feikki\TupasBundle\Model;

use \InvalidArgumentException;

class TupasForm
{
    /**
     *
     * @var TuåasRequest
     */
    private $request;

    private $formUrl;
    private $buttonUrl;
    private $buttonText;


    /**
     *
     * @param \JSomerstone\Feikki\TupasBundle\Model\TupasRequest $request
     * @param string $formUrl URL to submit TUPAS request to
     */
    public function __construct(TupasRequest $request, $formUrl)
    {
        $this->request = $request;
        $this->formUrl = $formUrl;
    }

    /**
     *
     * @return TupasRequest
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     *
     */
    public function getUrl()
    {
        return $this->formUrl;
    }

    /**
     * Sets URL of (the picture of) forms submit-button
     * @param string $buttonUrl
     */
    public function setButtonUrl($buttonUrl)
    {
        $this->buttonUrl = $buttonUrl;
        return $this;
    }

    public function getButtonUrl()
    {
        return $this->buttonUrl;
    }

    /**
     * Sets Text of forms submit-button
     * @param string $buttonText
     */
    public function setButtonText($buttonText)
    {
        $this->buttonText = $buttonText;
        return $this;
    }

    public function getButtonText()
    {
        return $this->buttonText;
    }
}