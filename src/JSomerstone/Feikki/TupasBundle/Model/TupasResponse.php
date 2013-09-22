<?php

namespace JSomerstone\Feikki\TupasBundle\Model;

use \InvalidArgumentException;

class TupasResponse extends TupasBase
{
    private $version;
    private $responseTimestamp;
    private $responseNumber;
    private $stamp;
    private $keyVersion;
    private $algorithm;
    private $customerId;
    private $customerType;
    private $idType;
    private $userId;
    private $userName;
    private $mac;
    private $secret;

    private $parameterMapping = [
        'B02K_VERS' => 'version',
        'B02K_TIMESTMP' => 'responseTimestamp',
        'B02K_IDNBR' => 'responseNumber',
        'B02K_STAMP' => 'stamp',
        'B02K_CUSTNAME' => 'customer',
        'B02K_KEYVERS' => 'keyVersion',
        'B02K_ALG' => 'algorithm',
        'B02K_CUSTID' => 'customerId',
        'B02K_CUSTTYPE' => 'customerType',
        'B02K_USERID' => 'userId',
        'B02K_USERNAME' => 'userName',
        'B02K_MAC' => 'mac',
    ] ;

    public function __construct($GET)
    {
        foreach ($GET as $getParam => $value)
        {
            if (isset($this->parameterMapping[$getParam])) {
                $this->$this->parameterMapping[$getParam] = $value;
            }
        }
    }

    public function setSecret($secret)
    {
        $this->secret = $secret;
        return $this;
    }

    public function getBankPrefix()
    {
        return substr($this->responseTimestamp, 0, 3);
        $mapping = [
            '310' => 'handelsbanken',
            '200' => 'nordea',
            '500' => 'osuuspankki',
            '390' => 's-pankki',
            '800' => 'sampo',
            '400' => 'aktia',
            '360' => 'tapiola',
            '600' => 'ålandsbanken',
        ];
    }
}