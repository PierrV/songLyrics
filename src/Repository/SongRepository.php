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
        "query" : {
                "match_phrase" : {
                    "content" :  "'.$value.'"
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

}
