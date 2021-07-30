<?php

namespace App\Command;

use App\Service\UpdateJourneys;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class JourneysUpdateStatusCommand extends Command
{
    protected static $defaultName = 'journeys:update:status';
    protected static $defaultDescription = 'Update the status of journeys on "Passée" or "Activité en cours" ';
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
        $time = new \DateTime();
        $result = $time->format('Y-m-d H:i:s');
        $output->write($result);

        $this->updateJourneys->updateStatusJourney();
        return Command::SUCCESS;
    }
}
