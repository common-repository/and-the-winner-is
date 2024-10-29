<?php

/*
 * Plugin Name: And The Winner Is&hellip;
 * Plugin URI: http://spencersokol.com/projects/and-the-winner-is/
 * Description: And The Winner Is&hellip;helps you manage product give-aways by allowing you to mark posts as "contests" and can then select a random comment from a contest post as the winner. This plugin creates <code>manage_contests</code> capability that can be given to other users with the <a href="http://wordpress.org/extend/plugins/capsman/" title="Capability Manager">Capability Manager</a> plugin. Requires WP 3.0, PHP 5 and JavaScript. Tested on WP 3.2 and PHP 5.3.
 * License: GPL3
 * Author: Spencer Sokol
 * Author URI: http://spencersokol.com/
 * Version: 1.1.1
 *
 *
 * 
 * Copyright (C) 2010-2011  Spencer Sokol
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 * 
 */

/**
 * Core plugin file for And The Winner Is... containing includes 
 * and defined constants and PHP version check
 *
 * @package AndTheWinnerIs
 */

define( 'ATWI_DOMAIN', 'and-the-winner-is' );
define( 'ATWI', __('And The Winner Is', ATWI_DOMAIN) );

define( 'ATWI_VERSION', '1.0' );
define( 'ATWI_VERSION_TXT', 'atwi_version' );

define( 'ATWI_PATH', WP_PLUGIN_DIR . '/' . ATWI_DOMAIN );
define( 'ATWI_URL', WP_PLUGIN_URL . '/' . ATWI_DOMAIN );

define( 'ATWI_WINNERS_TABLE', 'atwi_winners' );
define( 'ATWI_CAPABILITY', 'manage_contests' );

define( 'ATWI_POST_META_IS_CONTEST', '_and_the_winner_is_contest' );
define( 'ATWI_POST_META_WINNERS_UNCONFIRMED', '_and_the_winner_is' );
define( 'ATWI_POST_META_WINNERS_CONFIRMED', '_and_the_winner_is_confirmed' );
define( 'ATWI_POST_META_WINNERS_REJECTED', '_and_the_winner_is_rejected' );
define( 'ATWI_POST_META_NUMBER_OF_WINNERS', '_and_the_winner_is_number_of_winners' );

define( 'ATWI_FORM_POST_IS_CONTEST', 'atwi_is_contest' );
define( 'ATWI_FORM_POST_NUMBER_OF_WINNERS', 'atwi_number_of_winners' );

define( 'ATWI_ERROR_PERMISSION_DENIED', __('You do not have permission to do that.', ATWI_DOMAIN) );
define( 'ATWI_ERROR_INVALID_POST_ID', __('Invalid post ID.', ATWI_DOMAIN) );
define( 'ATWI_ERROR_INVALID_COMMENT_ID', __('Invalid comment ID.', ATWI_DOMAIN) );
define( 'ATWI_ERROR_COMMENTS_ARE_OPEN', __('Comments are still open for this post.', ATWI_DOMAIN) );
define( 'ATWI_ERROR_POST_NOT_A_CONTEST', __('Post is not a contest.', ATWI_DOMAIN) );
define( 'ATWI_ERROR_WINNER_ALREADY_CONFIRMED', __('This winner has already been confirmed.', ATWI_DOMAIN) );
define( 'ATWI_ERROR_WINNER_ALREADY_REJECTED', __('This winner has already been rejected.', ATWI_DOMAIN) );

define( 'ATWI_MESSAGE_PLEASE_WAIT', __('Please wait', ATWI_DOMAIN) );

// PHP Version check, die semi-gracefully
if (version_compare(PHP_VERSION, '5.0.0', '<')) {
	$message = ATWI.' '.__('requires PHP 5 or higher to work properly. Your current version is', ATWI_DOMAIN).' '.PHP_VERSION;
	echo '<div class="error"><p>'.$message.'</p></div>';
	wp_die($message, ATWI.' :: '.__('Requirement Error', ATWI_DOMAIN), 'back_link=true');
	die();
}

require_once(ATWI_PATH.'/ajax.php');
require_once(ATWI_PATH.'/contest.php');
require_once(ATWI_PATH.'/winner.php');
require_once(ATWI_PATH.'/atwi.php');

new AndTheWinnerIs(__FILE__);

?>