<?php

namespace Aspect\Lib\Entity;

use Aspect\Lib\Blueprint\Dto\Key;
use Aspect\Lib\Blueprint\Dto\Table;
use Aspect\Lib\Blueprint\Table\Primary;
use Aspect\Lib\Service\Repository\EntityInterface;
use Aspect\Lib\Service\Repository\RepositoryEntity;

#[Table('aspect_queue')]
class QueueEntity extends RepositoryEntity
{
    #[Primary]
    #[Key('ID')]
    public ?int $id = null;

    #[Key('SERIAL')]
    public string $serial;

    #[Key('SIGN')]
    public string $sign;

    #[Key('TAG')]
    public string $tag;

    #[Key('START_AT')]
    public int $startAt;

    #[Key('BUSY')]
    public bool $busy;

    public function isMustRunning(): bool
    {
        return $this->startAt <= now()->unix();
    }
}