<?php

namespace JSomerstone\Feikki\TupasBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JSomerstone\Feikki\TupasBundle\Model\TupasRequest;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('JSomerstoneFeikkiTupasBundle:Default:index.html.twig', array('name' => $name));
    }

    public function tupasFormAction()
    {
        $tupasRequest = new TupasRequest();
        $tupasRequest->setUrl('#')
            ->setVersion(1)
            ->setServiceProvider('JSomerstone2013')
            ->setLanguage('FI')
            ->setRequestId(substr(uniqid(), 0, 6))
            ->setIdType('02')
            ->setReturnLink('#success')
            ->setCancelLink('#cancelled')
            ->setRejectedLink('#rejected')
            ->setKeyVersion(1)
            ->setAlgorithm('sha256')
            ->setSecret('TotallyF4k3Secret!');

        return $this->render(
            'JSomerstoneFeikkiTupasBundle:Default:formCollection.html.twig',
            array(
                'tupasRequests' => array($tupasRequest)
            )
        );
    }
}
