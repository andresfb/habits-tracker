<?php

namespace App\Enums;

enum InvitationStatus: int
{
    case CREATED = 0;
    case APPROVED = 1;
    case REJECTED = 2;
    case REGISTERED = 3;
}
