<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DeployToProduction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:deploy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'run some commands to deploy the app to production';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $supportsEmoji = $this->supportsEmoji();

            $this->info(
                ($supportsEmoji ? '🚀🚀🚀 ' : '[START]') 
                . ' Starting deployment to production...' 
                . $supportsEmoji ? ' 🚀🚀🚀' : ''
            );

            $commands = [
                'cache:clear',
                'config:clear',
                'config:cache',
                'route:cache',
                'view:cache'
            ];

            foreach ($commands as $command) {
                $this->info("Running {$command}...");
                $this->call($command);
            }

            // $this->info(($supportsEmoji ? '⚙️' : '[OPTIMIZE]') . ' Optimizing composer autoloader...');
            // $result = shell_exec('composer dump-autoload');

            // if ($result === null) {
            //     throw new \RuntimeException('Failed to optimize composer autoloader');
            // }

            $this->info(($supportsEmoji ? '✨✨✨ ' : '[OPTIMIZE]') . ' Optimizing Filament...');
            $this->call('filament:optimize');

            $this->info(($supportsEmoji ? '🎉🎉🎉 ' : '[SUCCESS]') . ' Deployment ready for your production!');

            $this->info(
                ($supportsEmoji ? '🔥🔥🔥 ' : '[SUCCESS]') 
                . ' KEEP ON FIGHTING TILL THE END!' 
                . $supportsEmoji ? '🔥🔥🔥' : ''
            );

        } catch (\Exception $e) {
            $this->error(($supportsEmoji ? '❌' : '[ERROR]') . ' Deployment failed: ' . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    private function supportsEmoji()
    {
        return stripos(PHP_OS, 'WIN') === false;
    }
}
