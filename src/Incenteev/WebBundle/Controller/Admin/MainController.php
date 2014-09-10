<?php

namespace Incenteev\WebBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Controller displaying the admin panel
 */
class MainController extends Controller
{
    public function indexAction()
    {
        return $this->render('WebBundle:Admin:index.html.twig');
    }
}
