<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use OpenPayments\AuthClient;
use OpenPayments\Config\Config;

class FetchQuoteAndInitializePayment extends Command
{
    protected static $defaultName = 'OP:fetch-quote-and-initialize-payment';
    protected static $defaultDescription = 'Fetches a quote and initializes a payment.';

    protected function configure(): void
    {
        $this
            ->setDescription('Create IP grant, IP create, Quote Grant, quote create and initialize outgoing payment')
            ->setHelp('This command will ouput IP details, Quote details and the interactive outgoing payment grant details');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $WALLET_ADDRESS =  $_ENV['WALLET_ADDRESS'];
        $PRIVATE_KEY = $_ENV['PRIVATE_KEY'];
        $KEY_ID = $_ENV['KEY_ID'];
       
        $config = new Config(
            $WALLET_ADDRESS, $PRIVATE_KEY, $KEY_ID
        );
        $opClient = new AuthClient($config);

        $wallet = $opClient->walletAddress()->get([
            'url' => $config->getWalletAddressUrl()
        ]);
        //IP GRANT
        $ipGrantRequest = [
            'access_token' => [
                'access' => [
                    [
                        'type' => 'incoming-payment',
                        'actions' => ['read', 'complete', 'create', 'list' ]
                    ]
                ]
            ],
            'client' => $config->getWalletAddressUrl()
        ];
        $ipGrantResponse = $opClient->grant()->request(
            [
                'url' => $wallet->authServer
            ],
            $ipGrantRequest
        );
    
        $output->writeln('INCOMING_PAYMENT_GRANT: '.$ipGrantResponse->access_token->value);
        //IP REQUEST
        $incomingPaymentRequest = [
            'walletAddress' => $config->getWalletAddressUrl(),
            // 'incomingAmount' => [
            //     'value' => "130",
            //     'assetCode' => 'USD',
            //     'assetScale' => 2
            // ],
            'metadata' => [
                'description' => 'Test php snippets transaction with $1,30 amount',
                'externalRef' => 'INVOICE-'.uniqid()
            ],
            'expiresAt' => (new \DateTime())->add(new \DateInterval('PT59M'))->format("Y-m-d\TH:i:s.v\Z")
        ];

        $newIncomingPayment = $opClient->incomingPayment()->create(
            [
                'url' => $wallet->resourceServer,
                'access_token' => $ipGrantResponse->access_token->value
            ],
            $incomingPaymentRequest
        );


        $output->writeln('INCOMING_PAYMENT_URL: '.$newIncomingPayment->id);

        //QUOTE GRANT
        $quoteGrantRequest = [
            'access_token' => [
                'access' => [
                    [
                        'type' => 'quote',
                        'actions' => ['create', 'read', 'read-all']
                    ]
                ]
            ],
            'client' => $config->getWalletAddressUrl()
        ];

        $quoteResponse = $opClient->grant()->request(
            [
                'url' => $wallet->authServer
            ],
            $quoteGrantRequest
        );

        $output->writeln('QUOTE_GRANT_ACCESS_TOKEN: '.$quoteResponse->access_token->value);

        //QUOTE REQUEST
        $quoteRequest = [
            'method' => "ilp",
            'walletAddress'=> $wallet->id,
            'receiver'=> $newIncomingPayment->id,
            'debitAmount' => [
                'assetCode' => 'USD',
                'assetScale' => 2,
                'value' => "132",
            ],
        ];
            
        $newQuote = $opClient->quote()->create(
            [
                'url' => $wallet->resourceServer,
                'access_token' => $quoteResponse->access_token->value
            ],
            $quoteRequest
        );

        $output->writeln('QUOTE_URL: '.$newQuote->id);

        //OP grant
        $grantRequest = [
            'access_token' => [
                'access' => [
                    [
                        'type' => 'outgoing-payment',
                        'actions' => ['list', 'list-all', 'read', 'read-all','create'],
                        'identifier'=> $wallet->id,
                        'limits' => [
                            //'receiver' => $newQuote->receiver,
                            'debitAmount'   => $newQuote->debitAmount->toArray(),
                            'receiveAmount' => $newQuote->receiveAmount->toArray(),
                            
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
        $response = $opClient->grant()->request(
            [
                'url' => $wallet->authServer
            ],
            $grantRequest
        );

        $output->writeln('GRANT request response: '.print_r($response, true));
        $output->writeln('Please interact at the following URL: '.$response->interact->redirect);
        $output->writeln('CONTINUE_ACCESS_TOKEN = '.$response->continue->access_token->value);
        $output->writeln('CONTINUE_URI = '.$response->continue->uri);


        return Command::SUCCESS;
    }
}