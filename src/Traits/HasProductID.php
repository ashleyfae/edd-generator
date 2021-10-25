<?php
/**
 * HasProductID.php
 *
 * @package   edd-generator
 * @copyright Copyright (c) 2021, Ashley Gibson
 * @license   GPL2+
 */

namespace EDD\Generator\Traits;

trait HasProductID
{

    /**
     * Parses the specified product ID from the command arguments.
     * If one is not set, then a random product ID is queried and returned.
     *
     * @param  array  $args
     *
     * @return int
     * @throws \RuntimeException
     */
    public function getProductId(array $args): int
    {
        return (int) ($args['product'] ?? $this->randomProduct());
    }

    /**
     * @return int
     * @throws \RuntimeException
     */
    protected function randomProduct(): int
    {
        $results = get_posts([
            'post_type'   => 'download',
            'orderby'     => 'rand',
            'numberposts' => 1,
            'fields'      => 'ids',
        ]);

        if (empty($results[0])) {
            throw new \RuntimeException('No products found.');
        }

        return (int) $results[0];
    }

}
