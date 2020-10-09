<?php
/*
Plugin Name: Data Listing
Description: Plugin should store the data to database Using admin panel and also list records in admin panel as well as on frontend. Use Shortcode: [display_listing]
Version:     1.0
Author:      Shailaja Bhagat
Author URI:  https://github.com/shailaja-bhagat/
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

function data_listing_install(){
    global $wpdb;

    $table_name = $wpdb->prefix . 'data_listing'; 

    $sql = "CREATE TABLE " . $table_name . " (
      id int(11) NOT NULL AUTO_INCREMENT,
      name tinytext NOT NULL,
      email VARCHAR(100) NOT NULL,
      age int(11) NULL,
      PRIMARY KEY  (id)
    );";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

}

register_activation_hook(__FILE__, 'data_listing_install');

function data_listing_install_data(){
    
    global $wpdb;

    $table_name = $wpdb->prefix . 'data_listing';

    $wpdb->insert($table_name, array(
        'name' => 'john',
        'email' => 'john@gmail.com',
        'age' => 26
    ));
}

register_activation_hook(__FILE__, 'data_listing_install_data');

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class Custom_Table_Example_List_Table extends WP_List_Table{
    function __construct(){
        global $status, $page;

        parent::__construct(array(
            'singular' => 'employee',
            'plural' => 'employees',
        ));
    }

    function column_default($item, $column_name){
        return $item[$column_name];
    }

    function column_age($item){
        return '<em>' . $item['age'] . '</em>';
    }

    function column_name($item){
        $actions = array(
            'edit' => sprintf('<a href="?page=employees_form&id=%s">%s</a>', $item['id'], __('Edit item', 'data_listing')),
            'delete' => sprintf('<a href="?page=%s&action=delete&id=%s">%s</a>', $_REQUEST['page'], $item['id'], __('Delete', 'data_listing')),
        );

        return sprintf('%s %s',
            $item['name'],
            $this->row_actions($actions)
        );
    }

    function get_columns(){
        $columns = array(
            'name' => __('Name', 'data_listing'),
            'email' => __('E-Mail', 'data_listing'),
            'age' => __('Age', 'data_listing'),
        );
        return $columns;
    }

    function process_action(){
        global $wpdb;
        $table_name = $wpdb->prefix . 'data_listing'; // do not forget about tables prefix

        if ('delete' === $this->current_action()) {
            $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
            if (is_array($ids)) $ids = implode(',', $ids);

            if (!empty($ids)) {
                $wpdb->query("DELETE FROM $table_name WHERE id IN($ids)");
            }
        }
    }

    function prepare_items(){
        global $wpdb;
        $table_name = $wpdb->prefix . 'data_listing';

        $per_page = 5;

        $columns = $this->get_columns();
        $hidden = array();

        $this->_column_headers = array($columns, $hidden);

        $this->process_action();

        $total_items = $wpdb->get_var("SELECT COUNT(id) FROM $table_name");

        $paged = isset($_REQUEST['paged']) ? max(0, intval($_REQUEST['paged'] - 1) * $per_page) : 0;
        
        $this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name LIMIT %d OFFSET %d", $per_page, $paged), ARRAY_A);

        $this->set_pagination_args(array(
            'total_items' => $total_items,
            'per_page' => $per_page,
            'total_pages' => ceil($total_items / $per_page)
        ));
    }
}

function data_listing_admin_menu(){
    add_menu_page(__('employees', 'data_listing'), __('Employees', 'data_listing'), 'activate_plugins', 'employees', 'data_listing_employees_page_handler');
    add_submenu_page('employees', __('employees', 'data_listing'), __('Employees Listing', 'data_listing'), 'activate_plugins', 'employees', 'data_listing_employees_page_handler');
    // add new will be described in next part
    add_submenu_page('employees', __('Add new', 'data_listing'), __('Add new Employee', 'data_listing'), 'activate_plugins', 'employees_form', 'data_listing_employees_form_page_handler');
}

add_action('admin_menu', 'data_listing_admin_menu');

function data_listing_employees_page_handler(){
    global $wpdb;

    $table = new Custom_Table_Example_List_Table();
    $table->prepare_items();

    $message = '';
    if ('delete' === $table->current_action()) {
        $message = '<div class="updated below-h2" id="message"><p>' . sprintf(__('Items deleted', 'data_listing')) . '</p></div>';
    }
    ?>
    <div class="wrap">

        <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
        <h2><?php _e('Employees Listing', 'data_listing')?> <a class="add-new-h2"
                                    href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=employees_form');?>"><?php _e('Add new', 'data_listing')?></a>
        </h2>
        <?php echo $message; ?>

        <form id="employees-table" method="GET">
            <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
            <?php $table->display() ?>
        </form>

    </div>
    <?php
}

function data_listing_employees_form_page_handler(){
    global $wpdb;
    $table_name = $wpdb->prefix . 'data_listing'; 

    $message = '';
    $notice = '';

    $default = array(
        'id' => 0,
        'name' => '',
        'email' => '',
        'age' => null,
    );

    if ( isset($_REQUEST['nonce']) ) {
        $item = shortcode_atts($default, $_REQUEST);
        $item_valid = data_listing_validate_employee($item);
        if ($item_valid === true) {
            if ($item['id'] == 0) {
                $result = $wpdb->insert($table_name, $item);
                $item['id'] = $wpdb->insert_id;
                if ($result) {
                    $message = __('Item was successfully saved', 'data_listing');
                } else {
                    $notice = __('There was an error while saving item', 'data_listing');
                }
            } else {
                $result = $wpdb->update($table_name, $item, array('id' => $item['id']));
                if ($result) {
                    $message = __('Item was successfully updated', 'data_listing');
                } else {
                    $notice = __('There was an error while updating item', 'data_listing');
                }
            }
        } else {
            $notice = $item_valid;
        }
    }
    else {
        $item = $default;
        if (isset($_REQUEST['id'])) {
            $item = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $_REQUEST['id']), ARRAY_A);
            if (!$item) {
                $item = $default;
                $notice = __('Item not found', 'data_listing');
            }
        }
    }

    add_meta_box('employees_form_meta_box', 'Employee data', 'data_listing_employees_form_meta_box_handler', 'employee', 'normal', 'default');

    ?>
    <div class="wrap">
        <h2><?php _e('Employee Details', 'data_listing')?> <a class="add-new-h2"
                                    href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=employees');?>"><?php _e('back to list', 'data_listing')?></a>
        </h2>

        <?php if (!empty($notice)): ?>
        <div id="notice" class="error"><p><?php echo $notice ?></p></div>
        <?php endif;?>
        <?php if (!empty($message)): ?>
        <div id="message" class="updated"><p><?php echo $message ?></p></div>
        <?php endif;?>

        <form id="form" method="POST">
            <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(basename(__FILE__))?>"/>
            <input type="hidden" name="id" value="<?php echo $item['id'] ?>"/>

            <div class="metabox-holder" id="poststuff">
                <div id="post-body">
                    <div id="post-body-content">
                        <?php do_meta_boxes('employee', 'normal', $item); ?>
                        <input type="submit" value="<?php _e('Save', 'data_listing')?>" id="submit" class="button-primary" name="submit">
                    </div>
                </div>
            </div>
        </form>
    </div>
<?php
}

function data_listing_employees_form_meta_box_handler($item){
    ?>

    <table cellspacing="2" cellpadding="5" style="width: 100%;" class="form-table">
        <tbody>
        <tr class="form-field">
            <th valign="top" scope="row">
                <label for="name"><?php _e('Name', 'data_listing')?></label>
            </th>
            <td>
                <input id="name" name="name" type="text" style="width: 95%" value="<?php echo esc_attr($item['name'])?>"
                    size="50" class="code" placeholder="<?php _e('Your name', 'data_listing')?>" required>
            </td>
        </tr>
        <tr class="form-field">
            <th valign="top" scope="row">
                <label for="email"><?php _e('E-Mail', 'data_listing')?></label>
            </th>
            <td>
                <input id="email" name="email" type="email" style="width: 95%" value="<?php echo esc_attr($item['email'])?>"
                    size="50" class="code" placeholder="<?php _e('Your E-Mail', 'data_listing')?>" required>
            </td>
        </tr>
        <tr class="form-field">
            <th valign="top" scope="row">
                <label for="age"><?php _e('Age', 'data_listing')?></label>
            </th>
            <td>
                <input id="age" name="age" type="number" style="width: 95%" value="<?php echo esc_attr($item['age'])?>"
                    size="2" maxlength="2" class="code" placeholder="<?php _e('Your age', 'data_listing')?>" required>
            </td>
        </tr>
        </tbody>
    </table>
    <?php
}

function data_listing_validate_employee($item){
    $messages = array();

    if (empty($item['name'])) $messages[] = __('Name is required', 'data_listing');
    if (empty($item['email'])) $messages[] = __('E-Mail is required', 'data_listing');
    if (empty($item['age'])) $messages[] = __('Age is required', 'data_listing');

    if (empty($messages)) return true;
    return implode('<br />', $messages);
}

//Shortcode creation [display_listing]

function data_listing_shortcode($attr) {

	if( file_exists(plugin_dir_path( __FILE__ ).'/display-listing.php' )) {
			ob_start();
            include(plugin_dir_path( __FILE__ ).'display-listing.php');
			return ob_get_clean();
	}
}

add_shortcode( 'display_listing', 'data_listing_shortcode' );

add_action( 'wp_enqueue_scripts', 'data_listing_enqueue' );
function data_listing_enqueue() {
	//css
    wp_enqueue_style( 'dl_bs_style', plugins_url( '/css/datalist-customstyle.css', __FILE__ ));
    //js
    wp_enqueue_script( 'dl_custom_script', plugins_url( '/js/datalist-customscript.js', __FILE__ ), array('jquery'), '1.0', true );
}