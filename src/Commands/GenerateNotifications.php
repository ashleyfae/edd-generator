<?php
/**
 * GenerateNotifications.php
 *
 * @package   edd-generator
 * @copyright Copyright (c) 2021, Ashley Gibson
 * @license   GPL2+
 */

namespace EDD\Generator\Commands;

use EDD\Generator\Traits\MakesNumberOf;

class GenerateNotifications extends Command implements \EDD\Generator\Contracts\Command
{

    use MakesNumberOf;

    public static function commandName(): string
    {
        return 'notifications';
    }

    /**
     * Generates notifications.
     *
     * ## OPTIONS
     *
     * [--number=<number>]
     * : Number of notifications to generate.
     * ---
     * default: 1
     *
     * [--dismissed]
     * : If set, generated notifications will be dismissed.
     *
     * [--buttons]
     * : If set, buttons are always generated.
     *
     * @param  array  $assocArgs
     * @param  array  $args
     */
    public function __invoke(array $assocArgs, array $args): void
    {
        if (! isset(EDD()->notifications)) {
            \WP_CLI::error('Notifications not available.');
        }

        $this->makeFromArgs($args);
    }

    protected function makeItem(array $args): void
    {
        $args = [
            'title'     => $this->faker->words(rand(3, 7), true),
            'content'   => $this->faker->paragraph,
            'buttons'   => $this->randomButtons($args),
            'type'      => $this->randomType(),
            'dismissed' => isset($args['dismissed']),
        ];

        $notificationId = EDD()->notifications->insert($args);

        if (! $notificationId) {
            throw new \RuntimeException('Failed to create notification.');
        }

        \WP_CLI::debug(sprintf('Notification ID: %d; Notification args: %s', $notificationId, json_encode($args)));
    }

    private function randomButtons(array $args): ?array
    {
        // 50/50 chance of actually having buttons.
        if (! isset($args['buttons']) && rand(0, 1) === 0) {
            return null;
        }

        $buttons = [];

        for ($i = 1; $i <= rand(1, 2); $i++) {
            $buttons[] = [
                'type' => ($i === 1) ? 'primary' : 'secondary',
                'url'  => $this->faker->url,
                'text' => ($i === 1) ? 'Learn More' : $this->faker->words(rand(1, 3), true),
            ];
        }

        return $buttons;
    }

    private function randomType(): string
    {
        $types      = ['success', 'warning', 'error', 'info'];
        $chosenType = rand(0, (count($types) - 1));

        return $types[$chosenType];
    }
}
