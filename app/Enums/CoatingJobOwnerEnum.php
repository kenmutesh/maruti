<?php

namespace App\Enums;

enum CoatingJobOwnerEnum: Int
{
    case MARUTI = 1;

    case DIRECT = 2;

    case OWNERALUMINIUM = 3;

    case OWNERSTEEL = 4;

    case OWNERSTEELALUMINIUM = 5;


    public function humanreadablestring(): string
    {
        return match ($this) {
            CoatingJobOwnerEnum::MARUTI => 'Maruti',
            CoatingJobOwnerEnum::DIRECT => 'Maruti',
            CoatingJobOwnerEnum::OWNERALUMINIUM => 'Owner',
            CoatingJobOwnerEnum::OWNERSTEEL => 'Owner',
            CoatingJobOwnerEnum::OWNERSTEELALUMINIUM => 'Owner'
        };
    }

    public function coatingjobselectionstring(): string
    {
        return match ($this) {
            CoatingJobOwnerEnum::MARUTI => 'Maruti',
            CoatingJobOwnerEnum::DIRECT => 'Direct',
            CoatingJobOwnerEnum::OWNERALUMINIUM => 'Owner Aluminium',
            CoatingJobOwnerEnum::OWNERSTEEL => 'Owner Steel',
            CoatingJobOwnerEnum::OWNERSTEELALUMINIUM => 'Owner Combined'
        };
    }

    public function coatingjobselectionradiovalue(): string
    {
        return match ($this) {
            CoatingJobOwnerEnum::MARUTI => 'MARUTI',
            CoatingJobOwnerEnum::DIRECT => 'DIRECT-SALE',
            CoatingJobOwnerEnum::OWNERALUMINIUM => 'OWNER-ALUMINIUM',
            CoatingJobOwnerEnum::OWNERSTEEL => 'OWNER-STEEL',
            CoatingJobOwnerEnum::OWNERSTEELALUMINIUM => 'OWNER-COMBINED'
        };
    }
}
