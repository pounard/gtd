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
 * Mark a quittance has being "acquittée", boolean means "à titre gracieux"
 * which means that quittance amount will not account in totals.
 */
final class QuittanceAcquitteCommand implements Command
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

/**
 * Generate "quittances acquittées" (frenglish my friend, best language in the
 * world) "courrier". 
 */
final class QuittanceGenerateCourrierCommand implements Command
{
    /** @var Identifier[] */
    public array $quittanceIdList = [];

    public function __construct(array $quittanceIdList)
    {
        foreach ($quittanceIdList as $quittanceId) {
            if (!$quittanceId instanceof Identifier) {
                throw new \InvalidArgumentException();
            }
            $this->quittanceIdList[] = $quittanceId;
        }
    }
}

/**
 * Generate Generate "quittances acquittées courrier" response.
 */
final class QuittanceGenerateCourrierResponse implements Response
{
    public Identifier $courrierId;

    public function __construct(Identifier $courrierId)
    {
        $this->courrierId = $courrierId;
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
