<?php

namespace Tapp\Airtable;

class Airtable
{
    private $api;

    /** @var string */
    protected $base;

    /** @var string */
    protected $table;

    public function __construct($client, $table)
    {
        $this->table = $table;
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

    public function where($column, $value)
    {
        return $this->api->where($column, $value);
    }

    public function firstOrCreate(array $idData, array $createData = [])
    {
        return $this->api->firstOrCreate($idData, $createData);
    }

    public function createOrUpdate(array $idData, array $updateData = [])
    {
        return $this->api->createOrUpdate($idData, $updateData);
    }

    public function get()
    {
        return $this->api->get();
    }

    public function all()
    {
        return $this->api->getAllPages();
    }
}
