<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExtendedListCommand extends Command
{
    protected static $defaultName = 'list:extended';

    protected function configure()
    {
        $this->setDescription('List all commands with arguments and options');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $application = $this->getApplication();
        foreach ($application->all() as $name => $command) {
            $output->writeln("➤ <info>$name</info>: " . $command->getDescription());

            $definition = $command->getDefinition();
            foreach ($definition->getArguments() as $arg) {
                $output->writeln("   - Argument: <comment>{$arg->getName()}</comment> " . $arg->getDescription());
            }
            foreach ($definition->getOptions() as $opt) {
                $output->writeln("   - Option: <comment>--{$opt->getName()}</comment> " . $opt->getDescription());
            }

            $output->writeln("");
        }

        return Command::SUCCESS;
    }
}
