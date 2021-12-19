<?php

use ApiOpenStudio\Core\ApiException;
use ApiOpenStudio\Core\Config;
use ApiOpenStudio\Core\OpenApi\OpenApiParent20;
use ApiOpenStudio\Db\Account;
use ApiOpenStudio\Db\Application;
use Codeception\Test\Unit;

class OpenApiParent20Test extends Unit
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
     * @var OpenApiParent20
     */
    protected OpenApiParent20 $openApiParent;

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
        $this->settings = new Config(dirname(__DIR__) . '/_data/settings.openapi.20.yml');
        $this->openApiParent = new OpenApiParent20();
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
            $this->fail('An exception was thrown generating the default OpenApi 2.0: ' . $e->getMessage());
        }
        try {
            $json = json_decode($this->openApiParent->export());
        } catch (ApiException $e) {
            $this->fail('An exception was thrown exporting the default OpenApi 2.0: ' . $e->getMessage());
        }
        try {
            $url = $this->settings->__get(['api', 'url']);
        } catch (ApiException $e) {
            $this->fail('An exception was thrown reading from the settings.yml file: ' . $e->getMessage());
        }

        $this->assertIsObject($json);
        $this->assertNotEmpty($json);

        $this->assertEquals('2.0', $json->swagger, 'Invalid swagger version');

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

        $this->assertNotEmpty($json->schemes, 'Invalid schemes');
        $this->assertIsArray($json->schemes, 'Invalid schemes');
        foreach ($json->schemes as $scheme) {
            $this->assertNotEmpty($scheme, 'Invalid scheme in schemes');
            $this->assertIsString($scheme, 'Invalid scheme in schemes');
            if (!in_array($scheme, ['https', 'http', 'ws', 'wss'])) {
                $this->fail('Invalid scheme in schemes');
            }
        }

        $this->assertNotEmpty($json->host, 'Invalid host');
        $this->assertIsString($json->host, 'Invalid host');
        $this->assertEquals($url, $json->host, 'Invalid host value.');

        $this->assertNotEmpty($json->basePath, 'Invalid basePath');
        $this->assertIsString($json->basePath, 'Invalid basePath');
        $this->assertEquals(
            '/' . $this->account->getName() . '/' . $this->application->getName(),
            $json->basePath,
            'Invalid basePath value.'
        );
    }

    protected function testGetSetAccount()
    {
        try {
            $this->openApiParent->setDefault($this->account->getName(), $this->application->getName());
        } catch (ApiException $e) {
            $this->fail('An exception was thrown generating the default OpenApi 2.0: ' . $e->getMessage());
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
            $this->fail('An exception was thrown generating the default OpenApi 2.0: ' . $e->getMessage());
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
