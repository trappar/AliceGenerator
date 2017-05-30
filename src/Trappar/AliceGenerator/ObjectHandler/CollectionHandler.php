<?php

namespace Trappar\AliceGenerator\ObjectHandler;

use Trappar\AliceGenerator\DataStorage\ValueContext;

class CollectionHandler implements ObjectHandlerInterface
{
    /**
     * @param ValueContext $valueContext
     * @return bool true if the handler changed the value, false otherwise
     */
    public function handle(ValueContext $valueContext)
    {
        if (!is_a($collection = $valueContext->getValue(), 'Doctrine\Common\Collections\Collection')) {
            return false;
        }

        $fixturesGenerationContext = $valueContext->getValueVisitor()->getFixtureGenerationContext();
        if($fixturesGenerationContext->getSkipNotInitializedCollections() && !$collection->isInitialized()) {
            $valueContext->setValue([]);
        } else {
            $valueContext->setValue($collection->toArray());
        }
        $valueContext->getValueVisitor()->visitArray($valueContext);

        return true;
    }
}