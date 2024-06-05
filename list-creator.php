<?php
/*
Plugin Name: MNA List Creator
Plugin URI: www.mnakhtar.com
Author: Nur Akhtar
Version: 1.0.0
Description: This is a test plugin
Text Domain: mnakhtar-lang
 */

if ( !defined( 'ABSPATH') ){
    exit;
}

register_activation_hook( __FILE__, 'mna_list_creator_install' );

function mna_list_creator_install() {
    global $wpdb;

    $tablename = $wpdb->prefix . 'list_creator';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $tablename(
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        name varchar(50) NOT NULL,
        email varchar(50) NOT NULL,
        phone varchar(50) NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}


/**  Register DEACTIVATION HOOK */
register_deactivation_hook(__FILE__, 'mna_list_creator_deactivate' );
function mna_list_creator_deactivate(){
    global $wpdb;

    $tablename =$wpdb->prefix . 'list_creator';

    $sql = "DROP TABLE IF EXISTS $table_name;";

    $result = $wpdb->query($sql);

    if ($result === false) {
        error_log('Failed to drop table: ' . $wpdb->last_error);
    } else {
        error_log('Table dropped successfully.');
    }
}

//Enque Styles and Scripts
function mna_list_creator_scripts(){
    wp_enqueue_script( 'mna-list-creator', plugin_dir_url(__FILE__) . 'js/mna-list-creator.js', array( 'jquery' ), '1.0.0', true );
    wp_localize_script( 'mna-list-creator', 'mna_list_object', array( 'ajax_url' => admin_url('admin-ajax.php' )) );
}
add_action( 'admin_enqueue_scripts', 'mna_list_creator_scripts' );

//Create Admin Menu for List Creator
function list_creator_admin_menu(){
    add_menu_page( 'MNA List Creator', 'MNA List Creator', 'manage_options', 'mna-list-creator', 'mna_list_creator_page' );
}
add_action( 'admin_menu', 'list_creator_admin_menu' );

function mna_list_creator_page() {
    ?>
        <div class="wrap">
            <h2>Create Your List Here</h2>

            <table id="list-display-table" class="widefat">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Row will be added dynamically -->
                </tbody>
            </table>
            <form id="mna-list-submit">
                <p>
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" required>
                </p>
                <p>
                    <label for="email">Email:</label>
                    <input type="email" name="email" id="email">
                </p>
                <p>
                    <label for="phone">Phone:</label>
                    <input type="text" name="phone" id="phone">
                </p>
                <p>
                    <input type="submit" value="Submit">
                </p>
            </form>
        </div>
    <?php
}

//Display Initial Data 
function mna_list_creator_initial_data(){
    global $wpdb;

    $tablename = $wpdb->prefix . 'list_creator';

    $results = $wpdb->get_results( "SELECT * FROM $tablename", ARRAY_A );
    echo json_encode( $results );

    wp_die();
}
add_action( 'wp_ajax_mna_list_creator_initial_data', 'mna_list_creator_initial_data' );

//Handle Form submission

function list_creator_form_submit() {
    global $wpdb;

    $tablename = $wpdb->prefix . 'list_creator';

    $name = sanitize_text_field( $_POST['name' ] );
    $email = sanitize_text_field( $_POST['email'] );
    $phone = sanitize_text_field( $_POST['phone'] );

    $wpdb->insert(
        $tablename,
        array(
            'name'      => $name,
            'email'     => $email,
            'phone'     => $phone,
        ),
        array( '%s', '%s', '%s' ),
    );

    $row_id = $wpdb->insert_id;

    $row = $wpdb->get_row( "SELECT * FROM $tablename where id = $row_id" );

    echo json_encode( $row );

    wp_die();
}
add_action( 'wp_ajax_list_creator_form_submit', 'list_creator_form_submit' );

//Delete List Row
function delete_list_row() {
    global $wpdb;

    $tablename = $wpdb->prefix . 'list_creator';

    $row_id = intval( $_POST['row_id'] );

    $wpdb->delete( $tablename, array( 'id' => $row_id ) );

    wp_die();
}
add_action( 'wp_ajax_delete_list_row', 'delete_list_row' );