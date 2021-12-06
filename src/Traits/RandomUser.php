<?php
/**
 * RandomUser.php
 *
 * @package   edd-generator
 * @copyright Copyright (c) 2021, Ashley Gibson
 * @license   GPL2+
 */

namespace EDD\Generator\Traits;

trait RandomUser
{

    protected function randomUserId(): int
    {
        global $wpdb;

        return (int) $wpdb->get_var(
            "SELECT ID from {$wpdb->users} ORDER BY RAND() LIMIT 1"
        );
    }

    /**
     * Retrieves the ID of a random administrator.
     * Using a direct query here because I don't think `get_users()` supports ordering by random.
     *
     * @return int
     */
    protected function randomAdminId(): int
    {
        global $wpdb;

        return (int) $wpdb->get_var(
            "SELECT wp_users.ID
FROM {$wpdb->users}
INNER JOIN {$wpdb->usermeta}
ON ( {$wpdb->users}.ID = {$wpdb->usermeta}.user_id )
WHERE (
    ( ( {$wpdb->usermeta}.meta_key = 'wp_capabilities'
    AND {$wpdb->usermeta}.meta_value LIKE '%\"administrator\"%' ) )
    )
ORDER BY RAND()
LIMIT 1"
        );
    }

}
