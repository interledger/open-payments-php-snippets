<?php
namespace App\Command\Token;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

//@! start chunk 1 | title=Import dependencies
use OpenPayments\AuthClient;
use OpenPayments\Config\Config;
//@! end chunk 1
/**
 * Class TokenRevoke
 * @package App\Command\Token
 *
 * This command is used to revoke an access token.
 * It outputs the response from the Open Payments API.
 */

class TokenRevoke extends Command
{
    protected static $defaultName = 'token:revoke';

    protected function configure(): void
    {
        $this
            ->setDescription('This command is used to revoke an access token.')
            ->setHelp('This command outputs the response from the Open Payments API.')
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
            );
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

        //@! start chunk 2 | title=Initialize Open Payments client
        $config = new Config(
            $WALLET_ADDRESS, $PRIVATE_KEY, $KEY_ID
        );
        $opClient = new AuthClient($config);
        //@! end chunk 2
        
        //@! start chunk 3 | title=Revoke token
        $tokenResponse = $opClient->token()->revoke(
            [
                'access_token' => $ACCESS_TOKEN,
                'url' => $TOKEN_MANAGE_URL
            ]
        );
        //@! end chunk 3

        echo "TOKEN RESPONSE: ".print_r($tokenResponse, true);

        return Command::SUCCESS;
    }
}