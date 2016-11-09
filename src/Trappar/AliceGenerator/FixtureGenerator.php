<?php

namespace Trappar\AliceGenerator;

class FixtureGenerator
{
    /**
     * @var ValueVisitor
     */
    private $valueVisitor;
    /**
     * @var YamlWriterInterface
     */
    private $yamlWriter;

    public function __construct(
        ValueVisitor $valueVisitor,
        YamlWriterInterface $yamlWriter
    )
    {
        $this->yamlWriter   = $yamlWriter;
        $this->valueVisitor = $valueVisitor;
    }

    public function generateYaml($value, $fixtureGenerationContext = null)
    {
        $results = $this->generateArray($value, $fixtureGenerationContext);

        return $this->yamlWriter->write($results);
    }

    public function generateArray($value, $fixtureGenerationContext = null)
    {
        if (!$fixtureGenerationContext) {
            $fixtureGenerationContext = new FixtureGenerationContext();
        }

        $this->valueVisitor->setup($fixtureGenerationContext);
        $this->valueVisitor->visitSimpleValue($value);

        $results = $this->valueVisitor->getResults();

        if ($fixtureGenerationContext->isSortResultsEnabled()) {
            ksort($results, SORT_NATURAL);
            foreach ($results as &$objectType) {
                ksort($objectType, SORT_NATURAL);
            }
        }

        return $results;
    }
}