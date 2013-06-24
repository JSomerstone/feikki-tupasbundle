<?php

namespace JSomerstone\Feikki\TupasBundle\Model;

use \InvalidArgumentException;

class TupasRequest
{
    private $action = 701;
    private $version;
    private $serviceProvider;
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

    public function setVersion($version){
        if (!preg_match('/^[0-9]{1,4}$/', $version)) {
            throw new InvalidArgumentException('Version must be numeric 1-4 characters long');
        }
        $this->version = $version;
        return $this;
    }

    public function getVersion(){
        return $this->version;
    }

    public function setServiceProvider($serviceProvider)
    {
        $this->serviceProvider = $serviceProvider;
        return $this;
    }

    public function getServiceProvider(){
        return $this->serviceProvider;
    }

    public function setLanguage($language)
    {
        $this->language = $language;
        return $this;
    }

    public function getLanguage(){
        return $this->language;
    }

    public function setRequestId($requestID){
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