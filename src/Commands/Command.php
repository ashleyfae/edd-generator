<?php
/**
 * Command.php
 *
 * @package   edd-generator
 * @copyright Copyright (c) 2021, Easy Digital Downloads
 * @license   GPL2+
 */

namespace EDD\Generator\Commands;

use Faker\Generator;

abstract class Command
{

    protected Generator $faker;

    public function __construct()
    {
        $this->faker = \Faker\Factory::create();
    }

}
