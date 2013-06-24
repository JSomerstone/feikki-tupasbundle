<?php

namespace JSomerstone\Feikki\TupasBundle\Model;

use Symfony\Component\Validator\Exception\InvalidArgumentException;

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
    private $keyVersion;
    private $algorithm;
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
}