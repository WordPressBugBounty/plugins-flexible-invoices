<?php

declare (strict_types=1);
namespace WPDeskFIVendor\WPDesk\Codeception\Runner\Command;

use WPDeskFIVendor\Symfony\Component\Console\Command\Command;
use WPDeskFIVendor\Symfony\Component\Console\Input\InputInterface;
use WPDeskFIVendor\Symfony\Component\Console\Output\OutputInterface;
use WPDeskFIVendor\WPDesk\Codeception\Runner\DockerCompose;
use WPDeskFIVendor\WPDesk\Codeception\Runner\RuntimeMode;
final class StopCommand extends Command
{
    public function __construct(private readonly DockerCompose $dockerCompose, private readonly RuntimeMode $runtimeMode)
    {
        parent::__construct();
    }
    protected function configure(): void
    {
        $this->setName('stop')->setAliases(['down'])->setDescription('Stop the local WP Desk test stack.');
    }
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($this->runtimeMode->isDirect()) {
            $output->writeln('<info>Direct runtime detected; no Docker stack to stop.</info>');
            return self::SUCCESS;
        }
        return $this->dockerCompose->stop($output);
    }
}
