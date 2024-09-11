<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Impiger\ACL\Models\Role;

class RoleSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $systemRoles = [
            [
                'name' => 'Superadmin',
                'slug' => 'superadmin',
                'description' => 'Superadmin',
                'permissions' => [
                    "annual-action-plan.index" , "annual-action-plan.create", "annual-action-plan.edit"
                    , "annual-action-plan.destroy" , "annual-action-plan.export" , "annual-action-plan.print"
                    , "core.appearance" , "menus.index" , "menus.create", "menus.edit"  , "menus.destroy"
                    , "theme.options"  , "plugins.blog", "posts.index", "posts.create", "posts.edit"
                    , "posts.destroy", "categories.index", "categories.create" , "categories.edit"
                    , "categories.destroy", "tags.index", "tags.create" , "tags.edit" , "tags.destroy"
                    , "hub-institution.index" , "hub-institution.create" , "hub-institution.edit"
                    , "hub-institution.destroy" , "hub-institution.export", "hub-institution.print"
                    , "plugins.master-detail", "master-detail.index" , "master-detail.create"
                    , "master-detail.edit" , "master-detail.destroy" , "master-detail.export"
                    , "master-detail.print" , "district.index"  , "district.create" , "district.edit"
                    , "district.destroy" , "district.export" , "district.print" , "division.index"
                    , "division.create", "division.edit" , "division.export"
                    , "division.print" , "specializations.index", "specializations.create"
                    , "specializations.edit" , "specializations.destroy" , "specializations.export"
                    , "specializations.print", "qualifications.index", "qualifications.create"
                    , "qualifications.edit" , "qualifications.destroy", "qualifications.export"
                    , "qualifications.print" , "milestone.index", "milestone.create"
                    , "milestone.edit" , "milestone.export" , "milestone.print" , "hub-type.index"
                    , "hub-type.create" , "hub-type.edit" , "hub-type.destroy" , "hub-type.export"
                    , "hub-type.print", "media.index" , "files.index" , "files.create" , "files.edit"
                    , "folders.index" , "folders.create" , "folders.edit" , "pages.index" , "pages.create"
                    , "pages.edit" , "pages.destroy", "simple-slider.index" , "simple-slider.create"
                    , "simple-slider.edit" , "simple-slider.destroy", "simple-slider-item.index"
                    , "simple-slider-item.create"  , "simple-slider-item.edit"  , "simple-slider-item.destroy"
                    , "spoke-registration.index", "spoke-registration.create" , "spoke-registration.edit"
                    , "spoke-registration.destroy", "spoke-registration.export", "spoke-registration.print"
                    , "core.system", "roles.index", "roles.create", "roles.edit", "roles.destroy"
                    , "audit-log.index", "audit-log.view", "plugins.user", "user.index", "user.create"
                    , "user.edit", "user.destroy", "user-address.index" , "user-address.create", "user-address.edit"
                    , "user-address.destroy", "training-title.index", "training-title.create", "training-title.edit", "training-title.destroy", "training-title.export"
                    , "training-title.print"
                ],
                'is_system' => 1,
                'is_admin' => 1,
                'is_enabled' => 1,
            ],
            [
                'name' => 'EDP Admin',
                'slug' => 'edp-admin',
                'description' => 'EDP Admin',
                'is_system' => 1,
                'is_admin' => 1,
                'is_enabled' => 1,
            ],
            [
                'name' => 'IEDP Admin',
                'slug' => 'iedp-admin',
                'description' => 'IEDP Admin',
                'is_system' => 1,
                'is_admin' => 1,
                'is_enabled' => 1,
            ],
            [
                'name' => 'IVP Admin',
                'slug' => 'ivp-admin',
                'description' => 'IVP Admin',
                'is_system' => 1,
                'is_admin' => 0,
                'is_enabled' => 1,
            ],
            [
                'name' => 'Regional Admin',
                'slug' => 'regional-admin',
                'description' => 'Regional Admin',
                'is_system' => 1,
                'is_admin' => 0,
                'is_enabled' => 1,
            ],
            [
                'name' => 'Vendor',
                'slug' => 'vendor',
                'description' => 'Vendor',
                'is_system' => 1,
                'is_admin' => 0,
                'is_enabled' => 1,
            ],
            [
                'name' => 'Innovators',
                'slug' => 'innovators',
                'description' => 'Innovators',
                'is_system' => 1,
                'is_admin' => 0,
                'is_enabled' => 1,
            ],
            [
                'name' => 'Mentor',
                'slug' => 'mentor',
                'description' => 'Mentor',
                'is_system' => 1,
                'is_admin' => 0,
                'is_enabled' => 1,
            ],
            [
                'name' => 'Candidate',
                'slug' => 'candidate',
                'description' => 'Candidate',
                'permissions' => array("training-title.index" => true, "training-title.export" => false, "training-title.print" => false),
                'is_system' => 1,
                'is_admin' => 0,
                'is_enabled' => 1,
            ],
            [
                'name' => 'Hub',
                'slug' => 'hub',
                'description' => 'Hub',
                'is_system' => 1,
                'is_admin' => 0,
                'is_enabled' => 1,
            ],
            [
                'name' => 'Spoke',
                'slug' => 'spoke',
                'description' => 'Spoke',
                'is_system' => 1,
                'is_admin' => 0,
                'is_enabled' => 1,
            ],
            [
                'name' => 'Spoke Student',
                'slug' => 'spoke-student',
                'description' => 'Spoke Student',
                'is_system' => 1,
                'is_admin' => 0,
                'is_enabled' => 1,
            ],
        ];


        foreach ($systemRoles as $role) {
            $rowExist = \DB::table('roles')->where('slug', $role['slug'])->get()->first();
            if(\Arr::has($role,'permissions') && is_array($role['permissions'])){
                $role['permissions'] = json_encode($role['permissions']);
            }
            if (empty($rowExist)) { 
                $role['created_at'] = date('Y-m-d H:i:s');
                \DB::table('roles')->insert($role);
            } else {
                $role['updated_at'] = date('Y-m-d H:i:s');
                \DB::table('roles')->where('id', $rowExist->id)->update($role);
            }
        }
    }

}
