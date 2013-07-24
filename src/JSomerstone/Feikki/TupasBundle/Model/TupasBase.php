<?php

namespace JSomerstone\Feikki\TupasBundle\Model;

use \InvalidArgumentException;

abstract class TupasBase
{
    const ACTION_ID = 701;

    /**
     *
     * @var string
     */
    protected $algorithm = 'sha256';

    protected $supportedAlgorithms = array(
        'md5' => '01',
        'sha1' => '02',
        'sha256' => '03',
    );

    /**
     * Calculates MAC-checksum from given arguments
     * Each calling class must know the order of arguments to correctly calculate MAC
     * @param string 1...n inputs to calculate MAC from
     * @return string
     */
    protected function calculateMac()
    {
        $arguments = func_get_args();
        $arguments[] = ''; //Workaround to get the $string to end with "&"

        $string = implode('&', $arguments);

        return hash($this->algorithm, $string);
    }

    /**
     * Setter for Algorithm, supported md5, sha1 and sha256
     * @param string $algorithm
     * @return \JSomerstone\Feikki\TupasBundle\Model\TupasBase
     * @throws InvalidArgumentException if given unsupported algorithm
     */
    public function setAlgorithm($algorithm)
    {
        if(!isset($this->supportedAlgorithms[$algorithm])) {
            throw new InvalidArgumentException("Unsupported hashing algorithm '$algorithm'");
        }
        $this->algorithm = $algorithm;
        return $this;
    }

    /**
     * Getter for algorithm
     * @return string
     */
    public function getAlgorithm()
    {
        return $this->algorithm;
    }

    /**
     * Returns two-digit presentation of given algorithm used by TUPAS
     * @param string $algorithm
     * @return string
     * @throws InvalidArgumentException if given unsupported algorithm
     */
    protected function codeForAlgorithm($algorithm)
    {
        if(!isset($this->supportedAlgorithms[$algorithm])) {
            throw new InvalidArgumentException("Unsupported hashing algorithm '$algorithm'");
        }
        return $this->supportedAlgorithms[$algorithm];
    }

    /**
     * Returns two-digit presentation of given algorithm used by TUPAS
     * @param string $code
     * @return string
     * @throws InvalidArgumentException if given unsupported algorithm
     */
    protected function algorithmForCode($code)
    {
        if(!in_array($code, $this->supportedAlgorithms)) {
            throw new InvalidArgumentException("Unsupported hashing algorithm code '$code'");
        }
        return array_search($code, $this->supportedAlgorithms);
    }
}