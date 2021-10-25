<?php
/**
 * MakesNumberOf.php
 *
 * @package   edd-generator
 * @copyright Copyright (c) 2021, Ashley Gibson
 * @license   GPL2+
 */

namespace EDD\Generator\Traits;

trait MakesNumberOf {

	protected function makeFromArgs(array $args, string $itemName = 'items'): void
	{
		$number = $args['number'] ?? 1;

		$progress = \WP_CLI\Utils\make_progress_bar(
			'Creating '.$itemName.'...',
			$number
		);

		for ($i = 0; $i < $number; $i++) {
			try {
				$this->makeItem($args);

				$progress->tick();
			} catch (\Exception $e) {
				\WP_CLI::error($e->getMessage());
			}
		}
	}

	abstract protected function makeItem(array $args): void;

}
