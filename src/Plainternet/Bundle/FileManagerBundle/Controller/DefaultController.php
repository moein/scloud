<?php

namespace Plainternet\Bundle\FileManagerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('PlainternetFileManagerBundle:Default:index.html.twig', array('name' => $name));
    }
}
