<?php

namespace Building\Domain\Aggregate;

use Building\Domain\DomainEvent\NewBuildingWasRegistered;
use Building\Domain\DomainEvent\PersonWasCheckedIntoBuilding;
use Building\Domain\DomainEvent\PersonWasCheckedOutOfBuilding;
use Prooph\EventSourcing\AggregateRoot;
use Rhumsaa\Uuid\Uuid;

final class Building extends AggregateRoot
{
    /**
     * @var Uuid
     */
    private $uuid;

    /**
     * @var string
     */
    private $name;

    /**
     * @var array<string, bool>
     */
    private $peopleThatCheckedIn = [];

    public static function new($name) : self
    {
        $self = new self();

        $self->recordThat(NewBuildingWasRegistered::occur(
            (string) Uuid::uuid4(),
            [
                'name' => $name
            ]
        ));

        return $self;
    }

    public function checkInUser(string $username)
    {
        if (isset($this->peopleThatCheckedIn[$username])) {
            throw new \LogicException(sprintf(
                'Person "%s" did already check in.',
                $username
            ));
        }

        $this->recordThat(PersonWasCheckedIntoBuilding::fromBuildingAndPersonName(
            $this->uuid,
            $username
        ));
    }

    public function checkOutUser(string $username)
    {
        if (! isset($this->peopleThatCheckedIn[$username])) {
            throw new \LogicException(sprintf(
                'Person "%s" did not check in.',
                $username
            ));
        }

        $this->recordThat(PersonWasCheckedOutOfBuilding::fromBuildingAndPersonName(
            $this->uuid,
            $username
        ));
    }

    public function whenNewBuildingWasRegistered(NewBuildingWasRegistered $event)
    {
        $this->uuid = $event->uuid();
        $this->name = $event->name();
    }

    public function whenPersonWasCheckedIntoBuilding(PersonWasCheckedIntoBuilding $event)
    {
        $this->peopleThatCheckedIn[$event->personName()] = true;
    }

    public function whenPersonWasCheckedOutOfBuilding(PersonWasCheckedOutOfBuilding $event)
    {
        unset($this->peopleThatCheckedIn[$event->personName()]);
    }

    /**
     * {@inheritDoc}
     */
    protected function aggregateId() : string
    {
        return (string) $this->uuid;
    }

    /**
     * {@inheritDoc}
     */
    public function id() : string
    {
        return $this->aggregateId();
    }
}
