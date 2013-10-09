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
        $this->setName('mwbs:export-entities')
            ->setDescription('Exports the MySQLWorkbench to doctrine2-annotation entities')
            ->addArgument('bundleName', InputArgument::REQUIRED, 'Name of the Bundle')
            ->addArgument('mwbFile', InputArgument::REQUIRED, 'Mysql-Workbench filename inside Resources/workbench/');
    }

    /**
     *
     * @see \Symfony\Component\Console\Command\Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->callMwbExporter($input, $output);
        
        $this->callStubGenerate($input, $output);
        
        $this->callSchemaValidation($input, $output);
    }

    /**
     *
     * @param InputInterface $input            
     * @param OutputInterface $output            
     */
    protected function callMwbExporter(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("\n" . '<info>Export <comment>' . $input->getArgument('mwbFile') . '</comment> workbench:</info>');
        $output->writeln('');
        
        $kernel = $this->getContainer()->get('kernel');
        
        $root_dir = realpath($kernel->getRootDir() . '/../');
        $config = $kernel->locateResource('@' . $input->getArgument('bundleName') . '/Resources/workbench/' . $input->getArgument('mwbFile') . '.json');
        $source = $kernel->locateResource('@' . $input->getArgument('bundleName') . '/Resources/workbench/' . $input->getArgument('mwbFile') . '.mwb');
        $destination = $kernel->locateResource('@' . $input->getArgument('bundleName') . '/Entity/');
        
        $command = sprintf('cd %1$s; php bin/export.php --config=%2$s %3$s %4$s', $root_dir, $config, $source, $destination);
        
        $returnCode = '';
        system($command, $returnCode);
        
        if ($returnCode != 0) {
            $output->writeln('<error>export failed...</error>');
            $output->writeln(sprintf('<comment>%s</comment>', $command));
        }
    }

    /**
     *
     * @param InputInterface $input            
     * @param OutputInterface $output            
     */
    protected function callStubGenerate(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("\n" . '<info>Creating getter and setter:</info>');
        
        $command = $this->getApplication()->find('generate:doctrine:entities');
        $arguments = array(
            'command' => $command->getName(),
            'name' => $input->getArgument('bundleName')
        );
        $input = new ArrayInput($arguments);
        
        $output->writeln(sprintf('call <comment>%s</comment>', $command->getName()));
        
        $returnCode = $command->run($input, $output);
        
        if ($returnCode != 0) {
            $output->writeln(sprintf('<error>%s failed...</error>', $command->getName()));
        }
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
