<?php

declare(strict_types=1);

namespace Gtd\Domain\Location\Event;

use Gtd\Domain\Location\Model\Paiement;
use Gtd\Domain\Location\Model\Quittance;
use Gtd\Shared\Domain\Model\Identifier;
use MakinaCorpus\CoreBus\EventBus\DomainEvent;

final class QuittanceAddedEvent implements DomainEvent
{
    public Quittance $quittance;

    public function __construct(Quittance $quittance)
    {
        $this->quittance = $quittance;
    }
}

final class QuittanceAcquittedEvent implements DomainEvent
{
    public Identifier $quittanceId;
    public bool $gracieux;
    public ?\DateTimeInterface $dateAcquittement;

    public function __construct(Identifier $quittanceId, bool $gracieux, ?\DateTimeInterface $dateAcquittement)
    {
        $this->gracieux = $gracieux;
        $this->quittanceId = $quittanceId;
        $this->dateAcquittement = $dateAcquittement;
    }
}

final class PaiementAddEvent implements DomainEvent
{
    public Paiement $paiement;

    public function __construct(Paiement $paiement)
    {
        $this->paiement = $paiement;
    }
}
