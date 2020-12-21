<?php

declare(strict_types=1);

namespace Gtd\Domain\Location\Handler;

use Gtd\Application\Location\Command\PaiementAddCommand;
use Gtd\Application\Location\Command\PaiementAddResponse;
use Gtd\Domain\Location\Event\PaiementAddEvent;
use Gtd\Domain\Location\Repository\PaiementRepository;
use Gtd\Domain\Location\Repository\PersonneRepository;
use MakinaCorpus\CoreBus\CommandBus\AbstractCommandHandler;

final class PaiementHandler extends AbstractCommandHandler
{
    private PersonneRepository $personneRepository;
    private PaiementRepository $paiementRepository;

    public function __construct(
        PersonneRepository $personneRepository,
        PaiementRepository $paiementRepository
    ) {
        $this->paiementRepository = $paiementRepository;
        $this->personneRepository = $personneRepository;
    }

    public function doAdd(PaiementAddCommand $command): PaiementAddResponse
    {
        if (!$this->personneRepository->exists($command->personneId)) {
            throw new \DomainException(\sprintf("La personne avec l'identifiant '%s' n'existe pas.", $command->personneId));
        }
        if (0 >= $command->montant) {
            throw new \DomainException("Le montant d'un paiement doit être positif.");
        }
        if (new \DateTimeImmutable() < $command->date) {
            throw new \DomainException("Vous ne pouvez pas renseigner un paiement dans le futur.");
        }

        $paiement = $this->paiementRepository->create(
            $command->personneId,
            $command->montant,
            $command->date,
            $command->typePaiement
        );

        $this->notifyEvent(new PaiementAddEvent($paiement));

        return new PaiementAddResponse($paiement);
    }
}
