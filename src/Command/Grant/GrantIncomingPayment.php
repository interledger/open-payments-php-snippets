<?php
namespace App\Command\Grant;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use OpenPayments\AuthenticatedClient;
use OpenPayments\Config\Config;

class GrantIncomingPayment extends Command
{
    protected static $defaultName = 'grant:ip';

    protected function configure(): void
    {
        $this
            ->setDescription('Outputs an incoming payment object, with the access_token value needed to make the incoming payment request.')
            ->setHelp('This command allows you to receive an incoming payment grant');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $WALLET_ADDRESS =  $_ENV['WALLET_ADDRESS'];
        $PRIVATE_KEY = $_ENV['PRIVATE_KEY'];
        $KEY_ID = $_ENV['KEY_ID'];
        $output->writeln('WALLET_ADDRESS: '.$WALLET_ADDRESS);
        $output->writeln('PRIVATE_KEY: '.$PRIVATE_KEY);
        $output->writeln('KEY_ID: '.$KEY_ID);

        $config = new Config(
            $WALLET_ADDRESS, $PRIVATE_KEY, $KEY_ID
        );
        $opClient = new AuthenticatedClient($config);

        $walletService = $opClient->walletAddress();
        $wallet  = $walletService->get([
            'url' => $config->getWalletAddressUrl()
        ]);

        $grantService = $opClient->grant($wallet->getAuthServer());
        $grantRequest = [
            'access_token' => [
                'access' => [
                    [
                        'type' => 'incoming-payment',
                        'actions' => ['list', 'read', 'read-all', 'complete', 'create', ]
                    ]
                ]
            ],
            'client' => $config->getWalletAddressUrl()
        ];
        $response = $grantService->request($grantRequest);

        $output->writeln('GRANT request response: '.print_r($response, true));
        $output->writeln('INCOMING_PAYMENT_GRANT: '.$response->access_token->value);

        return Command::SUCCESS;
    }
}