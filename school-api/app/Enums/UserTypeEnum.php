<?php

namespace App\Enums;

enum UserTypeEnum: int
{
    case ADMIN = 0;
    case TEACHER = 1;
    case STUDENT = 2;
}
