<?php

namespace JSomerstone\Feikki\TupasBundle\Model;

use \InvalidArgumentException;

class TupasRequest
{

    private $url;
    private $action = 701;
    private $version;
    private $serviceProvider;

    private $supportedLaguages = array(
        'FI', 'EN', 'SV'
    );
    private $language;
    private $stamp;
    private $idType;
    private $returnLink;
    private $cancelLink;
    private $rejectionLink;
    private $keyVersion = 1;
    private $algorithm = 'sha256';
    private $secret;
    private $mac;

    /**
     * Set banks URL
     * @param string $url URL to submit TUPAS request to
     * @return \JSomerstone\Feikki\TupasBundle\Model\TupasRequest
     */
    public function setUrl($url){
        $this->url = $url;
        return $this;
    }

    /**
     * Get banks URL
     * @return string $url
     */
    public function getUrl()
    {
        return $this->url;
    }

    public function setVersion($version){
        if (!preg_match('/^[\d]{1,4}$/', $version)) {
            throw new InvalidArgumentException(
                'Version (A01Y_VERS) must be numeric 1-4 characters long'
            );
        }
        $this->version = $version;
        return $this;
    }

    public function getVersion(){
        return $this->version;
    }

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

    public function setIdType($idType)
    {
        $this->idType = $idType;
        return $this;
    }

    public function getIdType(){
        return $this->idType;
    }

    public function setReturnLink($link)
    {
        $this->returnLink = $link;
        return $this;
    }

    public function getReturnLink()
    {
        return $this->returnLink;
    }

    public function setCancelLink($link)
    {
        $this->cancelLink = $link;
        return $this;
    }

    public function getCancelLink()
    {
        return $this->cancelLink;
    }

    public function setRejectedLink($link)
    {
        $this->rejectionLink = $link;
        return $this;
    }

    public function getRejectedLink()
    {
        return $this->rejectionLink;
    }

    public function setKeyVersion($version)
    {
        $this->keyVersion = $version;
        return $this;
    }

    public function getKeyVersion()
    {
        return $this->keyVersion;
    }

    public function setAlgorithm($alg)
    {
        $this->algorithm = $alg;
        return $this;
    }

    public function getAlgorithm()
    {
        return $this->algorithm;
    }

    public function setSecret($secret)
    {
        $this->secret = $secret;
        return $this;
    }

    public function getMac()
    {
        return $this->calculateMac();
    }

    private function calculateMac()
    {
        $array = array(
            $this->action,
            str_pad($this->version, 4, '0', STR_PAD_LEFT),
            $this->serviceProvider,
            $this->language,
            $this->stamp,
            $this->idType,
            $this->returnLink,
            $this->cancelLink,
            $this->rejectionLink,
            str_pad($this->keyVersion, 4, '0', STR_PAD_LEFT),
            self::codeForAlgorithm($this->algorithm),
            $this->secret,
            ''
        );
        $string = implode('&', $array);

        return hash($this->algorithm, $string);
    }

    /**
     * Get two-digit-presentation of given algorithm
     * See A01Y_ALG in TUPAS documentation
     * @return string
     */
    public function getAlgorithmCode()
    {
        return self::codeForAlgorithm($this->algorithm);
    }

    private static function codeForAlgorithm($algorithm)
    {
        switch ($algorithm)
        {
            default:
                throw new \InvalidArgumentException("Unsupported algorithm '$algorithm'");
            case 'md5':
                return '01';
            case 'sha1':
                return '02';
            case 'sha256':
                return '03';
        }
    }

}