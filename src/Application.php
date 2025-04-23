<?php

namespace App;

use Symfony\Component\Console\Application as ConsoleApplication;

use App\Command\Grant\GrantIncomingPayment;
use App\Command\Grant\GrantContinuation;
use App\Command\Grant\CancelGrant;

use App\Command\IncomingPayment\IncomingPaymentCreate;
use App\Command\IncomingPayment\IncomingPaymentGet;
use App\Command\IncomingPayment\IncomingPaymentList;
use App\Command\IncomingPayment\IncomingPaymentComplete;

use App\Command\Grant\GrantOutgoingPayment;
use App\Command\Grant\GrantOutgoingPaymentInterval;
use App\Command\OutgoingPayment\OutgoingPaymentCreate;
use App\Command\OutgoingPayment\OutgoingPaymentCreateAmount;
use App\Command\OutgoingPayment\OutgoingPaymentGet;
use App\Command\OutgoingPayment\OutgoingPaymentList;

use App\Command\Grant\GrantQuote;
use App\Command\Quote\QuoteCreate;
use App\Command\Quote\QuoteGet;

use App\Command\Token\TokenRotate;
use App\Command\Token\TokenRevoke;

use App\Command\WalletAddress\PublicGetWalletAddress;
use App\Command\WalletAddress\PublicGetWalletAddressKeys;

use App\Command\FetchQuoteAndInitializePayment;
use App\Command\FinalizePayment;

class Application
{
    public function run(): void
    {
        $application = new ConsoleApplication();

        $application->add(new PublicGetWalletAddress());
        $application->add(new PublicGetWalletAddressKeys());

        // Register commands
        $application->add(new GrantIncomingPayment());
        $application->add(new IncomingPaymentCreate());
        $application->add(new IncomingPaymentGet());
        $application->add(new IncomingPaymentList());
        $application->add(new IncomingPaymentComplete());

        $application->add(new GrantOutgoingPayment());
        $application->add(new GrantOutgoingPaymentInterval());
        $application->add(new OutgoingPaymentCreate());
        $application->add(new OutgoingPaymentCreateAmount());
        $application->add(new OutgoingPaymentGet());
        $application->add(new OutgoingPaymentList());

        $application->add(new GrantQuote());
        $application->add(new QuoteCreate());
        $application->add(new QuoteGet());

        $application->add(new TokenRotate());
        $application->add(new TokenRevoke());
        
        
        $application->add(new GrantContinuation());
        $application->add(new CancelGrant());

        $application->add(new FetchQuoteAndInitializePayment());
        $application->add(new FinalizePayment());

        // Run the application
        $application->run();
    }
}
