<?php

namespace App\Controller;

use App\Repository\SongRepository;
use Elasticsearch\ClientBuilder;
use Elasticsearch\Common\Exceptions\ClientErrorResponseException;
use Elasticsearch\Common\Exceptions\Curl\CouldNotConnectToHost;
use Elasticsearch\Common\Exceptions\NoNodesAvailableException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validation;

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
            $this->close();
            return $this->render('song/query.html.twig', ['result' => $response['hits']['hits'], 'search' => $value]);
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
            $this->close();
            return $this->render('song/query.html.twig', ['result' => $response['hits']['hits'], 'search' => $value]);

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
            $this->close();
            return $this->render('song/show.html.twig', ['field' => $response]);

        } else {
            return $this->render('song/query.html.twig');
        }
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

    /**
     * @Route("/admin/deleteSong/{id}", methods={"GET", "HEAD"})
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteSong($id){
        if (isset($id)) {
            $this->open();
            try {
                $response = $this->repository->deleteLyric($id);
            } catch (NoNodesAvailableException $no_node_alive) {
                $this->close();
                return $this->render('erreur.html.twig', ['erreur1' => "THE DATABASE", 'erreur2' => "WAS NOT FOUND"]);
            }
            $this->close();
            return $this->redirectToRoute("app_elasticsearch_search", ['lyric' => $_GET['search']]);

        } else {
            return $this->render('song/query.html.twig');
        }
    }

    /**
 * @Route("/admin/editSong/{id}", methods={"GET", "HEAD"})
 * @param $id
 * @return \Symfony\Component\HttpFoundation\Response
 */
    public function editSong($id){
        if (isset($id)) {
            $this->open();
            $value = htmlentities($id);
            try {
                $response = $this->repository->searchLyric($value);
            } catch (NoNodesAvailableException $no_node_alive) {
                $this->close();
                return $this->render('erreur.html.twig', ['erreur1' => "THE DATABASE", 'erreur2' => "WAS NOT FOUND"]);
            }
            return $this->render('song/edit.html.twig', ['field' => $response, 'id' => $value]);

        } else {
            return $this->render('song/query.html.twig');
        }
    }

    /**
     * @Route("/admin/SongConfirm", methods={"PUT", "POST", "GET"})
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editSongConfirm(){
        $id = htmlentities($_POST['id']);
        $data['author'] = htmlentities($_POST['author']);
        $data['title'] = htmlentities($_POST['title']);
        $data['year'] = htmlentities($_POST['year']);
        $data['collection'] = htmlentities($_POST['collection']);
        $data['content'] = htmlentities($_POST['content']);
        $validator = Validation::createValidator();
        $violations['author'] = $validator->validate($data['author'], [
            new NotBlank(),
        ]);
        $violations['title'] = $validator->validate($data['title'], [
            new NotBlank(),
        ]);
        $violations['year'] = $validator->validate($data['year'], [
            new NotBlank(),
        ]);
        $violations['content'] = $validator->validate($data['content'], [
            new NotBlank(),
        ]);

         // there are errors, now you can show them
        foreach ($violations as $violation) {
            if (0 !== count($violation)) {
                return $this->redirectToRoute("/admin/editSong/".$id);
            }
        }
        $this->open();
        try {
           if(!empty($id)) {
               $response = $this->repository->updateSong($id, $data);
           } else {
               $response = $this->repository->addSong($data);
           }
        } catch (NoNodesAvailableException $no_node_alive) {
           $this->close();
           return $this->render('erreur.html.twig', ['erreur1' => "THE DATABASE", 'erreur2' => "WAS NOT FOUND"]);
        }
           return $this->render('song/show.html.twig', ['field' => $response]);

    }

    /**
     * @Route("/admin/addSong")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addSong()
    {
        return $this->render('song/edit.html.twig', ['field' => false]);
    }


}

