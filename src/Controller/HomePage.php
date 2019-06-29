<?php

namespace App\Controller;
use \Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomePage extends AbstractController
{
    /**
     * @Route("/")
     * @return Response
     */

    public function wellcoming():Response
    {
        //return new Response('<html><head><title>Home page</title></head><body><h1 align="center">Wellcome to my page guys</h1></body></html>');

        $message = "Wellcome to my homepage";
        $navText = "navbar";
        return $this -> render('Home/homepage.html.twig',['navtext'=>$navText, 'message'=>$message]);
        return $this -> render('Home/homepagestyle.css.twig');

    }
}