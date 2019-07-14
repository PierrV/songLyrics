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
     * @Route("/search", methods={"GET", "HEAD"})
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function search()
    {
        if (isset($_GET['lyric'])) {
            $this->open();
            $value = htmlentities($_GET['lyric']);
            try {
                $response = $this->repository->search($value);
            } catch (NoNodesAvailableException $no_node_alive) {
                $this->close();
                return $this->render('erreur.html.twig', ['erreur1' => "THE DATABASE", 'erreur2' => "WAS NOT FOUND"]);
            }
            return $this->render('song/query.html.twig', ['result' => $response['hits']['hits']]);
        } else {
            return $this->render('song/query.html.twig');
        }
    }

    /**
     * @Route("/searchauthor/{author}", methods={"GET", "HEAD"})
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function searchAuthor($author)
    {
        if (isset($author)) {
            $this->open();
            $value = htmlentities($author);
            try {
                $response = $this->repository->searchAuthor($value);
            } catch (NoNodesAvailableException $no_node_alive) {
                $this->close();
                return $this->render('erreur.html.twig', ['erreur1' => "THE DATABASE", 'erreur2' => "WAS NOT FOUND"]);
            }
            return $this->render('song/query.html.twig', ['result' => $response['hits']['hits']]);

        } else {
            return $this->render('song/query.html.twig');
        }
    }

    /**
     * @Route("/getLyric/{id}", methods={"GET", "HEAD"})
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getLyric($id)
    {
        if (isset($id)) {
            $this->open();
            $value = htmlentities($id);
            try {
                $response = $this->repository->searchLyric($value);
            } catch (NoNodesAvailableException $no_node_alive) {
                $this->close();
                return $this->render('erreur.html.twig', ['erreur1' => "THE DATABASE", 'erreur2' => "WAS NOT FOUND"]);
            }
            return $this->render('song/show.html.twig', ['field' => $response]);

        } else {
            return $this->render('song/query.html.twig');
        }
    }

    /**
     * @Route("/searchAll", methods={"GET", "HEAD"})
     */
    public function searchAll(){
        $this->open();
        try {
            $response = $this->repository->searchAll();
        } catch (NoNodesAvailableException $no_node_alive){
            $this->close();
            return $this->render('erreur.html.twig', ['erreur1' => "THE DATABASE", 'erreur2' => "WAS NOT FOUND"]);
        }

        $i = 0;

        foreach ($response['hits']['hits'] as $result){
            $data[$i] = $result;
            $i++;
        }

        return $this->render('song/query.html.twig', ['result' => $data]);
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

