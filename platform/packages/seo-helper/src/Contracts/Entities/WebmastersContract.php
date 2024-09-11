<?php

namespace Impiger\SeoHelper\Contracts\Entities;

use Impiger\SeoHelper\Contracts\RenderableContract;

interface WebmastersContract extends RenderableContract
{
    /**
     * Make Webmaster instance.
     *
     * @param array $webmasters
     * @return $this
     */
    public static function make(array $webmasters = []);

    /**
     * Add a webmaster to collection.
     *
     * @param string $webmaster
     * @param string $content
     * @return $this
     */
    public function add($webmaster, $content);

    /**
     * Reset the webmaster collection.
     *
     * @return $this
     */
    public function reset();
}
