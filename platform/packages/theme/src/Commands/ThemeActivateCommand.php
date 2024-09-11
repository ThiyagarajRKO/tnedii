<?php

namespace Impiger\Theme\Commands;

use Impiger\Theme\Commands\Traits\ThemeTrait;
use Impiger\Theme\Services\ThemeService;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class ThemeActivateCommand extends Command
{

    use ThemeTrait;

    /**
     * @var ThemeService
     */
    public $themeService;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'cms:theme:activate
        {name : The theme that you want to activate}
        {--path= : Path to theme directory}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Activate a theme';

    /**
     * ThemeActivateCommand constructor.
     * @param ThemeService $themeService
     */
    public function __construct(ThemeService $themeService)
    {
        parent::__construct();
        $this->themeService = $themeService;
    }

    /**
     * Execute the console command.
     *
     * @return bool
     * @throws FileNotFoundException
     */
    public function handle()
    {
        if (!preg_match('/^[a-z0-9\-]+$/i', $this->argument('name'))) {
            $this->error('Only alphabetic characters are allowed.');
            return 1;
        }

        $result = $this->themeService->activate($this->argument('name'));

        if ($result['error']) {
            $this->error($result['message']);
            return 1;
        }

        $this->info($result['message']);

        return 0;
    }
}
