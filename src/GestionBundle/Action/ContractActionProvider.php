<?php

namespace GestionBundle\Action;

use GestionBundle\Entity\Contract;
use GestionBundle\Entity\ContractListDisplay;
use MakinaCorpus\Calista\Action\AbstractActionProvider;
use MakinaCorpus\Calista\Action\Action;

class ContractActionProvider extends AbstractActionProvider
{
    /**
     * {@inheritdoc}
     */
    public function supports($item)
    {
        return $item instanceof Contract || $item instanceof ContractListDisplay;
    }

    /**
     * {@inheritdoc}
     */
    public function getActions($item, $primaryOnly = false, array $groups = [])
    {
        $ret = [];

        if ($item instanceof Contract) {
            $contractId = $item->getId();
        } else if ($item instanceof ContractListDisplay) {
            $contractId = $item->getContractId();
        } else {
            return $ret;
        }

        $ret[] = new Action("View", 'gestion_quittance_list', ['contract' => $contractId], 'th-list', 0, true);

        return $ret;
    }
}
