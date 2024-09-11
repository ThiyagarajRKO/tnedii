<?php

namespace Impiger\SeoHelper\Entities\OpenGraph;

use Impiger\SeoHelper\Bases\MetaCollection as BaseMetaCollection;

class MetaCollection extends BaseMetaCollection
{
    /**
     * Meta tag prefix.
     *
     * @var string
     */
    protected $prefix = 'og:';

    /**
     * Meta tag name property.
     *
     * @var string
     */
    protected $nameProperty = 'property';
}
