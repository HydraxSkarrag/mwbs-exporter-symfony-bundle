<?php
namespace MwbsExporterBundle\Service;

use \MwbsExporterBundle\Core\Schema;
use \Symfony\Component\Console\Output\OutputInterface;
use \Symfony\Component\DependencyInjection\ContainerAware;

/**
 * Description of Exporter
 *
 * @author marc
 *        
 */
class Exporter extends ContainerAware
{

    /**
     * Schemas
     *
     * @var MwbsExporterBundle\Core\Schema[]
     *
     */
    protected $schemas = array();

    /**
     * Constructure
     *
     * @param type $schemas            
     *
     */
    public function __construct($schemas = array())
    {
        $this->setSchemas($schemas);
    }

    /**
     * Set the schema
     *
     * @param array $schemas            
     * @return \MwbsExporterBundle\Service\Exporter
     */
    public function setSchemas(array $schemas)
    {
        $this->schemas = $schemas;
        return $this;
    }

    /**
     * Get the current schemas
     *
     * @return MwbsExporterBundle\Core\Schema[]
     */
    public function getSchemas()
    {
        foreach ($this->schemas as $name => &$value) {
            if ($value instanceof Schema) {
                continue;
            }
            
            if (is_array($value)) {
                $value = new Schema($name, $value);
                $value->setContainer($this->container);
            }
        }
        
        return $this->schemas;
    }

    /**
     * Export
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output            
     */
    public function export(OutputInterface $output)
    {
        $bundles = array();
        foreach ($this->getSchemas() as $schema) {
            $output->writeln(sprintf('Exporting "<info>%s</info>" schema', $schema->getName()));
            $location = $schema->export();
            $output->writeln(sprintf('Saved to "<info>%s</info>".', $location));
            $bundles[] = array(
                'name' => $schema->getOption('bundle'),
                'path' => $schema->getBundle()->getPath()
            );
        }
    }
}
