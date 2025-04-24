<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use OpenPayments\AuthClient;
use OpenPayments\Config\Config;

class FinalizePayment extends Command
{
    protected static $defaultName = 'OP:finish-payment';

    protected function configure(): void
    {
        $this
            ->setDescription('Create a continuation grant and create an outgoing payment')
            ->setHelp('This command allows you to create an outgoing payment grant and complete the payment')
            ->addArgument(
                'CONTINUE_ACCESS_TOKEN',
                InputArgument::OPTIONAL,
                'The value of CONTINUE_ACCESS_TOKEN',
                $_ENV['CONTINUE_ACCESS_TOKEN']
            )
            ->addArgument(
                'URL_WITH_INTERACT_REF',
                InputArgument::OPTIONAL,
                'The value of URL_WITH_INTERACT_REF',
                $_ENV['URL_WITH_INTERACT_REF']
            )
            ->addArgument(
                'CONTINUE_URI',
                InputArgument::OPTIONAL,
                'The value of CONTINUE_URI',
                $_ENV['CONTINUE_URI']
            )
            ->addArgument(
                'QUOTE_URL',
                InputArgument::OPTIONAL,
                'The name of the person to greet.',
                $_ENV['QUOTE_URL'] ?? null
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $WALLET_ADDRESS =  $_ENV['WALLET_ADDRESS'];
        $PRIVATE_KEY = $_ENV['PRIVATE_KEY'];
        $KEY_ID = $_ENV['KEY_ID'];
       

        $CONTINUE_ACCESS_TOKEN = $input->getArgument('CONTINUE_ACCESS_TOKEN');
        $URL_WITH_INTERACT_REF = $input->getArgument('URL_WITH_INTERACT_REF');
        $CONTINUE_URI = $input->getArgument('CONTINUE_URI');
        $QUOTE_URL = $input->getArgument('QUOTE_URL');
        echo '$URL_WITH_INTERACT_REF: '.$URL_WITH_INTERACT_REF."\n";
        $parse = parse_url($URL_WITH_INTERACT_REF);
        $query = $parse['query'] ?? '';
        parse_str($query, $params);
        echo '$parse: '.print_r($params, true)."\n";
        $interactRef = $params['interact_ref'] ?? null;
        $config = new Config(
            $WALLET_ADDRESS, $PRIVATE_KEY, $KEY_ID
        );
        $opClient = new AuthClient($config);
        $wallet = $opClient->walletAddress()->get([
            'url' => $config->getWalletAddressUrl()
        ]);

        //@! start chunk 3 | title=Continue grant
        $continueGrant = $opClient->grant()->continue(
            [
                'access_token'=> $CONTINUE_ACCESS_TOKEN,
                'url' => $CONTINUE_URI
            ],
            [
                'interact_ref'=> $interactRef,
            ]
        );
        //@! end chunk 3
        

        $output->writeln('GRANT request response: '.print_r($continueGrant, true));
        $output->writeln('OUTGOING_PAYMENT_GRANT_ACCES_TOKEN: '.$continueGrant->access_token->value);




        $outgoingPaymentRequest = [
            'walletAddress' => $config->getWalletAddressUrl(),
            'quoteId' => $QUOTE_URL,
            'metadata' => [
                'description' => 'Test outgoing payment',
                'reference' => '1234567890',
                'invoiceId' => '1234567890',
                'customData' => [
                    'key1' => 'value1',
                    'key2' => 'value2'
                ]
            ],
        ];
        
        $newOutgoingPayment = $opClient->outgoingPayment()->create(
            [
                'url' => $wallet->resourceServer,
                'access_token' => $continueGrant->access_token->value
            ],
            $outgoingPaymentRequest
        );

        echo "GRANT outgoingPayment  create() response:".print_r($newOutgoingPayment, true);

       $output->writeln('OUTGOING_PAYMENT_URL: '.$newOutgoingPayment->id);


        return Command::SUCCESS;
    }
}

