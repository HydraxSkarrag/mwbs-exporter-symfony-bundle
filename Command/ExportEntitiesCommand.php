<?php
namespace MwbsExporterBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;

class ExportEntitiesCommand extends ContainerAwareCommand
{

    /**
     *
     * @see \Symfony\Component\Console\Command\Command::configure()
     */
    protected function configure()
    {
        $this->setName('mwbs:export-entities')->setDescription('Exports the MySQLWorkbench to doctrine2-annotation entities');
    }

    /**
     *
     * @see \Symfony\Component\Console\Command\Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->callMwbExporter($input, $output);
        
        $this->callSchemaValidation($input, $output);
    }

    /**
     *
     * @param InputInterface $input            
     * @param OutputInterface $output            
     */
    protected function callMwbExporter(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('Dumping all <comment>%s</comment> mysql workbench schemas.', $input->getOption('env')));
        $output->writeln('');
        
        $exporter = $this->getContainer()->get('mwbs_exporter.exporter');
        $exporter->export($output);
    }

    /**
     *
     * @param InputInterface $input            
     * @param OutputInterface $output            
     */
    protected function callSchemaValidation(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("\n" . '<info>Schema validation:</info>');
        
        $command = $this->getApplication()->find('doctrine:schema:validate');
        $arguments = array(
            'command' => $command->getName()
        );
        $input = new ArrayInput($arguments);
        
        $output->writeln(sprintf('call <comment>%s</comment>', $command->getName()));
        
        $returnCode = $command->run($input, $output);
        
        if ($returnCode != 0) {
            $output->writeln(sprintf('<error>%s failed...</error>', $command->getName()));
        }
    }
}
