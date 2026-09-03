<?php

namespace App\Global;

use OpenApi\Attributes as OA;

enum DatePeriod: string
{
    case P1D = 'p1d';
    case P2D = 'p2d';
    case P3D = 'p3d';
    case P4D = 'p4d';
    case P5D = 'p5d';
    case P6D = 'p6d';

    case P1W = 'p1w';
    case P2W = 'p2w';
    case P3W = 'p3w';
    case P4W = 'p4w';

    case P1M = 'p1m';
    case P3M = 'p3m';
    case P6M = 'p6m';

    case P1Y = 'p1y';
    case P2Y = 'p2y';
}
