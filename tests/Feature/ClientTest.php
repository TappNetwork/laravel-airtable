<?php

namespace Tapp\Airtable\Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tapp\Airtable\Api\AirtableApiClient as Client;
use Tapp\Airtable\Facades\AirtableFacade as Airtable;
use Tapp\Airtable\Tests\TestCase;

class ClientTest extends TestCase
{
    /** @test */
    public function it_can_be_instantiated()
    {
        $client = new Client('test_base', 'test_table', 'test_key');
        $this->assertInstanceOf(Client::class, $client);
    }

    /** @test */
    public function it_can_post()
    {
        $expectedResponse = [
            'id'          => 'randomlygenerated',
            'fields'      => ['Company Name' => 'Tapp Network'],
            'createdTime' => 'timestamp',
        ];

        $postData = ['Company Name' => 'Tapp Network'];

        Http::fake([
            '/v0/test_base/companies' => Http::response($expectedResponse, 200),
        ]);

        $actualResponse = Airtable::table('companies')
            ->firstOrCreate($postData);

        $this->assertEquals($expectedResponse['fields'], $actualResponse['fields']);
    }

    /** @test */
    public function it_can_search()
    {
        $expectedResponse = [
            'id' => 'randomlygenerated',
            'fields' => ['Company Name' => 'Tapp Network'],
            'createdTime' => 'timestamp',
        ];

        Http::fake([
            '/v0/test_base/companies' => Http::response($expectedResponse, 200),
        ]);

        $actualResponse = Airtable::table('companies')
            ->where('Company Name', '=', 'Tapp Network')
            ->get();

        $first = $actualResponse[0];

        $this->assertEquals($expectedResponse['fields'], $first['fields']);
    }

    /** @test */
    public function it_can_sort_asc()
    {
        //Ascending sort
        $expectedResponseAsc = [
            [
                'id'          => 0,
                'fields'      => ['Company Name' => 'A Network'],
                'createdTime' => 'timestamp',
            ],
            [
                'id'          => 1,
                'fields'      => ['Company Name' => 'B Network'],
                'createdTime' => 'timestamp',
            ],
        ];

        Http::fake([
            'api.airtable.com/*' =>
            Http::response($expectedResponseAsc, 200)
        ]);

        $actualResponse = Airtable::table('companies')
            ->orderBy('Company Name', 'asc')
            ->get();

        $first = $actualResponse[0];

        $this->assertEquals($expectedResponseAsc[0]['fields'], $first['fields']);
    }

    /** @test */
    public function it_can_sort_desc()
    {
        //Descending sort
        $expectedResponseDesc = [
            [
                'id'          => 1,
                'fields'      => ['Company Name' => 'B Network'],
                'createdTime' => 'timestamp',
            ],
            [
                'id'          => 0,
                'fields'      => ['Company Name' => 'A Network'],
                'createdTime' => 'timestamp',
            ],
        ];

        Http::fake([
            'api.airtable.com/*' =>
            Http::response($expectedResponseDesc, 200)
        ]);

        $actualResponse = Airtable::table('companies')
            ->orderBy('Company Name', 'desc')
            ->get();

        $first = $actualResponse[0];

        $this->assertEquals($expectedResponseDesc[0]['fields'], $first['fields']);
    }
}
