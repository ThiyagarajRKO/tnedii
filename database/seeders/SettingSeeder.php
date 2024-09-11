<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $settings = [
            [
                'key' => 'activated_plugins',
                'value' => ["language","audit-log","backup","cookie-consent","request-log","social-login","translation","log-viewer","maintenance-mode","impersonate","crud","password-criteria","user","master-detail","workflows","backend-menu","vendors","simple-slider","blog","annual-action-plan","training-title","division","hub-institution","spoke-registration","captcha","tnsi-startup","entrepreneur"]
            ],
            [
                'key' => 'enable_captcha',
                'value' => 0
            ],
            [
                'key' => 'admin_title',
                'value' => 'Government of EDII Tamil Nadu'
            ],
            [
                'key' => 'enable_change_admin_theme',
                'value' => 0
            ],[
                'key' => 'admin_logo',
                'value' => 'mask-group-1.png'
            ],
            [
                'key' => 'site_description',
                'value' => 'Entrepreneurs play an important role in the economic development of a country. Successful entrepreneurs innovate, bring new products and concepts to the market, improve market efficiency, build wealth, create jobs, and enhance economic growth. Entrepreneurs convert ideas into economic opportunities through innovations which are considered to be major source of competitiveness in an increasingly globalizing world economy.'
            ],
            [
                'key' => 'enable_force_pwd_change',
                'value' => 0
            ],
            [
                'key' => 'enable_force_pwd_change_roles.0',
                'value' => NULL
            ]
            ,[
                'key' => 'username_setting',
                'value' => 'both'
            ],[
                'key' => 'table_bulk_change',
                'value' => 0
            ],[
                'key' => 'table_bulk_delete',
                'value' => 0
            ],[
                'key' => 'role_form_custom_layout',
                'value' => 1
            ],
            [
                'key' => 'reset_action',
                'value' => 1
            ],
            [
                'key' => 'enable_dls',
                'value' => 1
            ],
            [
                'key' => 'user_level_permission',
                'value' => 0
            ],
            [
                'key' => 'custom_stats_view',
                'value' => 0
            ],
        ];
        foreach ($settings as $setting) {
            $rowExist = \DB::table('settings')->where('key', $setting['key'])->get()->first();
            if (empty($rowExist)) {
                $setting['created_at'] = date('Y-m-d H:i:s');
                \DB::table('settings')->insert($setting);
            } else {
                $setting['updated_at'] = date('Y-m-d H:i:s');
                \DB::table('settings')->where('id', $rowExist->id )->update($setting);
            }
        }
    }
}
