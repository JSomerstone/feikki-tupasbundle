<?php

namespace JSomerstone\Feikki\TupasBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JSomerstone\Feikki\TupasBundle\Model\TupasForm;
use JSomerstone\Feikki\TupasBundle\Model\TupasRequest;
use JSomerstone\Feikki\TupasBundle\Model\TupasValidator;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('JSomerstoneFeikkiTupasBundle:Default:index.html.twig', array('name' => $name));
    }

    public function identifyAction($bank)
    {
        $tupasSettings =  $this->container->getParameter('tupas');
        $httpRequest = $this->getRequest()->request->all();

        $sharedSecret = $tupasSettings['custom'][$bank]['secret'];
        $validator = new TupasValidator();
        $validator->validateTupasRequest($httpRequest, $sharedSecret);


        return $this->render(
            'JSomerstoneFeikkiTupasBundle:Default:fakeBank.html.twig',
            array(
                'bankName' => ucfirst($bank),
                'tupasRequest' => $httpRequest,
                'errorMessage' => $validator->errorMessage,
                'debugMessages' => $validator->debugMessages
            )
        );
    }

    public function tupasFormAction()
    {
        $tupasSettings =  $this->container->getParameter('tupas');
        $requests = $this->getTupasRequests($tupasSettings['common'], $tupasSettings['custom']);

        return $this->render(
            'JSomerstoneFeikkiTupasBundle:Default:formCollection.html.twig',
            array(
                'tupasRequests' => $requests
            )
        );
    }

    private function getTupasRequests($defaultSettings, $bankList)
    {
        $banks = array();
        foreach ($bankList as $customSettings) {
            $banks[] = $this->formTupasForm(array_merge($defaultSettings, $customSettings));
        }
        return $banks;
    }

    private function formTupasForm($settings)
    {
        if ( ! isset($settings['buttonUrl'])){
            $settings['buttonUrl'] = null;
        }
        if ( ! isset($settings['buttonText'])){
            $settings['buttonText'] = null;
        }
        $tupasRequest = new TupasRequest();
        $tupasRequest->setVersion(1)
            ->setServiceProvider($settings['serviceProvider'])
            ->setLanguage($settings['languageCode'])
            ->setRequestId(substr(uniqid(), 0, 6))
            ->setIdType($settings['idType'])
            ->setReturnLink($settings['returnLink'])
            ->setCancelLink($settings['cancelLink'])
            ->setRejectedLink($settings['rejectedLink'])
            ->setKeyVersion(1)
            ->setAlgorithm($settings['algorithm'])
            ->setSecret($settings['secret']);

        $form = new TupasForm($tupasRequest, $settings['url']);
        $form->setButtonUrl($settings['buttonUrl'])
            ->setButtonText($settings['buttonText']);

        return $form;
    }
}
