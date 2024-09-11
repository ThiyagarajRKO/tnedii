<?php

namespace Impiger\Blog;

use Impiger\Blog\Models\Category;
use Impiger\Blog\Models\Tag;
use Impiger\Dashboard\Repositories\Interfaces\DashboardWidgetInterface;
use Impiger\Menu\Repositories\Interfaces\MenuNodeInterface;
use Schema;
use Impiger\PluginManagement\Abstracts\PluginOperationAbstract;

class Plugin extends PluginOperationAbstract
{
    public static function remove()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('post_tags');
        Schema::dropIfExists('post_categories');
        Schema::dropIfExists('posts');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('tags');

        app(DashboardWidgetInterface::class)->deleteBy(['name' => 'widget_posts_recent']);

        app(MenuNodeInterface::class)->deleteBy(['reference_type' => Category::class]);
        app(MenuNodeInterface::class)->deleteBy(['reference_type' => Tag::class]);
    }
}
