<?php

use ApiOpenStudio\Core\ApiException;
use ApiOpenStudio\Core\Config;
use ApiOpenStudio\Core\OpenApi\OpenApiParent301;
use ApiOpenStudio\Core\OpenApi\OpenApiPath301;
use ApiOpenStudio\Db\Account;
use ApiOpenStudio\Db\Application;
use ApiOpenStudio\Db\Resource;
use cebe\openapi\exceptions\TypeErrorException;
use Codeception\Test\Unit;
use cebe\openapi\Reader;

class OpenApi301Test extends Unit
{
    /**
     * @var UnitTester
     */
    protected UnitTester $tester;

    /**
     * @var Config
     */
    protected Config $settings;

    /**
     * @var OpenApiParent301
     */
    protected OpenApiParent301 $openApiParent;

    /**
     * @var OpenApiPath301
     */
    protected OpenApiPath301 $openApiPath;

    /**
     * @var Account
     */
    protected Account $account;

    /**
     * @var Application
     */
    protected Application $application;

    protected function _before()
    {
        $this->settings = new Config(dirname(__DIR__) . '/_data/settings.openapi.301.yml');
        $this->openApiParent = new OpenApiParent301();
        $this->openApiPath = new OpenApiPath301();
        $this->account = new Account(1, 'test_account');
        $this->application = new Application(3, 1, 'test_application');
    }

    protected function _after()
    {
    }

    /**
     * Test default Parent attributes.
     */
    public function testDefault()
    {
        try {
            $this->openApiParent->setDefault($this->account->getName(), $this->application->getName());
        } catch (ApiException $e) {
            $this->fail('An exception was thrown generating the default OpenApi 3.0.1: ' . $e->getMessage());
        }
        try {
            $decodedJson = $this->openApiParent->export(false);
        } catch (ApiException $e) {
            $this->fail('An exception was thrown exporting the default OpenApi 3.0.1: ' . $e->getMessage());
        }
        try {
            $decodedJson->paths = $this->getPathsOpenApi();
            $json = json_encode($decodedJson, JSON_UNESCAPED_SLASHES);
        } catch (ApiException $e) {
            $this->fail('An exception was thrown exporting the default paths OpenApi 3.0.1: ' . $e->getMessage());
        }
        try {
            $url = $this->settings->__get(['api', 'url']);
            $protocols = $this->settings->__get(['api', 'protocols']);
        } catch (ApiException $e) {
            $this->fail('An exception was thrown reading from the settings.yml file: ' . $e->getMessage());
        }
        $this->assertIsObject($decodedJson);
        $this->assertNotEmpty($decodedJson);

        $this->assertEquals('3.0.1', $decodedJson->openapi, 'Invalid openapi version');

        $this->assertNotEmpty($decodedJson->info, 'Invalid info');
        $this->assertIsObject($decodedJson->info, 'Invalid info');
        $this->assertNotEmpty($decodedJson->info->title, 'Invalid info.title');
        $this->assertIsString($decodedJson->info->title, 'Invalid info.title');
        $this->assertEquals($this->application->getName(), $decodedJson->info->title, 'Invalid info.title');
        $this->assertNotEmpty($decodedJson->info->description, 'Invalid info.description');
        $this->assertIsString($decodedJson->info->description, 'Invalid info.description');
        $this->assertEquals(
            "These are the resources that belong to the {$this->application->getName()} application.",
            $decodedJson->info->description,
            'Invalid info.description'
        );

        $this->assertNotEmpty($decodedJson->servers, 'Invalid servers');
        $this->assertIsArray($decodedJson->servers, 'Invalid servers');
        $accountName = $this->account->getName();
        $applicationName = $this->application->getName();
        foreach ($protocols as $protocol) {
            $server = "$protocol://$url/$accountName/$applicationName";
            $inServers = false;
            foreach ($decodedJson->servers as $obj) {
                if ($server == $obj->url) {
                    $inServers = true;
                }
            }
            if (!$inServers) {
                $this->fail("Invalid server in servers: $server.");
            }
        }

        try {
            $openapi = Reader::readFromJson($json);
        } catch (TypeErrorException $e) {
            $this->fail('An exception was thrown reading from the default OpenApi spec: ' . $e->getMessage());
        }
        if (!$openapi->validate()) {
            $this->fail('Schema validation failed: ' . print_r($openapi->getErrors(), true));
        }
    }

    /**
     * @return stdClass
     * @throws ApiException
     */
    protected function getPathsOpenApi(): stdClass
    {
        $paths = new stdClass();

        $resource = new Resource(
            null,
            1,
            'Test GET Resource OpenAPI',
            'This is to test a GET Resource OpenAPI',
            'get', 'test/openapi/get',
            json_encode($this->getMeta()),
            null,
            0
        );
        $this->openApiPath->setDefault($resource);
        $path = $this->openApiPath->export(false);
        foreach ((array) $path as $url => $methods) {
            foreach ((array) $methods as $method => $body) {
                $paths->{$url} = new stdClass();
                $paths->{$url}->{$method} = $body;
            }
        }

        $resource = new Resource(
            null,
            1,
            'Test POST Resource OpenAPI',
            'This is to test a POST Resource OpenAPI',
            'post', 'test/openapi/post',
            json_encode($this->postMeta()),
            null,
            0
        );
        $this->openApiPath->setDefault($resource);
        $path = $this->openApiPath->export(false);
        foreach ((array) $path as $url => $methods) {
            foreach ((array) $methods as $method => $body) {
                $paths->{$url} = new stdClass();
                $paths->{$url}->{$method} = $body;
            }
        }

        $resource = new Resource(
            null,
            1,
            'Test PATH Resource OpenAPI',
            'This is to test a PATH Resource OpenAPI',
            'post', 'test/openapi/path',
            json_encode($this->pathMeta()),
            null,
            0
        );
        $this->openApiPath->setDefault($resource);
        $path = $this->openApiPath->export(false);
        foreach ((array) $path as $url => $methods) {
            foreach ((array) $methods as $method => $body) {
                $paths->{$url} = new stdClass();
                $paths->{$url}->{$method} = $body;
            }
        }

        return $paths;
    }

    /**
     * @return array
     */
    protected function getMeta(): array
    {
        return [
            'process' => [
                'processor' => 'var_object',
                'id' => 'result_object',
                'attributes' => [
                    [
                        'processor' => 'var_field',
                        'id' => 'var_field_1',
                        'key' => 0,
                        'value' => [
                            'processor' => 'var_get',
                            'id' => 'var_get_1',
                            'key' => 'var_get_key_1',
                            'nullable' => true,
                            'expected_type' => 'text',
                        ],
                    ], [
                        'processor' => 'var_field',
                        'id' => 'var_field_2',
                        'key' => 1,
                        'value' => [
                            'processor' => 'var_get',
                            'id' => 'var_get_2',
                            'key' => 'var_get_key_2',
                            'nullable' => false,
                            'expected_type' => 'float',
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    protected function postMeta(): array
    {
        return [
            'process' => [
                'processor' => 'var_object',
                'id' => 'result_object',
                'attributes' => [
                    [
                        'processor' => 'var_field',
                        'id' => 'var_field_1',
                        'key' => 0,
                        'value' => [
                            'processor' => 'var_post',
                            'id' => 'var_post_1',
                            'key' => 'var_post_key_1',
                            'nullable' => true,
                            'expected_type' => 'text',
                        ],
                    ], [
                        'processor' => 'var_field',
                        'id' => 'var_field_2',
                        'key' => 1,
                        'value' => [
                            'processor' => 'var_post',
                            'id' => 'var_post_2',
                            'key' => 'var_post_key_2',
                            'nullable' => true,
                            'expected_type' => 'float',
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    protected function pathMeta(): array
    {
        return [
            'process' => [
                'processor' => 'var_object',
                'id' => 'result_object',
                'attributes' => [
                    [
                        'processor' => 'var_field',
                        'id' => 'var_field_1',
                        'key' => 0,
                        'value' => [
                            'processor' => 'var_uri',
                            'id' => 'var_path_1',
                            'index' => 0,
                            'nullable' => false,
                            'expected_type' => 'text',
                        ],
                    ], [
                        'processor' => 'var_field',
                        'id' => 'var_field_2',
                        'key' => 1,
                        'value' => [
                            'processor' => 'var_uri',
                            'id' => 'var_path_1',
                            'index' => 1,
                            'nullable' => true,
                            'expected_type' => 'integer',
                        ],
                    ],
                ],
            ],
        ];
    }

    protected function testGetSetAccount()
    {
        try {
            $this->openApiParent->setDefault($this->account->getName(), $this->application->getName());
        } catch (ApiException $e) {
            $this->fail('An exception was thrown generating the default OpenApi: ' . $e->getMessage());
        }
        $this->assertEquals($this->openApiParent->getAccount(),
            $this->account->getName(),
            'Invalid account extracted from OpenApi.'
        );
        $this->openApiParent->setAccount('edited_account');
        $this->assertEquals($this->openApiParent->getAccount(),
            'edited_account',
            'Invalid account extracted from OpenApi after editing.'
        );
    }

    protected function testGetSetApplication()
    {
        try {
            $this->openApiParent->setDefault($this->account->getName(), $this->application->getName());
        } catch (ApiException $e) {
            $this->fail('An exception was thrown generating the default OpenApi: ' . $e->getMessage());
        }
        $this->assertEquals($this->openApiParent->getApplication(),
            $this->application->getName(),
            'Invalid application extracted from OpenApi.'
        );
        $this->openApiParent->setApplication('edited_application');
        $this->assertEquals($this->openApiParent->getApplication(),
            'edited_application',
            'Invalid application extracted from OpenApi after editing.'
        );
    }
}
