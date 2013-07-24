<?php
namespace JSomerstone\Feikki\TupasBundle\Model;

use \InvalidArgumentException;
use \LogicException;

class TupasValidator extends TupasBase
{
    /**
     * Validates TUPAS authentication request
     * @param array $post All POST-parameters from request
     * @param string $sharedSecret Shared secret used to calculate MAC
     * @throws \InvalidArgumentException
     */
    public function validateTupasRequest($post, $sharedSecret)
    {
        $this->checkRequiredForRequest($post);
        $this->checkMac($post, $sharedSecret);
        return true;
    }

    /**
     * Check that all requred parameters are present for TUPAS request
     * @param array $post
     * @throws \InvalidArgumentException
     */
    private function checkRequiredForRequest($post)
    {
        $requestedParameters = array(
            'A01Y_ACTION_ID',
            'A01Y_VERS',
            'A01Y_RCVID',
            'A01Y_LANGCODE',
            'A01Y_STAMP',
            'A01Y_IDTYPE',
            'A01Y_RETLINK',
            'A01Y_CANLINK',
            'A01Y_REJLINK',
            'A01Y_KEYVERS',
            'A01Y_ALG',
            'A01Y_MAC',
        );
        $missing = array();
        foreach ($requestedParameters as $param) {
            if (!isset($post[$param])) {
                $missing[] = $param;
            }
        }
        if (!empty($missing)) {
            throw new InvalidArgumentException(
                'Missing required parameter(s): ' . implode(', ', $missing)
            );
        }
        return;
    }

    /**
     * Check that Tupas requests MAC was calculated correctly
     * @param array $post
     * @param string $secret
     * @throws \LogicException
     */
    private function checkMac($post, $secret)
    {
        $algorithm = $this->algorithmForCode($post['A01Y_ALG']);
        $this->setAlgorithm($algorithm);
        $expectedMac = $this->calculateMac(
            $post['A01Y_ACTION_ID'],
            $post['A01Y_VERS'],
            $post['A01Y_RCVID'],
            $post['A01Y_LANGCODE'],
            $post['A01Y_STAMP'],
            $post['A01Y_IDTYPE'],
            $post['A01Y_RETLINK'],
            $post['A01Y_CANLINK'],
            $post['A01Y_REJLINK'],
            $post['A01Y_KEYVERS'],
            $post['A01Y_ALG'],
            $secret
        );

        if ($expectedMac !== $post['A01Y_MAC']) {
            throw new LogicException(
                'MAC-checksum mismatched - using algoritm '.$algorithm
            );
        }
        return;
    }
}
