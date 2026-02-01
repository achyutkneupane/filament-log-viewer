<?php

declare(strict_types=1);

arch('no dd, dump, or ray calls')
    ->expect(['dd', 'dump', 'ray'])
    ->each
    ->not
    ->toBeUsed();

arch('enums are string backed')
    ->expect('AchyutN\FilamentLogViewer\Enums')
    ->toBeStringBackedEnums();

arch('traits are of type trait')
    ->expect('AchyutN\FilamentLogViewer\Traits')
    ->toBeTraits();
