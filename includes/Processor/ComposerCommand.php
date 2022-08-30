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
use Symfony\Component\Console\Input\ArrayInput;
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
                'limitValues' => ['install', 'require', 'remove'],
                'default' => '',
            ],
            'package' => [
                'description' => 'Package to install or remove.',
                'cardinality' => [1, '*'],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
        ],
    ];

    /**
     * {@inheritDoc}
     *
     * @return DataContainer Result of the processor.
     *
     * @throws ApiException Exception if invalid result.
     */
    public function process(): DataContainer
    {
        parent::process();
        $command = $this->val('command', true);
        $packages = $this->val('package', true);
        $packages = is_array($packages) ? $packages : [$packages];
        $input = new ArrayInput([
            'command' => $command,
            'packages' => $packages,
        ]);
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
     * @param ArrayInput $input
     * @return string
     *
     * @throws ApiException
     */
    protected function composerCommand(ArrayInput $input): string
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
