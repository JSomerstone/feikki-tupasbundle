<?php

namespace JSomerstone\Feikki\TupasBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('JSomerstoneFeikkiTupasBundle:Default:index.html.twig', array('name' => $name));
    }
}
