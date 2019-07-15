<?php

namespace App\Controller;

use \Symfony\Component\HttpFoundation\Response;
use Twig\Environment ;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Authorization\Voter;
//use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomePageController extends AbstractController
{

    /**
     * @var Environment
     */
    private $twig;

    public function __construct(Environment $twig){
        $this -> twig = $twig ;
    }

    public function wellcoming():Response
    {
            return $this->redirectToRoute('fos_user_security_login');
    }
}