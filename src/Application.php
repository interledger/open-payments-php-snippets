<?php

namespace App;

use Symfony\Component\Console\Application as ConsoleApplication;
use App\Command\Grant\GrantIncomingPayment;
use App\Command\IncomingPayment\IncomingPaymentCreate;
use App\Command\IncomingPayment\IncomingPaymentGet;
//use App\Command\IncomingPayment\IncomingPaymentList;

class Application
{
    public function run(): void
    {
        $application = new ConsoleApplication();

        // Register commands
        $application->add(new GrantIncomingPayment());
        $application->add(new IncomingPaymentCreate());
        $application->add(new IncomingPaymentGet());
        //$application->add(new IncomingPaymentList());

        // Run the application
        $application->run();
    }
}
