<?php

namespace App\Controller;

use \Symfony\Component\HttpFoundation\Response;
use Twig\Environment ;
//use Symfony\Component\Routing\Annotation\Route;
//use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController //extends AbstractController
{

    /**
     * @var Environment
     */
    private $twig;

    public function __construct(Environment $twig){
        $this -> twig = $twig ;
    }

    public function admin():Response
    {
        return new Response($this->twig->render('Home/admin.html.twig'));

    }
}