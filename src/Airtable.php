<?php

namespace Tapp\Airtable;

use InvalidArgumentException;

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

    /**
     * @param  dynamic $args (string) $id, $data | (array) $data
     * @return mixed
     *
     * @throws \InvalidArgumentException
     */
    public function update(...$args)
    {
        if (is_string($args[0])) {
            if (! isset($args[1])) {
                throw new InvalidArgumentException('$data argument is required.');
            }

            return $this->api->put($args[0], $args[1]);
        } elseif (is_array($args[0])) {
            return $this->api->massUpdate('put', $args[0]);
        }

        throw new InvalidArgumentException('Update accepts either an array or an id and array of data.');
    }

    /**
     * @param  dynamic $args (string) $id, $data | (array) $data
     * @return mixed
     *
     * @throws \InvalidArgumentException
     */
    public function patch(...$args)
    {
        if (is_string($args[0])) {
            if (! isset($args[1])) {
                throw new InvalidArgumentException('$data argument is required.');
            }

            return $this->api->patch($args[0], $args[1]);
        } elseif (is_array($args[0])) {
            return $this->api->massUpdate('patch', $args[0]);
        }

        throw new InvalidArgumentException('Patch accepts either an array or an id and array of data.');
    }

    public function destroy(string $id)
    {
        return $this->api->delete($id);
    }

    public function get(array $fields = [])
    {
        if ($fields) {
            $this->select($fields);
        }

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

    public function select(array $fields = [])
    {
        $this->api->setFields($fields);

        return $this;
    }

    private function toCollection($object)
    {
        return isset($object['records']) ? collect($object['records']) : $object;
    }
}
