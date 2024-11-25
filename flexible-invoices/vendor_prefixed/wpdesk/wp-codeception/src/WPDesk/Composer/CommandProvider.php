<?php

namespace WPDeskFIVendor\WPDesk\Composer\Codeception;

use WPDeskFIVendor\WPDesk\Composer\Codeception\Commands\CreateCodeceptionTests;
use WPDeskFIVendor\WPDesk\Composer\Codeception\Commands\PrepareCodeceptionDb;
use WPDeskFIVendor\WPDesk\Composer\Codeception\Commands\PrepareLocalCodeceptionTests;
use WPDeskFIVendor\WPDesk\Composer\Codeception\Commands\PrepareLocalCodeceptionTestsWithCoverage;
use WPDeskFIVendor\WPDesk\Composer\Codeception\Commands\PrepareParallelCodeceptionTests;
use WPDeskFIVendor\WPDesk\Composer\Codeception\Commands\PrepareWordpressForCodeception;
use WPDeskFIVendor\WPDesk\Composer\Codeception\Commands\RunCodeceptionTests;
use WPDeskFIVendor\WPDesk\Composer\Codeception\Commands\RunLocalCodeceptionTests;
use WPDeskFIVendor\WPDesk\Composer\Codeception\Commands\RunLocalCodeceptionTestsWithCoverage;
/**
 * Links plugin commands handlers to composer.
 */
class CommandProvider implements \WPDeskFIVendor\Composer\Plugin\Capability\CommandProvider
{
    public function getCommands()
    {
        return [new CreateCodeceptionTests(), new RunCodeceptionTests(), new RunLocalCodeceptionTests(), new RunLocalCodeceptionTestsWithCoverage(), new PrepareCodeceptionDb(), new PrepareWordpressForCodeception(), new PrepareLocalCodeceptionTests(), new PrepareLocalCodeceptionTestsWithCoverage(), new PrepareParallelCodeceptionTests()];
    }
}
