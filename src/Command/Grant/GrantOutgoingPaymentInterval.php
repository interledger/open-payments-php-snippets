<?php
namespace App\Command\Grant;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use OpenPayments\AuthClient;
use OpenPayments\Config\Config;

class GrantOutgoingPaymentInterval extends Command
{
    protected static $defaultName = 'grant:interval';

    protected function configure(): void
    {
        $this
            ->setDescription('Outputs an outgoing payment object, with the access_token value needed to make the ougoing payment request.')
            ->setHelp('This command allows you to receive an outgoing payment grant');
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
        $opClient = new AuthClient($config);

        $wallet  = $opClient->walletAddress()->get([
            'url' => $config->getWalletAddressUrl()
        ]);

        $grantRequest = [
            'access_token' => [
                'access' => [
                    [
                        'type' => 'outgoing-payment',
                        'actions' => ['list', 'read','create'],
                        'identifier'=> $wallet->id,
                        'limits' => [
                            'debitAmount'=> [
                                'assetCode'=> 'USD',
                                'assetScale'=> 2,
                                'value'=> 167,
                            ]
                        ],
                    ]
                ]
            ],
            'client' => $config->getWalletAddressUrl(),
            'interact'=> [
                'start'=> ["redirect"],
                'finish'=>[
                  'method'=> "redirect",
                  'uri'=> 'https://localhost/?paymentId=123423',
                  'nonce'=> "1234567890",
                ],
            ]
        ];
        echo "Grant Request: ".print_r($grantRequest, true);
        $response = $opClient->grant()->request(
            [
                'url' => $wallet->authServer
            ],
            $grantRequest
        );

        $output->writeln('GRANT request response: '.print_r($response, true));
        $output->writeln('OUTGOING_PAYMENT_GRANT_ACCES_TOKEN: '.$response->access_token->value);

        return Command::SUCCESS;
    }
}