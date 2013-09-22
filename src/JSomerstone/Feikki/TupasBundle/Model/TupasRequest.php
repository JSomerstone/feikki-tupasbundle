<?php

namespace JSomerstone\Feikki\TupasBundle\Model;

use \InvalidArgumentException;

class TupasRequest extends TupasBase
{
    private $supportedLaguages = array(
        'FI', 'EN', 'SV'
    );

    private $version;
    private $serviceProvider;
    private $language;
    private $stamp;
    private $idType;
    private $returnLink;
    private $cancelLink;
    private $rejectionLink;
    private $keyVersion = 1;
    private $secret;

    /**
     *
     * @param int $version
     * @return \JSomerstone\Feikki\TupasBundle\Model\TupasRequest
     * @throws InvalidArgumentException
     */
    public function setVersion($version){
        if (!preg_match('/^[\d]{1,4}$/', $version)) {
            throw new InvalidArgumentException(
                'Version (A01Y_VERS) must be numeric 1-4 characters long'
            );
        }
        $this->version = str_pad($version, 4, '0', STR_PAD_LEFT);
        return $this;
    }

    public function getVersion(){
        return $this->version;
    }

    /**
     *
     * @param string $serviceProvider
     * @return \JSomerstone\Feikki\TupasBundle\Model\TupasRequest
     * @throws InvalidArgumentException
     */
    public function setServiceProvider($serviceProvider)
    {
        if (!preg_match('/^[\da-zA-Z]{10,15}$/', $serviceProvider)) {
            throw new InvalidArgumentException(
                'Service Provider (A01Y_RCVID) must be 10-15 chars alfa-numeric'
            );
        }
        $this->serviceProvider = $serviceProvider;
        return $this;
    }

    public function getServiceProvider(){
        return $this->serviceProvider;
    }

    /**
     * Set language-code of tupas requst, either FI, SV or EN
     * Case sensitive
     * @param string $language
     * @return \JSomerstone\Feikki\TupasBundle\Model\TupasRequest
     * @throws InvalidArgumentException
     */
    public function setLanguage($language)
    {
        if (!in_array($language, $this->supportedLaguages)) {
            throw new InvalidArgumentException(
                'Supported languages (A01Y_LANGCODE) are : ' . implode(', ', $this->supportedLaguages)
            );
        }
        $this->language = $language;
        return $this;
    }

    public function getLanguage(){
        return $this->language;
    }

    /**
     *
     * @param string $requestID
     * @return \JSomerstone\Feikki\TupasBundle\Model\TupasRequest
     * @throws InvalidArgumentException
     */
    public function setRequestId($requestID){
        if (!preg_match('/^[\da-z]{1,6}$/i', $requestID)) {
            throw new InvalidArgumentException(
                'Request ID (A01Y_STAMP) is format YyyyMMddhhmmssxxxxxx'
            );
        }
        $this->stamp = sprintf(
            '%s%s',
            date('Ymdhis'),
            str_pad($requestID, 6, '0', STR_PAD_LEFT)
        );
        return $this;
    }

    public function getStamp(){
        return $this->stamp;
    }

    /**
     *
     * @param string $idType
     * @return \JSomerstone\Feikki\TupasBundle\Model\TupasRequest
     * @throws InvalidArgumentException
     */
    public function setIdType($idType)
    {
        if (!preg_match('/^[0-4][123]$/', $idType)) {
            throw new InvalidArgumentException(
                'ID type (A01Y_IDTYPE) must be two digits'
            );
        }
        $this->idType = $idType;
        return $this;
    }

    public function getIdType(){
        return $this->idType;
    }

    /**
     *
     * @param string $link URL to return from TUPAS-service provider
     * @return \JSomerstone\Feikki\TupasBundle\Model\TupasRequest
     */
    public function setReturnLink($link)
    {
        $this->returnLink = $link;
        return $this;
    }

    public function getReturnLink()
    {
        return $this->returnLink;
    }

    /**
     *
     * @param string $link URL to return on Cancel
     * @return \JSomerstone\Feikki\TupasBundle\Model\TupasRequest
     */
    public function setCancelLink($link)
    {
        $this->cancelLink = $link;
        return $this;
    }

    public function getCancelLink()
    {
        return $this->cancelLink;
    }

    /**
     *
     * @param string $link URL to return on rejection
     * @return \JSomerstone\Feikki\TupasBundle\Model\TupasRequest
     */
    public function setRejectedLink($link)
    {
        $this->rejectionLink = $link;
        return $this;
    }

    public function getRejectedLink()
    {
        return $this->rejectionLink;
    }

    /**
     *
     * @param int $version
     * @return \JSomerstone\Feikki\TupasBundle\Model\TupasRequest
     */
    public function setKeyVersion($version)
    {

        $this->keyVersion = str_pad($version, 4, '0', STR_PAD_LEFT);
        return $this;
    }

    public function getKeyVersion()
    {
        return $this->keyVersion;
    }

    /**
     * Set the pre-shared secter
     * @param string $secret
     * @return \JSomerstone\Feikki\TupasBundle\Model\TupasRequest
     */
    public function setSecret($secret)
    {
        $this->secret = $secret;
        return $this;
    }

    /**
     *
     * @return string $mac
     */
    public function getMac()
    {
        return $this->calculateMac(
            self::ACTION_ID,
            $this->version,
            $this->serviceProvider,
            $this->language,
            $this->stamp,
            $this->idType,
            $this->returnLink,
            $this->cancelLink,
            $this->rejectionLink,
            $this->keyVersion,
            $this->codeForAlgorithm($this->algorithm),
            $this->secret
        );
    }

    /**
     * Get two-digit-presentation of given algorithm
     * See A01Y_ALG in TUPAS documentation
     * @return string
     */
    public function getAlgorithmCode()
    {
        return $this->codeForAlgorithm($this->algorithm);
    }
}