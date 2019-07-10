<?php

namespace App\Controller;

use App\Repository\SongRepository;
use Elasticsearch\ClientBuilder;
use Elasticsearch\Common\Exceptions\ClientErrorResponseException;
use Elasticsearch\Common\Exceptions\Curl\CouldNotConnectToHost;
use Elasticsearch\Common\Exceptions\NoNodesAvailableException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ElasticsearchController extends AbstractController
{
    private $hosts;
    private $client;
    private $repository;

    public function __construct()
    {
        $this->hosts = ['localhost:9200'];
    }

    /**
     * @Route("/search/{lyric}", methods={"GET", "HEAD"})
     * @param $lyric
     */
    public function search($lyric){
        $this->open();
        $value = htmlentities($lyric);
        try {
            $response = $this->repository->search($value);
        } catch (NoNodesAvailableException $no_node_alive){
            $this->close();
            return $this->render('erreur.html.twig', ['erreur1' => "THE DATABASE", 'erreur2' => "WAS NOT FOUND"]);
        }
        $this->close();
        return $this->render('Home/homepage.html.twig');
    }

    public function open(){
        try {
            $this->client = ClientBuilder::create()
                ->setHosts($this->hosts)
                ->build();
        } catch (CouldNotConnectToHost $host_error){
            return $this->render('erreur.html.twig', ['erreur1' => "CONNECTION", 'erreur2' => "HAS FAILED"]);
        }

        $this->repository = new SongRepository($this->client);
    }

    public function close(){
        $this->client = null;
    }





}

