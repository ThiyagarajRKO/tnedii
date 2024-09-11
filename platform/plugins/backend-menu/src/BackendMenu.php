<?php

namespace Impiger\BackendMenu;

use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\BackendMenu\Repositories\Interfaces\BackendMenuInterface;
use Impiger\Support\Services\Cache\Cache;
use Collective\Html\HtmlBuilder;
use Exception;
use Illuminate\Cache\CacheManager;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Theme;
use Throwable;
use Impiger\BackendMenu\Models\BackendMenu AS BackendMenuModel;

class BackendMenu
{
    /**
     * @var BackendMenuInterface
     */
    protected $backendMenuRepository;

    /**
     * @var Array
     */
    protected $allowedMenus = [];

    /**
     * Menu constructor.
     * @param BackendMenuInterface $backendMenuRepository
     * @param HtmlBuilder $html
     */
    public function __construct(
        BackendMenuInterface $backendMenuRepository,
        HtmlBuilder $html
    ) {
        $this->allowedMenus = $this->getAllowedMenus();
        $this->backendMenuRepository = $backendMenuRepository;
        $this->html = $html;
    }
    

    /**
     * @param array $args
     * @return mixed|null|string
     * @throws Throwable
     */
    public function generateMenu(array $args = [])
    {
        $view = Arr::get($args, 'view');
        $menu = Arr::get($args, 'menu');
        $slug = Arr::get($args, 'slug');

        if (!$menu && !$slug) {
            return null;
        }

        $parentId = Arr::get($args, 'parent_id', NULL);
        $menuNodes = [];

        if (Arr::has($args, 'menu_nodes')) {
            $menuNodes = Arr::get($args, 'menu_nodes');
        } else {
            $menuNodes = $this->getBackEndMenus();
        }

        if ($menuNodes instanceof Collection) {
            $menuNodes = $menuNodes->sortBy('priority');
        }

        $data = [
            'menuNodes' => $menuNodes,
        ];

        $data['options'] = $this->html->attributes(Arr::get($args, 'options', []));

        if ($view) {
            return view($view, $data)->render();
        }
    }

    public function getBackEndMenus($parentId = NULL,  $allowedMenus = NULL) {
        $menus = $this->backendMenuRepository->allBy(['parent_id' => NULL])->toArray();
        $menus = $this->getRecursiveBackendMenus($menus, $allowedMenus);
        return $menus;
    }

    public function getRecursiveBackendMenus(&$menus, $allowedMenus = NULL) {
            foreach ($menus as &$menu) {
                $menuId = Arr::get($menu, 'menu_id');
                $menu['active'] = false;
                if($allowedMenus) {
                    $children = $this->backendMenuRepository->allBy(['parent_id' => $menuId,array('id' ,'!=',$menu['id'])])->toArray();
                    $menu['children'] = [];
                    foreach($children as $row) {
                        if(in_array($row['menu_id'], $allowedMenus)) {
                            $menu['children'][] = $row;
                        }
                    }
                } else {
                    $menu['children'] = $this->backendMenuRepository->allBy(['parent_id' => $menuId,array('id' ,'!=',$menu['id'])])->toArray();
                }
                if(Arr::has($menu['children'], 0)) {
                    $this->getRecursiveBackendMenus($menu['children']);
                }
            }

        return $menus;
    }

    public function getAllowedMenus() {
        $originalMenus = dashboard_menu()->getAll();
        $menuList = [];

        foreach($originalMenus as $menu) {
            $menuList[$menu['id']] = $menu;

            if(isset($menu['children']) && count($menu['children']) > 0) {
                foreach(Arr::get($menu,'children') as $k => $submenu) {
                    $menuList[$submenu['id']] = $submenu;
                }
            }

        }

        return $menuList;
    }

    public function getDynamicMenus() {
        $allowedMenus = $this->allowedMenus;
        $dynamicMenus = $this->getBackEndMenus(NULL, array_keys($this->allowedMenus));
        $menuList = [];
        
        foreach($dynamicMenus as $menu) {
            $parentMenuConfig = Arr::has($allowedMenus, $menu['menu_id']) ? Arr::get($allowedMenus, $menu['menu_id']) : NULL;

            if(Arr::has($menu,'children.0')) {
                $hasChildMenu = false;

                foreach(Arr::get($menu,'children') as $k => $submenu) {
                    $menuConfig = Arr::has($allowedMenus, $submenu['menu_id']) ? Arr::get($allowedMenus, $submenu['menu_id']) : [] ;
                    
                    if(Arr::get($menuConfig, 'id')) {
                        if(Arr::get($menuConfig, 'active')) {
                            $menu['active'] = true;
                        }
                        $hasChildMenu = true;
                    }
                }

                if($hasChildMenu) {
                    $menuList[$menu['menu_id']] = $menu;
                }
            } else {
                if($parentMenuConfig) {
                    $menuList[$menu['menu_id']] = $menu;
                }
            }
        }

        return $menuList;
    }

    public function mapMenuConfig($menu) {
        $menuConfig = Arr::get($this->allowedMenus, Arr::get($menu, 'menu_id'));

        if($menuConfig) {
            $menu['permissions'] = $menuConfig['permissions'];
            $menu['url'] = $menuConfig['url'];
            $menu['active'] = $menuConfig['active'];
            $menu['name'] = ($menu['name']) ? $menu['name']: $menuConfig['name'];
            $menu['icon'] = ($menu['icon']) ? $menu['icon'] :  $menuConfig['icon'];
            $menu['id'] = $menu['menu_id'];

            if(Arr::get($menuConfig, 'active')) {
                $menu['active'] = true;
            }
        }
        return $menu;
    }

    /**
     * @param array $args
     * @return mixed|null|string
     * @throws Throwable
     */
    public function renderDynamicMenus(array $args = [])
    {
        $view = Arr::get($args, 'view');
        $isSubmenu = Arr::get($args, 'is_submenu');

        if (!$view) {
            return null;
        }

        $menuNodes = [];

        if (Arr::has($args, 'menu_nodes')) {
            $menuNodes = Arr::get($args, 'menu_nodes');
        } else {
            $menuNodes = $this->getDynamicMenus();
        }

        $menus = [];
        foreach($menuNodes as $menu) {
            $menu = $this->mapMenuConfig($menu);
            $menus[] = $menu;
        }

        $data = [
            'menuNodes' => $menus,
            'isSubmenu' => ($isSubmenu) ? true : false
        ];

        return view($view, $data)->render();
    }
}
