<?php

namespace Tapp\Airtable\Test;

use Tapp\Airtable\Api\AirtableApiClient as Client;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use GuzzleHttp\Client as GuzzleClient;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\ClientException;

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
            'id' => 'randomlygenerated',
            'fields' => ['Company Name' => 'Tapp Network'],
            'createdTime' => 'timestamp',
        ];

        $postData = ['Company Name' => 'Tapp Network'];

        $mockGuzzle = $this->mock_guzzle_request(
            json_encode($expectedResponse),
            '/v0/test_base/companies',
            [
                'json' => [
                    'fields' => (object) $postData,
                ],
            ]
        );

        $client = $this->build_client($mockGuzzle);

        $actualResponse = $client->table('companies')
            ->post($postData);

        $this->assertEquals($expectedResponse['fields'], $actualResponse['fields']);
    }

    private function build_client($mockGuzzle)
    {
        if (getenv('TEST_AIRTABLE_API')) {
            return new Client(
                env('AIRTABLE_BASE'),
                env('AIRTABLE_TABLE'),
                env('AIRTABLE_KEY')
            );
        }

        return new Client(
            'test_base',
            'test_table',
            'test_key',
            $mockGuzzle
        );
    }

    private function mock_guzzle_request($expectedResponse, $expectedEndpoint, $expectedParams)
    {
        if (getenv('TEST_AIRTABLE_API')) {
            return null;
        }

        $mockResponse = $this->getMockBuilder(ResponseInterface::class)
            ->getMock();

        if ($expectedResponse) {
            $mockResponse->expects($this->once())
                ->method('getBody')
                ->willReturn($expectedResponse);
        }

        $mockGuzzle = $this->getMockBuilder(GuzzleClient::class)
            ->setMethods(['post'])
            ->getMock();

        $mockGuzzle->expects($this->once())
            ->method('post')
            ->with($expectedEndpoint, $expectedParams)
            ->willReturn($mockResponse);

        return $mockGuzzle;
    }
}
