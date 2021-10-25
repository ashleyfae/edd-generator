<?php
/**
 * Plugin Name: EDD Generator
 * Description: Generates sample data for Easy Digital Downloads and its extensions.
 * Version: 1.0
 * Author: Easy Digital Downloads
 * Author URI: https://easydigitaldownloads.com
 * License: GPL2+
 *
 * EDD Generator is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * EDD Generator is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with EDD Generator. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package   edd-generator
 * @copyright Copyright (c) 2021, Easy Digital Downloads
 * @license   GPL2+
 */

require_once dirname(__FILE__).'/vendor/autoload.php';

if (version_compare(phpversion(), '7.4', '>=')) {
    $plugin = new \EDD\Generator\Plugin(__FILE__);
    $plugin->boot();
}
