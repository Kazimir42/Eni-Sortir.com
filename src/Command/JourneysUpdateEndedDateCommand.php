<?php

namespace App\Command;

use App\Service\UpdateJourneys;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class JourneysUpdateEndedDateCommand extends Command
{
    protected static $defaultName = 'journeys:update:ended-date';
    protected static $defaultDescription = 'Update the status of journeys on "Clôturée"';
    private $updateJourneys;



    public function __construct(UpdateJourneys $updateJourneys)
    {
        parent::__construct();
        $this->updateJourneys = $updateJourneys;
    }


    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->updateJourneys->updateEndedRegisterDate();
        return Command::SUCCESS;
    }
}
