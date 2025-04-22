<?php
namespace App\Command\WalletAddress;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use OpenPayments\AuthClient;
use OpenPayments\Config\Config;

class PublicGetWalletAddressKeys extends Command
{
    protected static $defaultName = 'wa:get-keys';

    protected function configure(): void
    {
        $this
            ->setDescription('Outputs a friendly greeting.')
            ->setHelp('This command allows you to output a greeting message...')
            ->addArgument(
                'WALLET_ADDRESS',
                InputArgument::OPTIONAL,
                'The name of the person to greet.',
                $_ENV['WALLET_ADDRESS'] ?? null
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $WALLET_ADDRESS = $input->getArgument('WALLET_ADDRESS');
        $config = new Config($WALLET_ADDRESS);
        $opClient = new AuthClient($config);

        $wallet = $opClient->walletAddress()->getKeys([
            'url' => $config->getWalletAddressUrl()
        ]);

        $output->writeln('WALLET ADDRESS KEYS:<br><pre>'.print_r($wallet, true).'</pre>');

        return Command::SUCCESS;
    }
}