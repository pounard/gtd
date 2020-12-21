<?php

declare(strict_types=1);

namespace Gtd\Application\Location\Command;

use Gtd\Domain\Location\Model\Paiement;
use Gtd\Shared\Domain\Model\Identifier;
use MakinaCorpus\CoreBus\CommandBus\Command;
use MakinaCorpus\CoreBus\CommandBus\Response;

/**
 * Generate quittances stubs for an active contrat from the contrat start date
 * until the current month. Already existing quittances will be left alone.
 */
final class QuittanceStubGenerateCommand implements Command
{
    public Identifier $contratId;

    public function __construct(Identifier $contratId)
    {
        $this->contratId = $contratId;
    }
}

/**
 * Register received paiement.
 */
final class PaiementAddCommand implements Command
{
    public Identifier $personneId;
    public float $montant; // @todo monetary
    public \DateTimeInterface $date;
    public ?string $typePaiement;

    public function __construct(
        Identifier $personneId,
        float $montant, // @todo monetary
        \DateTimeInterface $date,
        ?string $typePaiement
    ) {
        $this->personneId = $personneId;
        $this->montant = $montant;
        $this->date = $date;
        $this->typePaiement = $typePaiement;
    }
}

/**
 * Register paiement response.
 */
final class PaiementAddResponse implements Response
{
    public Paiement $paiement;

    public function __construct(Paiement $paiement)
    {
        $this->paiement = $paiement;
    }
}
