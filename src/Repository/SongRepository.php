<?php

namespace App\Repository;

use Elasticsearch\Client;
use phpDocumentor\Reflection\Types\Integer;

class SongRepository
{

    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function search($value)
    {
        $json = '{
        "query": {
            "dis_max": {
              "queries": [
                { "match_phrase_prefix": { "content": "'.$value.'" }},
                { "match_phrase_prefix": { "author": "'.$value.'" }}
              ]
            }
          }
        }';

        return $this->client->search(
            [
                'index' => 'lyrics',
                'body' => $json
            ]
    );

    }

    public function searchAuthor($value)
    {
        $json = '{
        "query" : {
                "match" : {
                    "author" :  "'.$value.'"
                 }
            }
        }';

        return $this->client->search(
            [
                'index' => 'lyrics',
                'body' => $json
            ]
        );

    }

    public function searchLyric($id)
    {

        return $this->client->get(
            [
                'index' => 'lyrics',
                'id' => $id
            ]
        );

    }


    public function deleteLyric($id){
        return $this->client->delete(
            [
                'index' => 'lyrics',
                'id' => $id
            ]
        );
    }

    public function updateSong($id, $data){
        var_dump($data);
        $json = [
            'index' => 'lyrics',
            'id' => $id,
            'body' => [
                    'author' => $data['author'],
                    'title' => $data['title'],
                    'year' => $data['year'],
                    'collection' => $data['collection'],
                    'content' => $data['content'],
            ]
        ];

        return $this->client->update($json);
    }

    public function addSong($data){
        $json = [
            'index' => 'lyrics',
            'body' => [
                    'author' => strval($data['author']),
                    'title' => $data['title'],
                    'year' => intval($data['year']),
                    'collection' => $data['collection'],
                    'content' => $data['content'],
            ]
        ];

        return $this->client->index($json);
    }

}
