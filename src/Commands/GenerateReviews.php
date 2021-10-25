<?php
/**
 * GenerateReviews.php
 *
 * @package   edd-generator
 * @copyright Copyright (c) 2021, Easy Digital Downloads
 * @license   GPL2+
 */

namespace EDD\Generator\Commands;

use EDD\Generator\Contracts\Command;
use EDD\Generator\Traits\HasProductID;

class GenerateReviews extends \EDD\Generator\Commands\Command implements Command
{
    use HasProductID;

    public static function commandName(): string
    {
        return 'reviews';
    }

    /**
     * Generates reviews.
     *
     * ## OPTIONS
     *
     * [--number=<number>]
     * : Number of reviews to generate.
     * ---
     * default: 1
     *
     * [--product=<product_id>]
     * : ID of the product to create reviews for. If omitted, random product ID is used
     * for each review.
     *
     * [--status=<status>]
     * : Review status. If omitted, it's assigned randomly.
     * ---
     * options:
     *     - 1
     *     - 0
     *     - spam
     *     - trash
     *
     * @param  array  $assocArgs
     * @param  array  $args
     */
    public function __invoke(array $assocArgs, array $args): void
    {
        $number = $args['number'] ?? 1;

        $progress = \WP_CLI\Utils\make_progress_bar(
            'Creating reviews...',
            $number
        );

        for ($i = 0; $i < $number; $i++) {
            try {
                $this->makeReview($args);

                $progress->tick();
            } catch (\Exception $e) {
                \WP_CLI::error($e->getMessage());
            }
        }
    }

    private function makeReview(array $args): void
    {
        $date = $this->faker->dateTimeBetween('-1 year')->format('Y-m-d H:i:s');

        $meta = [
            'edd_review_title'    => $this->faker->words(rand(2, 5), true),
            'edd_review_approved' => $this->getStatus($args),
        ];

        $rating = $this->faker->numberBetween(0, 5);
        if ($rating > 0) {
            $meta['edd_rating'] = $rating;
        }

        foreach (['edd_review_vote_yes', 'edd_review_vote_no'] as $metaField) {
            $numberVotes = $this->faker->numberBetween(0, 10);
            if ($numberVotes > 0) {
                $meta[$metaField] = $numberVotes;
            }
        }

        $commentArgs = [
            'comment_post_ID'   => $this->getProductId($args),
            'comment_author'    => $this->faker->name(),
            'comment_author_IP' => $this->faker->ipv4(),
            'comment_content'   => $this->faker->paragraph(),
            'comment_date'      => $date,
            'comment_date_gmt'  => $date,
            'comment_type'      => 'edd_review',
            'comment_meta'      => $meta,
        ];

        $reviewId = wp_insert_comment($commentArgs);

        if (empty($reviewId)) {
            throw new \RuntimeException('Failed to create review.');
        }

        \WP_CLI::debug(sprintf('Review ID: %d; Review args: %s', $reviewId, json_encode($commentArgs)));
    }

    private function getStatus(array $args): string
    {
        $allowedStatuses = [
            '1', // approved
            '0', // unapproved
            'spam',
            'trash'
        ];
        if (isset($args['status'])) {
            if (in_array($args['status'], $allowedStatuses)) {
                return (string) $args['status'];
            } else {
                throw new \InvalidArgumentException(sprintf(
                    'Invalid status %s. Must be one of: %s',
                    $args['status'],
                    json_encode($allowedStatuses)
                ));
            }
        }

        /*
         * Generate a random status.
         * We generate a random number to help assign a status, giving the greatest
         * chance to "approved".
         */
        $number    = rand(1, 10);
        $statusMap = [
            // Numbers 1-7 go to `1` (approved)
            8  => '0',
            9  => 'spam',
            10 => 'trash',
        ];

        return $statusMap[$number] ?? '1';
    }
}
