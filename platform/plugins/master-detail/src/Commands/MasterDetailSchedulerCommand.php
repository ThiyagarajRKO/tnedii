<?php

namespace Impiger\MasterDetail\Commands;

use Impiger\MasterDetail\Repositories\Interfaces\MasterDetailInterface;
use Illuminate\Console\Command;
use Throwable;
use EmailHandler;
use BaseHelper;

class MasterDetailSchedulerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cms:master_detail:scheduler';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Schedule Jobs';

    /**
     * @var AuditLogInterface
     */
    protected $master_detailRepository;

    /**
     * RebuildPermissions constructor.
     *
     * @param master_detailInterface $master_detailRepository
     */
public function __construct(MasterDetailInterface $master_detailRepository)
    {
        parent::__construct();
        $this->master_detailRepository = $master_detailRepository;
    }

    /**
     * Execute the console command.
     *
     * @throws Throwable
     */
    public function handle()
    {
        $this->info('Processing...');
        
        $this->info('Done!');
    }
    
   

}
