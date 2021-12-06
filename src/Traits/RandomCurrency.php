<?php
/**
 * RandomCurrency.php
 *
 * @package   edd-generator
 * @copyright Copyright (c) 2021, Ashley Gibson
 * @license   GPL2+
 */

namespace EDD\Generator\Traits;

trait RandomCurrency
{

    /**
     * Returns a random currency, favouring the odds of returning USD, then GBP. Tiny
     * chance of using Faker to randomize it.
     *
     * @return string
     */
    public function randomCurrency(): string
    {
        $odds = $this->faker->numberBetween(1, 10);

        // 1-5 returns USD.
        if ($odds <= 5) {
            return 'USD';
        }

        if ($odds <= 8) {
            return 'GBP';
        }

        return strtoupper($this->faker->currencyCode);
    }

}
