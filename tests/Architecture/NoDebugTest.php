<?php

arch('it will not use debugging functions')
    ->expect(['dd', 'ddd', 'die', 'dump', 'ray', 'rd', 'sleep'])
    ->each->not->toBeUsed();
