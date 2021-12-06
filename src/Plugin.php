<?php
/**
 * Plugin.php
 *
 * @package   edd-generator
 * @copyright Copyright (c) 2021, Ashley Gibson
 * @license   GPL2+
 */

namespace EDD\Generator;

use EDD\Generator\Commands;
use EDD\Generator\Contracts\Command;

class Plugin
{
    /**
     * @var string Path to main plugin file.
     */
    private string $file;

    public function __construct($file)
    {
        $this->file = $file;
    }

    public function boot(): void
    {
        if (class_exists('WP_CLI')) {
            $this->registerCommands();
        }
    }

    private function registerCommands(): void
    {
        $commands = [
            Commands\GenerateReviews::class,
            Commands\GenerateNotifications::class,
        ];

        if (class_exists('EDDC')) {
            $commands[] = Commands\Commissions\GeneratePayouts::class;
        }

        foreach ($commands as $command) {
            if (! is_subclass_of($command, Command::class)) {
                throw new \RuntimeException(sprintf(
                    '%s must implement the %s interface.',
                    $command,
                    Command::class
                ));
            }

            \WP_CLI::add_command('edd generate '.$command::commandName(), $command);
        }
    }

}
