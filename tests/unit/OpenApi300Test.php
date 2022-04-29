<?php

use ApiOpenStudio\Core\ApiException;
use ApiOpenStudio\Core\Config;
use ApiOpenStudio\Core\OpenApi\OpenApiParent300;
use ApiOpenStudio\Core\OpenApi\OpenApiPath300;
use ApiOpenStudio\Db\Account;
use ApiOpenStudio\Db\Application;
use ApiOpenStudio\Db\Resource;
use cebe\openapi\exceptions\TypeErrorException;
use Codeception\Test\Unit;
use cebe\openapi\Reader;

class OpenApiParent300Test extends Unit
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
     * @var OpenApiParent300
     */
    protected OpenApiParent300 $openApiParent;

    /**
     * @var OpenApiPath300
     */
    protected OpenApiPath300 $openApiPath;

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
        $this->settings = new Config(dirname(__DIR__) . '/_data/settings.openapi.300.yml');
        $this->openApiParent = new OpenApiParent300($this->settings);
        $this->openApiPath = new OpenApiPath300();
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
            $this->fail('An exception was thrown generating the default OpenApi 3.0.0: ' . $e->getMessage());
        }
        try {
            $decodedJson = $this->openApiParent->export(false);
        } catch (ApiException $e) {
            $this->fail('An exception was thrown exporting the default OpenApi 3.0.0: ' . $e->getMessage());
        }
        try {
            $decodedJson->paths = $this->getPathsOpenApi();
            $json = json_encode($decodedJson, JSON_UNESCAPED_SLASHES);
        } catch (ApiException $e) {
            $this->fail('An exception was thrown exporting the default paths OpenApi 3.0.0: ' . $e->getMessage());
        }
        try {
            $url = $this->settings->__get(['api', 'url']);
            $protocols = $this->settings->__get(['api', 'protocols']);
        } catch (ApiException $e) {
            $this->fail('An exception was thrown reading from the settings.yml file: ' . $e->getMessage());
        }
        $this->assertIsObject($decodedJson);
        $this->assertNotEmpty($decodedJson);

        // openapi block
        $this->assertEquals('3.0.0', $decodedJson->openapi, 'Invalid openapi version');

        // info block
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

        // servers block
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

        // components block
        $this->assertNotEmpty($decodedJson->components);
        $this->assertNotEmpty($decodedJson->components->schemas);
        $this->assertNotEmpty($decodedJson->components->schemas->GeneralError);
        $this->assertNotEmpty($decodedJson->components->responses);
        $this->assertNotEmpty($decodedJson->components->responses->GeneralError);
        $this->assertNotEmpty($decodedJson->components->responses->Unauthorised);
        $this->assertNotEmpty($decodedJson->components->responses->Forbidden);
        $this->assertNotEmpty($decodedJson->components->securitySchemes);
        $this->assertEquals(
            json_decode(json_encode($this->getSchemasGeneralError())),
            $decodedJson->components->schemas->GeneralError,
            'Components->schemas->GeneralError has the expected format.'
        );
        $this->assertEquals(
            json_decode(json_encode($this->getResponsesGeneralError())),
            $decodedJson->components->responses->GeneralError,
            'Components->responses->GeneralError has the expected format.'
        );
        $this->assertEquals(
            json_decode(json_encode($this->getResponsesUnauthorised())),
            $decodedJson->components->responses->Unauthorised,
            'Components->responses->Unauthorised has the expected format.'
        );
        $this->assertEquals(
            json_decode(json_encode($this->getResponsesForbidden())),
            $decodedJson->components->responses->Forbidden,
            'Components->responses->Forbidden has the expected format.'
        );
        $this->assertEquals(
            json_decode(json_encode($this->getSecuritySchemes())),
            $decodedJson->components->securitySchemes,
            'Components->securitySchemes has the expected format.'
        );

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
    protected function getSchemasGeneralError(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'result' => [
                    'type' => 'string',
                ],
                'data' => [
                    'type' => 'object',
                    'properties' => [
                        'id' => [
                            'type' => 'integer',
                            'format' => 'int32',
                        ],
                        'code' => [
                            'type' => 'integer',
                            'format' => 'int32',
                        ],
                        'message' => [
                            'type' => 'string',
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    protected function getResponsesGeneralError(): array
    {
        return [
            'description' => 'General Error',
            'content' => [
                'application/json' => [
                    'schema' => [
                        '$ref' => '#/components/schemas/GeneralError'
                    ],
                    'example' => [
                        'result' => 'error',
                        'data' => [
                            'id' => '<my_processor_id>',
                            'code' => 6,
                            'message' => 'Oops, something went wrong.'
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    protected function getResponsesUnauthorised(): array
    {
        return [
            'description' => 'Unauthorised',
            'content' => [
                'application/json' => [
                    'schema' => [
                        '$ref' => '#/components/schemas/GeneralError'
                    ],
                    'example' => [
                        'result' => 'error',
                        'data' => [
                            'id' => '<my_processor_id>',
                            'code' => 4,
                            'message' => 'Invalid token.'
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    protected function getResponsesForbidden(): array
    {
        return [
            'description' => 'Forbidden',
            'content' => [
                'application/json' => [
                    'schema' => [
                        '$ref' => '#/components/schemas/GeneralError'
                    ],
                    'example' => [
                        'result' => 'error',
                        'data' => [
                            'id' => '<my_processor_id>',
                            'code' => 6,
                            'message' => 'Permission denied.'
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    protected function getSecuritySchemes(): array
    {
        return [
            'bearer_token' => [
                'type' => 'http',
                'scheme' => 'bearer',
                'bearerFormat' => 'JWT',
            ],
        ];
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
