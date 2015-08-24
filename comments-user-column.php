<?php
/**
 * Plugin Name: Comments User Column
 * Plugin URI:  http://themehybrid.com/plugins/comments-user-column
 * Description: Displays a logged-in comment author's site display name in a new column on the comments admin screen.
 * Version:     1.0.0-dev
 * Author:      Justin Tadlock
 * Author URI:  http://justintadlock.com
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU
 * General Public License as published by the Free Software Foundation; either version 2 of the License,
 * or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * You should have received a copy of the GNU General Public License along with this program; if not, write
 * to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 *
 * @package   CommentsUserColumn
 * @version   1.0.0
 * @author    Justin Tadlock <justin@justintadlock.com>
 * @copyright Copyright (c) 2015, Justin Tadlock
 * @link      http://themehybrid.com/plugins/comments-user-column
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Plugin class.
 *
 * @since  1.0.0
 * @access public
 */
final class Comments_User_Column {

	/**
	 * Sets up and runs the plugin.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	private function __construct() {

		// Translations.
		add_action( 'plugins_loaded', array( $this, 'i18n' ) );

		// Custom columns.
		add_filter( 'manage_edit-comments_columns',  array( $this, 'manage_edit_comments_columns'  )        );
		add_action( 'manage_comments_custom_column', array( $this, 'manage_comments_custom_column' ), 10, 2 );

		// Custom styles.
		add_action( 'admin_head-edit-comments.php', array( $this, 'print_styles' ) );
	}

	/**
	 * Loads the translation files.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function i18n() {
		load_plugin_textdomain( 'comments-user-column', false, trailingslashit( dirname( plugin_basename( __FILE__ ) ) ). 'languages' );
	}

	/**
	 * Adds the custom "User" column to the edit comments screen.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array   $columns
	 * @return array
	 */
	public function manage_edit_comments_columns( $columns ) {

		// Add user column.
		$columns['user'] = esc_html__( 'User', 'comments-user-column' );

		// Move core WP response column to the end.
		if ( isset( $columns['response'] ) ) {

			$response = $columns['response'];

			unset( $columns['response'] );

			$columns['response'] = $response;
		}

		return $columns;
	}

	/**
	 * Outputs the user column content, which is the user's display name linked to
	 * all their comments on the site.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array   $columns
	 * @return array
	 */
	public function manage_comments_custom_column( $column_name, $comment_id ) {

		if ( 'user' === $column_name ) {

			$comment = get_comment($comment_id);

			if ( $comment->user_id && $user = get_userdata( $comment->user_id ) ) {

				$url = add_query_arg( 'user_id', $comment->user_id, admin_url( 'edit-comments.php' ) );

				printf( '<a href="%s">%s</a>', esc_url( $url ), esc_html( $user->display_name ) );
			}
		}
	}

	/**
	 * Outputs some custom CSS to shorten the width of the user column.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function print_styles() { ?>

		<style type="text/css" media="screen">.fixed .column-user { width: 15%; }</style>

	<?php }

	/**
	 * Returns the instance.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return object
	 */
	public static function get_instance() {

		static $instance = null;

		if ( is_null( $instance ) )
			$instance = new Comments_User_Column;

		return $instance;
	}
}

Comments_User_Column::get_instance();
