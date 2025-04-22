<?php
namespace App\Command\Grant;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

//@! start chunk 1 | title=Import dependencies
use OpenPayments\AuthClient;
use OpenPayments\Config\Config;
//@! end chunk 1

class CancelGrant extends Command
{
    protected static $defaultName = 'grant:cancel';

    protected function configure(): void
    {
        $this
            ->setDescription('Cancel a grant.')
            ->setHelp('This command allows you to cancel a grant')
            ->addArgument(
                'ACCESS_TOKEN',
                InputArgument::OPTIONAL,
                'The value of ACCESS_TOKEN',
                $_ENV['ACCESS_TOKEN']
            )
            ->addArgument(
                'CONTINUE_URI',
                InputArgument::OPTIONAL,
                'The value of CONTINUE_URI',
                $_ENV['CONTINUE_URI']
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $WALLET_ADDRESS =  $_ENV['WALLET_ADDRESS'];
        $PRIVATE_KEY = $_ENV['PRIVATE_KEY'];
        $KEY_ID = $_ENV['KEY_ID'];
        $ACCESS_TOKEN = $input->getArgument('ACCESS_TOKEN');
        $CONTINUE_URI = $input->getArgument('CONTINUE_URI');
        
        //@! start chunk 2 | title=Initialize Open Payments client
        $config = new Config(
            $WALLET_ADDRESS, $PRIVATE_KEY, $KEY_ID
        );
        $opClient = new AuthClient($config);
        //@! end chunk 2

        //@! start chunk 3 | title=Revoke grant
        $response = $opClient->grant()->cancel(
            [
                'access_token'=> $ACCESS_TOKEN,
                'url' => $CONTINUE_URI
            ]
        );
        //@! end chunk 3

        $output->writeln('CANCEL GRANT request response: '.print_r($response, true));

        return Command::SUCCESS;
    }
}