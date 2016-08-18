<?php

namespace Trappar\AliceGenerator\Metadata\Driver;

use Doctrine\Common\Annotations\Reader;
use Metadata\Driver\DriverInterface;
use Metadata\MergeableClassMetadata;
use Trappar\AliceGenerator\Annotation as Fixture;
use Trappar\AliceGenerator\Metadata\PropertyMetadata;

class AnnotationDriver implements DriverInterface
{
    /**
     * @var Reader
     */
    private $reader;

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * @param \ReflectionClass $class
     *
     * @return \Metadata\ClassMetadata
     */
    public function loadMetadataForClass(\ReflectionClass $class)
    {
        $classMetadata                  = new MergeableClassMetadata($name = $class->name);
        $classMetadata->fileResources[] = $class->getFileName();

        foreach ($class->getProperties() as $property) {
            $propertyMetadata = new PropertyMetadata($name, $property->getName());
            $propertyAnnotations = $this->reader->getPropertyAnnotations($property);

            foreach ($propertyAnnotations as $annotation) {
                if ($annotation instanceof Fixture\Data) {
                    $propertyMetadata->staticData = $annotation->value;
                } elseif ($annotation instanceof Fixture\Faker) {
                    $propertyMetadata->fakerName = $annotation->name;
                    $propertyMetadata->fakerResolverType = $annotation->type;
                    $propertyMetadata->fakerResolverArgs = $annotation->arguments;
                } elseif ($annotation instanceof Fixture\Ignore) {
                    $propertyMetadata->ignore = true;
                }
            }

            $classMetadata->addPropertyMetadata($propertyMetadata);
        }

        return $classMetadata;
    }
}