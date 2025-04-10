<?php

namespace App\Enums;

enum CompanyUserStatus: string
{
    case Active = 'active';
    case Invited = 'invited';
    case Blocked = 'blocked';
}
