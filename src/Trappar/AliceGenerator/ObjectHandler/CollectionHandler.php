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
        if(is_a($collection, 'Doctrine\ORM\PersistentCollection') && ($collectionLimit = $fixturesGenerationContext->getCollectionLimit($collection->getTypeClass()->getName()))) {
            $refCollection = $valueContext->getMetadata()->reflection;
            $criteria = \Doctrine\Common\Collections\Criteria::create()->setMaxResults($collectionLimit);
            $refCollection->setAccessible(true);
            $limitedValue = $collection->matching($criteria);
            $refCollection->setValue($valueContext->getContextObject(), $limitedValue);

            $valueContext->setValue($limitedValue->toArray());
        } else {
            $valueContext->setValue($collection->toArray());
        }
        $valueContext->getValueVisitor()->visitArray($valueContext);

        return true;
    }

    public static function limitCollection($entity, $collectionName, $limit)
    {
        $refMatchTeams = new \ReflectionProperty($entity,$collectionName);

        $criteria = \Doctrine\Common\Collections\Criteria::create()->setMaxResults($limit);
        $refMatchTeams->setAccessible(true);
        $refMatchTeams->setValue($entity,$refMatchTeams->getValue($entity)->matching($criteria));
    }
}