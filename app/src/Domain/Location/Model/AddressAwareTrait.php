<?php

declare(strict_types=1);

namespace Gtd\Domain\Location\Model;

trait AddressAwareTrait
{
    public ?string $addrComplement = null;
    public ?string $addrLine1 = null;
    public ?string $addrLine2 = null;
    public ?string $addrCity = null;
    public ?string $addrPostcode = null;
}
