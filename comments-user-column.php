<?php
/**
 * Plugin Name: Comments User Column
 * Plugin URI:  http://themehybrid.com/plugins
 * Description: Displays the comment author's site display name in a new column on the "Comments" admin screen if they were logged in while commenting.
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
 * @package   CommentsUserColumns
 * @version   1.0.0
 * @author    Justin Tadlock <justin@justintadlock.com>
 * @copyright Copyright (c) 2015, Justin Tadlock
 * @link      http://themehybrid.com/plugins
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

final class Comments_User_Column {

	private function __construct() {

		add_filter( 'manage_edit-comments_columns', array( $this, 'manage_edit_comments_columns' ) );

		add_action( 'manage_comments_custom_column', array( $this, 'manage_comments_custom_column' ), 10, 2 );

		add_action( 'admin_head-edit-comments.php', array( $this, 'print_styles' ) );
	}

	public function manage_edit_comments_columns( $columns ) {


		$columns['user'] = esc_html__( 'User', 'comments-user-column' );


		// Move response column to the end.
		if ( isset( $columns['response'] ) ) {

			$response = $columns['response'];

			unset( $columns['response'] );

			$columns['response'] = $response;
		}

		return $columns;
	}

	public function manage_comments_custom_column( $column_name, $comment_id ) {

		if ( 'user' === $column_name ) {

			$comment = get_comment($comment_id);

			if ( $comment->user_id && $user = get_userdata( $comment->user_id ) ) {

				$url = add_query_arg( 'user_id', $comment->user_id, admin_url( 'edit-comments.php' ) );

				printf( '<a href="%s">%s</a>', esc_url( $url ), esc_html( $user->display_name ) );
			}
		}
	}

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
