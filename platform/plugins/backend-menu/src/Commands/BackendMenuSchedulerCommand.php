<?php

namespace Impiger\BackendMenu\Commands;

use Impiger\BackendMenu\Repositories\Interfaces\BackendMenuInterface;
use Illuminate\Console\Command;
use Throwable;
use EmailHandler;
use BaseHelper;

class BackendMenuSchedulerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cms:backend_menu:scheduler';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Schedule Jobs';

    /**
     * @var AuditLogInterface
     */
    protected $backend_menuRepository;

    /**
     * RebuildPermissions constructor.
     *
     * @param backend_menuInterface $backend_menuRepository
     */
public function __construct(BackendMenuInterface $backend_menuRepository)
    {
        parent::__construct();
        $this->backend_menuRepository = $backend_menuRepository;
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
