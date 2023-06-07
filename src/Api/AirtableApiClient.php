<?php

namespace Tapp\Airtable\Api;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AirtableApiClient implements ApiClient
{
    private $client;

    private $typecast;
    private $base;
    private $table;
    private $delay;

    private $filters = [];
    private $fields = [];
    private $sorts = [];
    private $offset = false;

    public function __construct($base, $table, $access_token, Http $client = null, $typecast = false, $delayBetweenRequests = 200000)
    {
        $this->base = $base;
        $this->table = $table;
        $this->typecast = $typecast;
        $this->delay = $delayBetweenRequests;

        $this->client = $client ?? $this->buildClient($access_token);
    }

    private function buildClient($access_token)
    {
        return Http::withOptions([
            'base_uri' => 'https://api.airtable.com',
            'headers' => [
                'Authorization' => "Bearer {$access_token}",
                'content-type' => 'application/json',
            ],
        ]);
    }

    public function addFilter($column, $operation, $value): AirtableApiClient
    {
        $this->filters[] = "{{$column}}{$operation}\"{$value}\"";

        return $this;
    }

    public function addSort(string $column, string $direction = 'asc'): AirtableApiClient
    {
        if ($direction === 'desc') {
            $this->sorts[] = ['field' => $column, 'direction' => $direction];
        } else {
            $this->sorts[] = ['field' => $column];
        }

        return $this;
    }

    public function setTable($table): AirtableApiClient
    {
        $this->table = $table;

        return $this;
    }

    public function get(?string $id = null)
    {
        $url = $this->getEndpointUrl($id);

        return $this->decodeResponse($this->client->get($url));
    }

    public function getAllPages($delayBetweenRequestsInMicroseconds): Collection
    {
        $records = [];

        do {
            $response = $this->get();

            if (isset($response['records'])) {
                $records = array_merge($response['records'], $records);
            }

            if (isset($response['offset'])) {
                $this->offset = $response['offset'];
                usleep($delayBetweenRequestsInMicroseconds);
            } else {
                $this->offset = false;
            }
        } while ($this->offset);

        return collect($records);
    }

    public function post($contents = null)
    {
        $url = $this->getEndpointUrl();

        $params = ['fields' => (object) $contents, 'typecast' => $this->typecast];

        return $this->decodeResponse($this->client->post($url, $params));
    }

    public function put(string $id, $contents = null)
    {
        $url = $this->getEndpointUrl($id);

        $params = ['fields' => (object) $contents, 'typecast' => $this->typecast];

        return $this->decodeResponse($this->client->put($url, $params));
    }

    public function patch(string $id, $contents = null)
    {
        $url = $this->getEndpointUrl($id);

        $params = ['fields' => (object) $contents, 'typecast' => $this->typecast];

        return $this->decodeResponse($this->client->patch($url, $params));
    }

    public function massUpdate(string $method, array $data): array
    {
        $url = $this->getEndpointUrl();
        $records = [];

        // Update & Patch request body can include an array of up to 10 record objects
        $chunks = array_chunk($data, 10);
        foreach ($chunks as $key => $data_chunk) {
            $params = ['records' => $data_chunk, 'typecast' => $this->typecast];

            $response = $this->decodeResponse($this->client->$method($url, $params));
            $records += $response['records'];

            if (isset($chunks[$key + 1])) {
                usleep($this->delay);
            }
        }

        return ['records' => $records];
    }

    public function upsert($data = null, $fieldsToMergeOn = [])
    {
        $records = [];
        $updatedRecords = [];
        $createdRecords = [];

        $chunks = array_chunk($data, 10);
        foreach ($chunks as $key => $dataChunk) {
            $contents = [];
            foreach ($dataChunk as $dataRow) {
                $contents[] = (object) [
                    'fields' => (object) $dataRow,
                ];
            }

            $params = ['performUpsert' => (object) ['fieldsToMergeOn' => $fieldsToMergeOn], 'records' => $contents];

            $responseData = $this->decodeResponse(
                $this->client->patch($this->getEndpointUrl(), $params)
            );

            if ($responseData->has('records')) {
                $records += $responseData->get('records');
            }

            if ($responseData->has('updatedRecords')) {
                $updatedRecords += $responseData->get('updatedRecords');
            }

            if ($responseData->has('createdRecords')) {
                $createdRecords += $responseData->get('createdRecords');
            }

            if (isset($chunks[$key + 1])) {
                usleep($this->delay);
            }
        }

        return collect([
            'records' => $records,
            'updatedRecords' => $updatedRecords,
            'createdRecords' => $createdRecords,
        ]);
    }

    public function delete(string $id)
    {
        $url = $this->getEndpointUrl($id);

        return $this->decodeResponse($this->client->delete($url));
    }

    public function responseToJson($response): string
    {
        return (string) $response->getBody();
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

        return collect(json_decode($body, true));
    }

    public function setFields(?array $fields): AirtableApiClient
    {
        $this->fields = $fields;

        return $this;
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

        if ($query_params = $this->getQueryParams()) {
            $url .= '?'.http_build_query($query_params);
        }

        return $url;
    }

    protected function getQueryParams(): array
    {
        $query_params = [];

        if ($this->filters) {
            $query_params['filterByFormula'] = 'AND('.implode(',', $this->filters).')';
        }

        if ($this->fields) {
            $query_params['fields'] = $this->fields;
        }

        if ($this->offset) {
            $query_params['offset'] = $this->offset;
        }

        if ($this->sorts) {
            $query_params['sort'] = $this->sorts;
        }

        return $query_params;
    }
}
