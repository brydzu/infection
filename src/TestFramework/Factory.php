<?php

declare(strict_types=1);

namespace Infection\TestFramework;

use Infection\Finder\TestFrameworkExecutableFinder;
use Infection\TestFramework\Config\ConfigLocator;
use Infection\TestFramework\PhpSpec\Adapter\PhpSpecAdapter;
use Infection\TestFramework\PhpUnit\Adapter\PhpUnitAdapter;
use Infection\TestFramework\PhpUnit\CommandLine\ArgumentsAndOptionsBuilder;
use Infection\TestFramework\PhpUnit\Config\Builder\InitialConfigBuilder;
use Infection\TestFramework\PhpUnit\Config\Builder\MutationConfigBuilder;
use Infection\TestFramework\PhpUnit\Config\Path\PathReplacer;

class Factory
{
    /**
     * @var string
     */
    private $tempDir;
    /**
     * @var PathReplacer
     */
    private $pathReplacer;

    /**
     * @var ConfigLocator
     */
    private $configLocator;

    public function __construct(string $tempDir, ConfigLocator $configLocator, PathReplacer $pathReplacer)
    {
        $this->tempDir = $tempDir;
        $this->configLocator = $configLocator;
        $this->pathReplacer = $pathReplacer;
    }

    public function create($adapterName) : AbstractTestFrameworkAdapter
    {
        if ($adapterName === PhpUnitAdapter::NAME) {
            $phpUnitConfigPath = $this->configLocator->locate();
            return new PhpUnitAdapter(
                new TestFrameworkExecutableFinder(PhpUnitAdapter::NAME),
                new InitialConfigBuilder($this->tempDir, $phpUnitConfigPath, $this->pathReplacer),
                new MutationConfigBuilder($this->tempDir, $phpUnitConfigPath, $this->pathReplacer),
                new ArgumentsAndOptionsBuilder()
            );
        }

        if ($adapterName === PhpSpecAdapter::NAME) {
            return new PhpSpecAdapter(
                new TestFrameworkExecutableFinder(PhpSpecAdapter::NAME)
            );
        }

        throw new \InvalidArgumentException(
            sprintf(
                'Invalid name of test framework. Available names are: %s',
                implode(', ', [PhpUnitAdapter::NAME, PhpSpecAdapter::NAME])
            )
        );
    }
}