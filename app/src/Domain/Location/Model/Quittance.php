<?php

declare(strict_types=1);

namespace Gtd\Domain\Location\Model;

use Gtd\Shared\Domain\Model\Identifier;
use Gtd\Shared\Domain\Model\UuidIdentifier;

final class Quittance
{
    public Identifier $id;
    public Identifier $contratId;
    /** Si acquittée, à quel paiement fait-elle référence ? */
    public ?Identifier $paiementId = null;
    public int $year;
    public int $month;
    public ?\DateTimeInterface $dateStart;
    public ?\DateTimeInterface $dateStop;
    public float $loyer; // @todo Monetary
    public float $provisionCharges; // @todo Monetary
    /** Est-elle acquitée ou non ? */
    public bool $acquitte = false;
    /** Si acquittée, acquittée à titre gracieux (travaux, autre arrangements) ? */
    public bool $gracieux = false;
    /** Date d'acquittement (dénormalisation de la table paiement si lié). */
    public ?\DateTimeInterface $dateAcquittement;

    public function __construct(
        int $year,
        int $month,
        \DateTimeInterface $dateStart,
        \DateTimeInterface $dateStop,
        float $loyer,
        float $provisionCharges
    ) {
        $this->contratId = UuidIdentifier::empty();
        $this->dateStart = $dateStart;
        $this->dateStop = $dateStop;
        $this->id = UuidIdentifier::empty();
        $this->loyer = $loyer;
        $this->month = $month;
        $this->provisionCharges = $provisionCharges;
        $this->year = $year;
    }
}
