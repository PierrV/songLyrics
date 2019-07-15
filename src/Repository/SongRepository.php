<?php

namespace App\Repository;

use Elasticsearch\Client;

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

    public function searchLyric($value)
    {

        return $this->client->get(
            [
                'index' => 'lyrics',
                'id' => $value
            ]
        );

    }


    public function searchAll()
    {
        $json = '{
        "query" : {
                "match_all": {}
            }
        }';

        return $this->client->search(
            [
                'index' => 'lyrics',
                'id' => $json
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

}
