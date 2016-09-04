<?php

declare(strict_types=1);

namespace Building\Domain\Command;

use Prooph\Common\Messaging\Command;
use Rhumsaa\Uuid\Uuid;

final class CheckPersonOutOfBuilding extends Command
{
    /**
     * @var string
     */
    private $personName;
    /**
     * @var Uuid
     */
    private $buildingId;

    private function __construct(string $personName, Uuid $buildingId)
    {
        $this->init();

        $this->personName = $personName;
        $this->buildingId = $buildingId;
    }

    public static function fromNameAndBuilding(string $personName, Uuid $buildingId) : self
    {
        return new self($personName, $buildingId);
    }

    public function personName() : string
    {
        return $this->personName;
    }

    public function buildingId() : Uuid
    {
        return $this->buildingId;
    }

    /**
     * {@inheritDoc}
     */
    public function payload() : array
    {
        return [
            'personName' => $this->personName,
            'buildingId' => $this->buildingId->toString(),
        ];
    }

    /**
     * {@inheritDoc}
     */
    protected function setPayload(array $payload)
    {
        $this->personName = $payload['personName'];
        $this->buildingId = Uuid::fromString($payload['buildingId']);
    }
}
