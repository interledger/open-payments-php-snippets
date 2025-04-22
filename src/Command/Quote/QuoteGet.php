<?php
namespace App\Command\Quote;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use OpenPayments\AuthClient;
use OpenPayments\Config\Config;

class QuoteGet extends Command
{
    protected static $defaultName = 'quote:get';

    protected function configure(): void
    {
        $this
            ->setDescription('Outputs a friendly greeting.')
            ->setHelp('This command allows you to output a greeting message...')
            ->addArgument(
                'QUOTE_GRANT_ACCESS_TOKEN',
                InputArgument::OPTIONAL,
                'The access token for the quote.',
                $_ENV['QUOTE_GRANT_ACCESS_TOKEN'] ?? null
            )
            ->addArgument(
                'QUOTE_URL',
                InputArgument::OPTIONAL,
                'The url of the quote.',
                $_ENV['QUOTE_URL'] ?? ''
            )
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $WALLET_ADDRESS =  $_ENV['WALLET_ADDRESS'];
        $PRIVATE_KEY = $_ENV['PRIVATE_KEY'];
        $KEY_ID = $_ENV['KEY_ID'];
        $QUOTE_GRANT_ACCESS_TOKEN = $input->getArgument('QUOTE_GRANT_ACCESS_TOKEN');
        $QUOTE_URL = $input->getArgument('QUOTE_URL');
        $output->writeln('WALLET_ADDRESS: '.$WALLET_ADDRESS);
        $output->writeln('PRIVATE_KEY: '.$PRIVATE_KEY);
        $output->writeln('KEY_ID: '.$KEY_ID);
        $output->writeln('QUOTE_URL: '.$QUOTE_URL);

        $config = new Config(
            $WALLET_ADDRESS, $PRIVATE_KEY, $KEY_ID
        );
        $opClient = new AuthClient($config);

        $Quote = $opClient->quote()->get(
            [
                'access_token' => $QUOTE_GRANT_ACCESS_TOKEN,
                'url' => $QUOTE_URL
            ]
        );

        $output->writeln('GET QUOTE:<br><pre>'.print_r($Quote, true).'</pre>');

        return Command::SUCCESS;
    }
}