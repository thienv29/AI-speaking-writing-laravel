<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:clean 
                            {--attempts : Clean attempts table}
                            {--all : Clean all test data}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up test data from database (attempts, etc.)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $cleanAttempts = $this->option('attempts') || $this->option('all');
        
        if ($cleanAttempts) {
            $this->info('Cleaning attempts table...');
            $count = DB::table('attempts')->count();
            DB::table('attempts')->truncate();
            $this->info("Deleted {$count} attempts.");
        }
        
        if ($this->option('all')) {
            $this->info('Database cleaned successfully!');
        } else {
            $this->info('Done!');
        }
        
        return 0;
    }
}
