<?php
namespace App\Command;

use App\Service\Unlink;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UnlinkCommand extends Command
{

    private $unlink;
    public function __construct(Unlink $unlink)
    {
        $this->unlink=$unlink;
        parent::__construct();
    }
    protected function configure()
{
    $this
        ->setName('app:unlink')
        ->setDescription('Delete file sent in the contact page.')
    ;
}
protected function execute(InputInterface $input, OutputInterface $output)
{
  $this->unlink->sup();
    $output->writeln('file successfully delted');
    return Command::SUCCESS;
}
}