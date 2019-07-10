<?php

namespace App\Controller;

use App\Repository\SongRepository;
use Elasticsearch\ClientBuilder;
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

        $this->client = ClientBuilder::create()
            ->setHosts($this->hosts)
            ->build();

        $this->repository = new SongRepository($this->client);
    }

    /**
     * @Route("/search/{lyric}", methods={"GET", "HEAD"})
     * @param $lyric
     */
    public function search($lyric){
        $value = htmlentities($lyric);
        $response = $this->repository->search($value);
        var_dump($response['hits']['hits']);
        die();
    }





}

