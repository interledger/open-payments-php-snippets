<?php
namespace App\Command\Token;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use OpenPayments\AuthClient;
use OpenPayments\Config\Config;

class TokenRotate extends Command
{
    protected static $defaultName = 'token:rotate';

    protected function configure(): void
    {
        //echo "<pre>".print_r($_ENV, true)."</pre>";
        $this
            ->setDescription('Outputs a friendly greeting.')
            ->setHelp('This command allows you to output a greeting message...')
            ->addArgument(
                'ACCESS_TOKEN',
                InputArgument::OPTIONAL,
                'The name of the person to greet.',
                $_ENV['ACCESS_TOKEN'] ?? null
            )
            ->addArgument(
                'TOKEN_MANAGE_URL',
                InputArgument::OPTIONAL,
                'The name of the person to greet.',
                $_ENV['TOKEN_MANAGE_URL'] ??  null
            )
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $WALLET_ADDRESS =  $_ENV['WALLET_ADDRESS'];
        $PRIVATE_KEY = $_ENV['PRIVATE_KEY'];
        $KEY_ID = $_ENV['KEY_ID'];
        $ACCESS_TOKEN = $input->getArgument('ACCESS_TOKEN');
        $TOKEN_MANAGE_URL = $input->getArgument('TOKEN_MANAGE_URL');
        
        $output->writeln('ACCESS_TOKEN: '.$ACCESS_TOKEN);
        $output->writeln('TOKEN_MANAGE_URL: '.$TOKEN_MANAGE_URL);

        $config = new Config(
            $WALLET_ADDRESS, $PRIVATE_KEY, $KEY_ID
        );
        $opClient = new AuthClient($config);

        $tokenResponse = $opClient->token()->rotate(
            [
                'access_token' => $ACCESS_TOKEN,
                'url' => $TOKEN_MANAGE_URL
            ]
        );
        
        echo "TOKEN RESPONSE:<br><pre>".print_r($tokenResponse, true)."</pre>";

        return Command::SUCCESS;
    }
}