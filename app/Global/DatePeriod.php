<?php

namespace App\Global;

use Carbon\CarbonInterval;
use OpenApi\Attributes as OA;

enum DatePeriod: string
{
    case P1D = 'P1D';
    case P2D = 'P2D';
    case P3D = 'P3D';
    case P4D = 'P4D';
    case P5D = 'P5D';
    case P6D = 'P6D';

    case P1W = 'P1W';
    case P2W = 'P2W';
    case P3W = 'P3W';
    case P4W = 'P4W';

    case P1M = 'P1M';
    case P3M = 'P3M';
    case P6M = 'P6M';

    case P1Y = 'P1Y';
    case P2Y = 'P2Y';

    public function toCarbonInterval(): CarbonInterval
    {
        return CarbonInterval::make($this->value);
    }
}
