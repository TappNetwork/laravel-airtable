<?php

namespace Tapp\Airtable;

class Airtable
{
    private $api;

    public function __construct($client)
    {
        $this->api = $client;
    }

    public function find(string $id)
    {
        return $this->api->get($id);
    }

    public function create($data)
    {
        return $this->api->post($data);
    }

    public function update(string $id, $data)
    {
        return $this->api->put($id, $data);
    }

    public function patch(string $id, $data)
    {
        return $this->api->patch($id, $data);
    }

    public function destroy(string $id)
    {
        return $this->api->delete($id);
    }

    public function get()
    {
        return $this->toCollection($this->api->get());
    }

    // There is delay between requests to avoid 429 error
    // 1000000 = 1 second
    // "The API is limited to 5 requests per second per base. If you exceed this rate, you will receive a 429 status code
    //  and will need to wait 30 seconds before subsequent requests will succeed."
    public function all($delayBetweenRequestsInMicroseconds = 200000)
    {
        return $this->toCollection($this->api->getAllPages($delayBetweenRequestsInMicroseconds));
    }

    public function table($table)
    {
        $this->api->setTable($table);

        return $this;
    }

    public function where($column, $operator, $value = null)
    {
        if (is_null($value)) {
            $this->api->addFilter($column, '=', $operator);
        } else {
            $this->api->addFilter($column, $operator, $value);
        }
        
        return $this;
    }

    public function firstOrCreate(array $idData, array $createData = [])
    {
        foreach ($idData as $key => $value) {
            $this->where($key, $value);
        }

        $results = $this->get();

        // first
        if ($results->isNotEmpty()) {
            return $results->first();
        }

        // create
        $data = array_merge($idData, $createData);

        return $this->create($data);
    }

    public function updateOrCreate(array $idData, array $updateData = [])
    {
        foreach ($idData as $key => $value) {
            $this->where($key, $value);
        }

        $results = $this->get();

        // first
        if ($results->isNotEmpty()) {
            $item = $results->first();

            //update
            return $this->update($item['id'], $updateData);
        }

        // create
        $data = array_merge($idData, $updateData);

        return $this->create($data);
    }

    private function toCollection($object)
    {
        return isset($object['records']) ? collect($object['records']) : $object;
    }
}
