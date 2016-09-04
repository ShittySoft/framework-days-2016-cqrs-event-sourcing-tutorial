<?php

declare(strict_types=1);

namespace Building\Domain\DomainEvent;

use Prooph\EventSourcing\AggregateChanged;
use Rhumsaa\Uuid\Uuid;

final class PersonWasCheckedIntoBuilding extends AggregateChanged
{
    public static function fromBuildingAndPersonName(Uuid $building, string $personName)
    {
        return self::occur($building->toString(), ['personName' => $personName]);
    }

    public function personName() : string
    {
        return $this->payload['personName'];
    }
}
