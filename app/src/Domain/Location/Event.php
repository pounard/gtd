<?php

declare(strict_types=1);

namespace Gtd\Domain\Location\Event;

use Gtd\Domain\Location\Model\Paiement;
use Gtd\Domain\Location\Model\Quittance;
use MakinaCorpus\CoreBus\EventBus\DomainEvent;

final class QuittanceAddedEvent implements DomainEvent
{
    public Quittance $quittance;

    public function __construct(Quittance $quittance)
    {
        $this->quittance = $quittance;
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
