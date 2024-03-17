<?php

namespace App\Entity;

enum VisitCategory: string
{
    case CHECK_UP = "CHECK_UP";
    case SPECIALIST = "SPECIALIST";
    case FOLLOW_UP = "FOLLOW_UP";
    case ILLNESS = "ILLNESS";

    public function getDisplayName(): string
    {
        return match ($this) {
            self::CHECK_UP => 'Check-up',
            self::SPECIALIST => 'Specialist',
            self::FOLLOW_UP => 'Follow-up',
            self::ILLNESS => 'Illness',
        };
    }
}
