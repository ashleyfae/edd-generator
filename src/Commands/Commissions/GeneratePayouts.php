<?php
/**
 * GeneratePayouts.php
 *
 * @package   edd-generator
 * @copyright Copyright (c) 2021, Ashley Gibson
 * @license   GPL2+
 */

namespace EDD\Generator\Commands\Commissions;

use EDD\Generator\Commands\Command;
use EDD\Generator\Traits\MakesNumberOf;
use EDD\Generator\Traits\RandomCurrency;
use EDD\Generator\Traits\RandomUser;

class GeneratePayouts extends Command implements \EDD\Generator\Contracts\Command
{

    use MakesNumberOf, RandomUser, RandomCurrency;

    public static function commandName(): string
    {
        return 'commission-payouts';
    }

    /**
     * Generates Commission Payouts.
     *
     * ## OPTIONS
     *
     * [--number=<number>]
     * : Number of items to generate.
     * ---
     * default: 1
     *
     * [--status=<status>]
     * : Payout status. If omitted, it's assigned randomly.
     * ---
     * options:
     *     - failed
     *     - processing
     *     - paid
     *
     * @param  array  $assocArgs
     * @param  array  $args
     */
    public function __invoke(array $assocArgs, array $args): void
    {
        $this->makeFromArgs($args);
    }

    protected function makeItem(array $args): void
    {
        $args = [
            'user_id'       => $this->randomUserId(),
            'commissions'   => implode(',', $this->randomCommissions()),
            'amount'        => $this->faker->randomFloat(2, 0, 3000),
            'currency'      => $this->randomCurrency(),
            'status'        => $args['status'] ?? $this->randomPayoutStatus(),
            'payout_method' => 'manual',
            'created_by'    => $this->randomAdminId(),
            'date_created'  => gmdate('Y-m-d H:i:s'),
        ];

        /*
         * Being lazy by bypassing `eddc_add_payout()`, which has some validation that doesn't
         * work for me at this time.
         */
        $payoutId = edd_commissions()->payouts_db->insert($args, 'payout');

        if (empty($payoutId)) {
            throw new \RuntimeException('Failed to create payout.');
        }

        \WP_CLI::debug(sprintf('Payout ID: %d; Payout args: %s', $payoutId, json_encode($args)));
    }

    protected function randomCommissions(): array
    {
        global $wpdb;

        $tableName = edd_commissions()->commissions_db->table_name;

        $results = $wpdb->get_col($wpdb->prepare(
            "SELECT id FROM {$tableName} ORDER BY RAND() LIMIT %d",
            $this->faker->numberBetween(1, 30)
        ));

        return array_map('intval', $results);
    }

    protected function randomPayoutStatus(): string
    {
        $odds = $this->faker->numberBetween(1, 6);

        if ($odds === 6) {
            return 'failed';
        } elseif ($odds === 5) {
            return 'processing';
        } else {
            return 'paid';
        }
    }
}
