<?php
/*
Plugin Name: Menu Visibility Control
Plugin URI: https://knowledge.buzz/menu-visibility-control
Description: Adds simple visibility options to WordPress menu items: show for everyone, only logged-in users, only logged-out users, or specific roles.
Version: 1.0.1
Author: KnowledgeBuzz
Author URI: https://knowledge.buzz
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: menu-visibility-control
Requires at least: 5.8
Tested up to: 6.8
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Menu_Visibility_Control {

    public function __construct() {
        add_filter( 'wp_nav_menu_objects', [ $this, 'filter_menu_items' ], 10, 2 );
        add_action( 'wp_nav_menu_item_custom_fields', [ $this, 'add_custom_fields' ], 10, 4 );
        add_action( 'wp_update_nav_menu_item', [ $this, 'save_custom_fields' ], 10, 3 );
    }

    /**
     * Filter menu items visibility
     */
    public function filter_menu_items( $items, $args ) {
        $filtered = [];
        foreach ( $items as $item ) {
            $state = get_post_meta( $item->ID, '_menu_item_mvc_state', true );
            $roles = get_post_meta( $item->ID, '_menu_item_mvc_roles', true );

            $show = true;
            if ( $state === 'logged_in' && ! is_user_logged_in() ) {
                $show = false;
            } elseif ( $state === 'logged_out' && is_user_logged_in() ) {
                $show = false;
            } elseif ( $state === 'roles' && is_user_logged_in() ) {
                $user   = wp_get_current_user();
                $show   = false;
                $roles  = is_array( $roles ) ? $roles : [];
                foreach ( $roles as $role ) {
                    if ( in_array( $role, (array) $user->roles, true ) ) {
                        $show = true;
                        break;
                    }
                }
            } elseif ( $state === 'roles' && ! is_user_logged_in() ) {
                $show = false;
            }

            if ( $show ) {
                $filtered[] = $item;
            }
        }
        return $filtered;
    }

    /**
     * Add custom fields in menu editor
     */
    public function add_custom_fields( $item_id, $item, $depth, $args ) {
        $state = get_post_meta( $item_id, '_menu_item_mvc_state', true );
        $roles = get_post_meta( $item_id, '_menu_item_mvc_roles', true );

        wp_nonce_field( 'menu_visibility_control_nonce_action', 'menu_visibility_control_nonce' );
        ?>
        <p class="description description-wide">
            <label for="edit-menu-item-mvc-state-<?php echo esc_attr( $item_id ); ?>">
                <?php esc_html_e( 'Visibility', 'menu-visibility-control' ); ?><br>
                <select id="edit-menu-item-mvc-state-<?php echo esc_attr( $item_id ); ?>"
                        class="widefat code edit-menu-item-mvc"
                        name="menu-item-mvc-state[<?php echo esc_attr( $item_id ); ?>]">
                    <option value="everyone" <?php selected( $state, 'everyone' ); ?>>
                        <?php esc_html_e( 'Everyone', 'menu-visibility-control' ); ?>
                    </option>
                    <option value="logged_in" <?php selected( $state, 'logged_in' ); ?>>
                        <?php esc_html_e( 'Logged In Users', 'menu-visibility-control' ); ?>
                    </option>
                    <option value="logged_out" <?php selected( $state, 'logged_out' ); ?>>
                        <?php esc_html_e( 'Logged Out Users', 'menu-visibility-control' ); ?>
                    </option>
                    <option value="roles" <?php selected( $state, 'roles' ); ?>>
                        <?php esc_html_e( 'User Roles', 'menu-visibility-control' ); ?>
                    </option>
                </select>
            </label>
        </p>
        <p class="description description-wide mvc-roles" style="margin-top:5px;">
            <label><?php esc_html_e( 'Select Roles (only applies if "User Roles" selected):', 'menu-visibility-control' ); ?></label><br>
            <?php
            global $wp_roles;
            foreach ( $wp_roles->roles as $role_key => $role ) :
                ?>
                <label style="margin-right:10px;">
                    <input type="checkbox"
                           name="menu-item-mvc-roles[<?php echo esc_attr( $item_id ); ?>][]"
                           value="<?php echo esc_attr( $role_key ); ?>"
                        <?php checked( is_array( $roles ) && in_array( $role_key, $roles, true ) ); ?>>
                    <?php echo esc_html( $role['name'] ); ?>
                </label>
            <?php endforeach; ?>
        </p>
        <?php
    }

    /**
     * Save custom fields
     */
    public function save_custom_fields( $menu_id, $menu_item_db_id, $args ) {
        $nonce_name = 'menu_visibility_control_nonce';

        if ( ! isset( $_POST[ $nonce_name ] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ $nonce_name ] ) ), 'menu_visibility_control_nonce_action' ) ) {
            return;
        }

        if ( isset( $_POST['menu-item-mvc-state'][ $menu_item_db_id ] ) ) {
            $state = sanitize_text_field( wp_unslash( $_POST['menu-item-mvc-state'][ $menu_item_db_id ] ) );
            update_post_meta( $menu_item_db_id, '_menu_item_mvc_state', $state );
        } else {
            delete_post_meta( $menu_item_db_id, '_menu_item_mvc_state' );
        }

        if ( isset( $_POST['menu-item-mvc-roles'][ $menu_item_db_id ] ) ) {
            $roles = array_map( 'sanitize_text_field', wp_unslash( $_POST['menu-item-mvc-roles'][ $menu_item_db_id ] ) );
            update_post_meta( $menu_item_db_id, '_menu_item_mvc_roles', $roles );
        } else {
            delete_post_meta( $menu_item_db_id, '_menu_item_mvc_roles' );
        }
    }
}

new Menu_Visibility_Control();
