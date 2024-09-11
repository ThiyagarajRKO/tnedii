<?php

use Impiger\Page\Models\Page;

return [
    // List supported modules or plugins
    'supported' => [
        Page::class,
        \Impiger\Institution\Models\Institution::class,
    ],
];
