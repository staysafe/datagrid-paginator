<?php

declare(strict_types=1);

namespace StaySafe\Paginator\DataGrid\Particle;

use Particle\Filter\Filter;

class ParticleFilterFactory
{
    public static function create(): Filter
    {
        return new Filter();
    }
}
