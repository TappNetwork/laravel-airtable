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

    public function destroy(string $id)
    {
        return $this->api->delete($id);
    }

    public function get()
    {
        return $this->toCollection($this->api->get());
    }

    public function all()
    {
        return $this->toCollection($this->api->getAllPages());
    }

    public function table($table)
    {
        $this->api->setTable($table);

        return $this;
    }

    public function where($column, $operator, $value = null)
    {
        if (is_null($value)) {
            return $this->api->addFilter($column, '=', $operator);
        } else {
            return $this->api->addFilter($column, $operator, $value);
        }
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
