<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Impiger\Workflows\Models\Workflows;

class WorkflowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $workflowData = [

            [
                'name' => "Organization Subscription",
                'slug' => "organization_subscription",
                'module_controller' => "Impiger\Organization\Models\OrganizationSubscription",
                'status' => 'published'
            ],

        ];
        foreach($workflowData as $workflow){
          $workflowExist = Workflows::where('slug',$workflow['slug'])->first();
          if (empty($workflowExist)) {
                $workflow = Workflows::create($workflow);
            }
        }

    }
}
