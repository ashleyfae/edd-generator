<?php
/**
 * Command.php
 *
 * @package   edd-generator
 * @copyright Copyright (c) 2021, Easy Digital Downloads
 * @license   GPL2+
 */

namespace EDD\Generator\Contracts;

interface Command
{

    public static function commandName(): string;

    public function __invoke(array $assocArgs, array $args): void;

}
