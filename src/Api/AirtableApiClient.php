<?php

namespace Tapp\Airtable\Api;

use GuzzleHttp\Client;
use Illuminate\Support\Str;

class AirtableApiClient implements ApiClient
{
    private $client;

    private $typecast;
    private $base;
    private $table;

    private $filters = [];
    private $pageSize = 100;
    private $maxRecords = 100;

    public function __construct($base, $table, $access_token, $httpLogFormat = null, Client $client = null, $typecast = false)
    {
        $this->base = $base;
        $this->table = $table;
        $this->typecast = $typecast;

        $stack = \GuzzleHttp\HandlerStack::create();

        if ($httpLogFormat) {
            $stack->push(
                \GuzzleHttp\Middleware::log(
                    new \Monolog\Logger('Logger'),
                    new \GuzzleHttp\MessageFormatter($httpLogFormat)
                )
            );
        }

        $this->client = $client ?? $this->buildClient($access_token, $stack);
    }

    private function buildClient($access_token, $stack)
    {
        return new Client([
            'base_uri' => 'https://api.airtable.com',
            'headers' => [
                'Authorization' => "Bearer {$access_token}",
                'content-type' => 'application/json',
            ],
            'handler' => $stack,
        ]);
    }

    public function addFilter($column, $operation, $value)
    {
        $this->filters[] = "{{$column}}{$operation}\"{$value}\"";

        return $this;
    }

    public function setTable($table)
    {
        $this->table = $table;

        return $this;
    }

    public function get(?string $id = null)
    {
        $url = $this->getEndpointUrl($id);

        return $this->decodeResponse($this->client->get($url));
    }

    public function getAllPages($delayBetweenRequestsInMicroseconds)
    {
        $url = $this->getEndpointUrl();

        $records = [];
        $offset = false;

        do {
            $query = []; // page sizes defaults to 100

            if ($offset) {
                $query['offset'] = $offset;
            }

            $response = $this->client->get($url, [
                'query' => $query,
            ]);

            $response = $this->decodeResponse($response);

            if (isset($response['records'])) {
                $records = array_merge($response['records'], $records);
            }

            if (isset($response['offset'])) {
                $offset = $response['offset'];
                usleep($delayBetweenRequestsInMicroseconds);
            } else {
                $offset = false;
            }
        } while ($offset);

        return collect($records);
    }

    public function post($contents = null)
    {
        $url = $this->getEndpointUrl();

        $params = ['json' => ['fields' => (object) $contents, 'typecast' => $this->typecast]];

        return $this->decodeResponse($this->client->post($url, $params));
    }

    public function put(string $id, $contents = null)
    {
        $url = $this->getEndpointUrl($id);

        $params = ['json' => ['fields' => (object) $contents, 'typecast' => $this->typecast]];

        return $this->decodeResponse($this->client->put($url, $params));
    }

    public function patch(string $id, $contents = null)
    {
        $url = $this->getEndpointUrl($id);

        $params = ['json' => ['fields' => (object) $contents, 'typecast' => $this->typecast]];

        return $this->decodeResponse($this->client->patch($url, $params));
    }

    public function delete(string $id)
    {
        $url = $this->getEndpointUrl($id);

        return $this->decodeResponse($this->client->delete($url));
    }

    public function responseToJson($response)
    {
        $body = (string) $response->getBody();

        return $body;
    }

    public function responseToCollection($response)
    {
        $body = (string) $response->getBody();

        if ($body === '') {
            return collect([]);
        }

        $object = json_decode($body);

        return isset($object->records) ? collect($object->records) : $object;
    }

    public function decodeResponse($response)
    {
        $body = (string) $response->getBody();

        if ($body === '') {
            return [];
        }

        return json_decode($body, true);
    }

    protected function getEndpointUrl(?string $id = null): string
    {
        if ($id) {
            $url = '/v0/~/~/~';

            return Str::replaceArray('~', [
                $this->base,
                $this->table,
                $id,
            ], $url);
        }

        $url = '/v0/~/~';

        $url = Str::replaceArray('~', [
            $this->base,
            $this->table,
        ], $url);

        if ($this->filters) {
            $url .= '?'.http_build_query([
                'filterByFormula' => 'AND('.implode(',', $this->filters).')',
            ]);
        }

        return $url;
    }
}
