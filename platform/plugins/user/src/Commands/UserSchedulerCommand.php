<?php

namespace Impiger\User\Commands;

use Impiger\User\Repositories\Interfaces\UserInterface;
use Illuminate\Console\Command;
use Throwable;
use EmailHandler;
use BaseHelper;

class UserSchedulerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cms:user:scheduler';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Schedule Jobs';

    /**
     * @var AuditLogInterface
     */
    protected $userRepository;

    /**
     * RebuildPermissions constructor.
     *
     * @param userInterface $userRepository
     */
public function __construct(UserInterface $userRepository)
    {
        parent::__construct();
        $this->userRepository = $userRepository;
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
