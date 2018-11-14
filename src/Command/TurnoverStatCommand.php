<?php

namespace App\Command;

use App\Service\TurnoverService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TurnoverStatCommand extends Command
{
    /**
     * @var TurnoverService
     */
    private $turnoverService;

    /**
     * CreateUserCommand constructor.
     * @param TurnoverService $turnoverService
     */
    public function __construct(TurnoverService $turnoverService)
    {
        $this->turnoverService = $turnoverService;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('app:update-turnover')
            ->setDescription('Update turnover')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->turnoverService->updateStats();
    }
}