<?php

namespace Trappar\AliceGenerator\ReferenceNamer;

use Doctrine\Common\Util\ClassUtils;

class NamespaceNamer implements ReferenceNamerInterface
{
    protected $namespaceSeparator = '';
    protected $ignoredNamespaces = [];

    public function createReference($object, $key)
    {
        return $this->createPrefix($object).$key;
    }

    public function createPrefix($object)
    {
        $class = ClassUtils::getClass($object);

        $parts          = explode('\\', $class);
        $partCount      = count($parts);
        $namespaceParts = array_slice($parts, 0, $partCount - 1);
        $namespaceParts = array_diff($namespaceParts, $this->ignoredNamespaces);
        $className      = $parts[$partCount - 1];

        return
            implode($this->namespaceSeparator, $namespaceParts) .
            $this->namespaceSeparator .
            $className .
            '-';
    }

    /**
     * @param array $ignoredNamespaces
     * @return NamespaceNamer
     */
    public function setIgnoredNamespaces($ignoredNamespaces)
    {
        $this->ignoredNamespaces = $ignoredNamespaces;

        return $this;
    }

    /**
     * @param string $namespaceSeparator
     * @return NamespaceNamer
     */
    public function setNamespaceSeparator($namespaceSeparator)
    {
        $this->namespaceSeparator = $namespaceSeparator;

        return $this;
    }
}