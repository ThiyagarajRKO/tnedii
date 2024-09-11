<?php

namespace Database\Seeders;
use DB;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BackendMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $hostUrl = env('APP_URL');       
        $backendMenus =[
            [
                "menu_id" => "vendor_management",
                "parent_id" => NULL,
                "name" => "Vendor Management",
                "url" => $hostUrl."custom-link",
                "icon" => "fas fa-user-check",
                "priority" => 2
            ],
            [
                "menu_id" => "cms-plugins-vendor-request_1",
                "parent_id" => "vendor_management",
                "name" => "Vendor requests",
                "url" => $hostUrl."/admin/vendor-requests",
                "icon" => "",
                "priority" => 0
            ],
            [
                "menu_id" => "cms-plugins-approved-vendor",
                "parent_id" => "vendor_management",
                "name" => "Approved Invitations",
                "url" => $hostUrl."/admin/approved-vendor/approved",
                "icon" => "",
                "priority" => 1
            ],
            
            
        ];
//        dd($backendMenus);
        foreach ($backendMenus as $backendMenu) {
            $menuExist = DB::table('backend_menus')->where($backendMenu)->get()->first();
            if (empty($menuExist)) {
                DB::table('backend_menus')->insert($backendMenu);
            } else {
                DB::table('backend_menus')->where('id', $menuExist->id )->update($backendMenu);
            }
        }

        
    }
}
