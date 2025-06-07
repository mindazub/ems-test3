<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class CleanChartImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'charts:cleanup {--days=7 : Number of days to keep chart images}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up old chart image files';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $daysToKeep = $this->option('days');
        $this->info("Cleaning up chart images older than {$daysToKeep} days...");

        $directory = public_path('charts');

        if (!File::exists($directory)) {
            $this->warn("Charts directory does not exist. Nothing to clean up.");
            return Command::SUCCESS;
        }

        $oldFiles = collect(File::files($directory))
            ->filter(function ($file) use ($daysToKeep) {
                return Carbon::createFromTimestamp($file->getMTime())
                    ->addDays($daysToKeep)
                    ->isPast();
            });

        $count = $oldFiles->count();

        if ($count === 0) {
            $this->info("No old chart images to clean up.");
            return Command::SUCCESS;
        }

        $this->info("Found {$count} old chart images to remove.");

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        foreach ($oldFiles as $file) {
            File::delete($file->getPathname());
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Successfully cleaned up {$count} old chart images.");

        return Command::SUCCESS;
    }
}
