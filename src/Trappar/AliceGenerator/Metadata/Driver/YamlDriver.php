<?php

namespace Trappar\AliceGenerator\Metadata\Driver;

use Metadata\Driver\AbstractFileDriver;
use Metadata\MergeableClassMetadata;
use Symfony\Component\Yaml\Yaml;
use Trappar\AliceGenerator\Exception\RuntimeException;
use Trappar\AliceGenerator\Metadata\PropertyMetadata;

class YamlDriver extends AbstractFileDriver
{
    /**
     * Parses the content of the file, and converts it to the desired metadata.
     *
     * @param \ReflectionClass $class
     * @param string           $file
     *
     * @return \Metadata\ClassMetadata|null
     */
    protected function loadMetadataFromFile(\ReflectionClass $class, $file)
    {
        $config = Yaml::parse(file_get_contents($file));

        if (!isset($config[$name = $class->name])) {
            throw new RuntimeException(sprintf('Expected metadata for class %s to be defined in %s.', $class->name, $file));
        }

        $config                         = $config[$name];
        $classMetadata                  = new MergeableClassMetadata($name);
        $classMetadata->fileResources[] = $file;
        $classMetadata->fileResources[] = $class->getFileName();

        $propertiesMetadata = [];

        foreach ($class->getProperties() as $property) {
            $pName                      = $property->getName();
            $propertiesMetadata[$pName] = new PropertyMetadata($name, $pName);
        }

        foreach ($config as $pName => $pConfig) {
            $propertyMetadata = $propertiesMetadata[$pName];

            if (isset($pConfig['data'])) {
                $propertyMetadata->staticData = $pConfig['data'];
            } elseif (isset($pConfig['faker'])) {
                $fConfig = $pConfig['faker'];

                if (isset($fConfig['name'])) {
                    $propertyMetadata->fakerName = (string)$fConfig['name'];
                }
                if (isset($fConfig['type'])) {
                    $propertyMetadata->fakerResolverType = (string)$fConfig['type'];
                }
                if (isset($fConfig['arguments'])) {
                    $propertyMetadata->fakerResolverArgs = (array)$fConfig['arguments'];
                }
            } elseif (isset($pConfig['ignore'])) {
                $propertyMetadata->ignore = (Boolean)$pConfig['ignore'];
            }
        }

        foreach ($propertiesMetadata as $propertyMetadata) {
            $classMetadata->addPropertyMetadata($propertyMetadata);
        }

        return $classMetadata;
    }

    /**
     * Returns the extension of the file.
     *
     * @return string
     */
    protected function getExtension()
    {
        return 'yml';
    }
}