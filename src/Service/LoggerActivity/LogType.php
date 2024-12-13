<?php

declare(strict_types=1);

namespace App\Service\LoggerActivity;

class LogType
{
    public const int COMPANY__REGISTERED             = 100;
    public const int COMPANY__REGISTRATION_CONFIRMED = 101;
    public const int COMPANY__ACCESS_TOKEN_CHANGED   = 102;
    public const int COMPANY__NAME_CHANGED           = 103;
    public const int COMPANY__EMAIL_CHANGED          = 104;
    public const int COMPANY__DELETED                = 105;

    public const int PERSON__ADDED                = 110;
    public const int PERSON__DELETED              = 111;
    public const int PERSON__NAME_CHANGED         = 112;
    public const int PERSON__PHONE_NUMBER_CHANGED = 113;
    public const int PERSON__EMAIL_CHANGED        = 114;
    public const int PERSON__NOTIFIER_CHANGED     = 115;

    public const int SERVICE__ADDED        = 120;
    public const int SERVICE__DELETED      = 121;
    public const int SERVICE__NAME_CHANGED = 122;

    public const int CONTRACT__CREATED            = 131;
    public const int CONTRACT__DELETED            = 132;
    public const int CONTRACT__NUMBER_CHANGED     = 133;
    public const int CONTRACT__PERIOD_CHANGED     = 134;
    public const int CONTRACT__MAX_AMOUNT_CHANGED = 135;

    public const int INSURED_PERSON__ADDED                   = 141;
    public const int INSURED_PERSON__DELETED                 = 142;
    public const int INSURED_PERSON__POLICY_NUMBER_CHANGED   = 143;
    public const int INSURED_PERSON__EXCEED_LIMIT_ALLOWED    = 144;
    public const int INSURED_PERSON__EXCEED_LIMIT_DISALLOWED = 145;

    public const int CONTRACT_SERVICE__ADDED         = 151;
    public const int CONTRACT_SERVICE__DELETED       = 152;
    public const int CONTRACT_SERVICE__LIMIT_CHANGED = 153;

    public const int PROVIDED_SERVICE__REGISTERED = 161;
    public const int PROVIDED_SERVICE__CANCELED   = 162;
}
