<?php

namespace Trappar\AliceGenerator\ObjectHandler;

use Trappar\AliceGenerator\DataStorage\ValueContext;

class DateTimeHandler implements ObjectHandlerInterface
{
    public function handle(ValueContext $valueContext)
    {
        if (!($datetime = $valueContext->getValue()) instanceof \DateTime) {
            return false;
        }

        $formatted = $datetime->format('Y-m-d H:i:s');

        if (strpos($formatted, ' 00:00:00') !== false) {
            $valueContext->setValue(sprintf(
                '<(new \DateTime("%s"))>',
                str_replace(' 00:00:00', '', $datetime->format('Y-m-d H:i:s'))
            ));
        } else {
            $valueContext->setValue(sprintf(
                '<(new \DateTime("%s", new \DateTimeZone("%s")))>',
                $datetime->format('Y-m-d H:i:s'),
                $datetime->getTimezone()->getName()
            ));
        }

        return true;
    }
}