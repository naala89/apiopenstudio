<?php

use ApiOpenStudio\Core\ApiException;
use ApiOpenStudio\Core\Config;
use ApiOpenStudio\Core\OpenApi\OpenApiParent301;
use ApiOpenStudio\Db\Account;
use ApiOpenStudio\Db\Application;
use cebe\openapi\exceptions\TypeErrorException;
use Codeception\Test\Unit;
use cebe\openapi\Reader;

class OpenApiParent301Test extends Unit
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
            $encodedJson = $this->openApiParent->export();
            $json = json_decode($encodedJson);
        } catch (ApiException $e) {
            $this->fail('An exception was thrown exporting the default OpenApi 3.0.1: ' . $e->getMessage());
        }
        try {
            $url = $this->settings->__get(['api', 'url']);
            $protocols = $this->settings->__get(['api', 'protocols']);
        } catch (ApiException $e) {
            $this->fail('An exception was thrown reading from the settings.yml file: ' . $e->getMessage());
        }
        $this->assertIsObject($json);
        $this->assertNotEmpty($json);

        $this->assertEquals('3.0.1', $json->openapi, 'Invalid openapi version');

        $this->assertNotEmpty($json->info, 'Invalid info');
        $this->assertIsObject($json->info, 'Invalid info');
        $this->assertNotEmpty($json->info->title, 'Invalid info.title');
        $this->assertIsString($json->info->title, 'Invalid info.title');
        $this->assertEquals($this->application->getName(), $json->info->title, 'Invalid info.title');
        $this->assertNotEmpty($json->info->description, 'Invalid info.description');
        $this->assertIsString($json->info->description, 'Invalid info.description');
        $this->assertEquals(
            "These are the resources that belong to the {$this->application->getName()} application.",
            $json->info->description,
            'Invalid info.description'
        );

        $this->assertNotEmpty($json->servers, 'Invalid servers');
        $this->assertIsArray($json->servers, 'Invalid servers');
        $accountName = $this->account->getName();
        $applicationName = $this->application->getName();
        foreach ($protocols as $protocol) {
            $server = "$protocol://$url/$accountName/$applicationName";
            $inServers = false;
            foreach ($json->servers as $obj) {
                if ($server == $obj->url) {
                    $inServers = true;
                }
            }
            if (!$inServers) {
                $this->fail("Invalid server in servers: $server.");
            }
        }

        try {
            $openapi = Reader::readFromJson($encodedJson);
        } catch (TypeErrorException $e) {
            $this->fail('An exception was thrown reading from the default OpenApi spec: ' . $e->getMessage());
        }
        if (!$openapi->validate()) {
            $this->fail(print_r($openapi->getErrors(), true));
        }
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
