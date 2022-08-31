<?php

/**
 * Class ComposerCommand.
 *
 * @package    ApiOpenStudio\Processor
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the ApiOpenStudio Public License.
 *             If a copy of the license was not distributed with this file,
 *             You can obtain one at https://www.apiopenstudio.com/license/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Processor;

use ApiOpenStudio\Core\ApiException;
use ApiOpenStudio\Core\Config;
use ApiOpenStudio\Core\DataContainer;
use ApiOpenStudio\Core\ProcessorEntity;
use Composer\Console\Application;
use Exception;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Class ComposerCommand
 *
 * Processor class to run update() for a module.
 */
class ComposerCommand extends ProcessorEntity
{
    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'Composer Command',
        'machineName' => 'composer_command',
        'description' => 'Run a composer command.',
        'menu' => 'Admin',
        'input' => [
            'command' => [
                'description' => 'Composer command.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => ['repository_list', 'repository_set', 'repository_unset', 'require', 'remove'],
                'default' => '',
            ],
            'package' => [
                'description' => 'Package to install or remove.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'repository_url' => [
                'description' => 'Repository url to add to composer.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'repository_key' => [
                'description' => 'Repository name to add to composer.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
        ],
    ];

    /**
     * @return DataContainer
     * @throws ApiException
     */
    public function process(): DataContainer
    {
        parent::process();
        $command = $this->val('command', true);
        $package = $this->val('package', true);
        $key = $this->val('repository_key', true);
        $url = $this->val('repository_url', true);

        switch ($command) {
            case 'require':
                if (empty($package)) {
                    throw new ApiException('No package names received', 6, $this->id, 400);
                }
                $input = new StringInput("$command --with-all-dependencies $package");
                return $this->runCommand($input);
            case 'remove':
                if (empty($package)) {
                    throw new ApiException('No package names received', 6, $this->id, 400);
                }
                $input = new StringInput("$command $package");
                return $this->runCommand($input);
            case 'repository_set':
                if (empty($key)) {
                    throw new ApiException('No repository key received', 6, $this->id, 400);
                }
                if (empty($url)) {
                    throw new ApiException('No repository URL received', 6, $this->id, 400);
                }
                $input = new StringInput("config repositories.$key vcs $url");
                return $this->runCommand($input);
            case 'repository_unset':
                if (empty($key)) {
                    throw new ApiException('No repository key received', 6, $this->id, 400);
                }
                $input = new StringInput("config --unset repositories.$key");
                return $this->runCommand($input);
            case 'repository_list':
                $input = new StringInput("config --list");
                $config = $this->runCommand($input);
                $config = explode("\n", $config->getData());
                $result = [];
                foreach ($config as $line) {
                    $line = str_replace('[', '', $line);
                    $line = str_replace(']', '', $line);
                    $items = explode(' ', $line);
                    if (
                        sizeof($items) == 2
                        && strpos($items[0], 'repositories.') === 0
                        && !in_array($items[1], ['vcs', 'composer'])
                    ) {
                        $items[0] = str_replace('repositories.', '', $items[0]);
                        $items[0] = str_replace('.url', '', $items[0]);
                        $result[$items[0]] = $items[1];
                    }
                }
                return new DataContainer($result);
            default:
                throw new ApiException('Unhandled composer command', 6, $this->id, 400);
        }
    }

    /**
     * Run a composer command and return the cli response.
     *
     * @param $input
     *
     * @return DataContainer
     *
     * @throws ApiException
     */
    protected function runCommand($input)
    {
        $input->setInteractive(false);
        try {
            $result = $this->composerCommand($input);
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }

        return new DataContainer($result, 'text');
    }

    /**
     * Run a composer command.
     *
     * @param $input
     * @return string
     *
     * @throws ApiException
     */
    protected function composerCommand($input): string
    {
        $settings = new Config();
        $cwd = getcwd();
        $basePath = $settings->__get(['api', 'base_path']);
        chdir($basePath);
        $application = new Application();
        $application->setAutoExit(false);
        $output = new BufferedOutput();

        try {
            $application->run($input, $output);
            chdir($cwd);
        } catch (Exception $e) {
            chdir($cwd);
            $this->logger->error('api', $e->getMessage());
            throw new ApiException($e->getMessage(), 6, 'oops', 400);
        }

        $message = $output->fetch();
        $this->logger->warning('api', $message);
        return $message;
    }
}
