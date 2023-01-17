<?php
if ( ! defined( 'WPINC' ) ) {
	die;
}
/**
 * The helper functionality of the Theme.
 *
 */

/**
 * Class HoodslyHubCustomOrder
 *
 */
class wilkesHoodSpeciesStockList {

    /**
     * This class Constructor
     */
    public function __construct() {
        add_action('admin_menu', [$this, 'Hood_Species_Stock_Admin_Menu']);
        add_action('wp_ajax_wilkes_save_options', [$this, 'saveOptions']);
        add_action('wp_ajax_wilkes_delete_option', [$this, 'deleteOptions']);
    }

    // Registering admin menus to control product options
    public function Hood_Species_Stock_Admin_Menu() {
        add_menu_page(
            __('Wood Species Stock', 'hoodslyhub'),
            __('Wood Species Stock', 'hoodslyhub'),
            'manage_options',
            'wood-species-stock',
            [$this, 'adminPage'],
            'dashicons-tickets',
            5
        );
    }

    public function adminPage() {
        ?>
            <div class="table_container">
                <table class="wilkes_table" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th><h2>In stock products</h2></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="hidden_row">
                            <td>
                                <input type="text" class="wilkes_item_id" value="" />
                            </td>
                            <td>
                                <button class="delete_option">Remove</button>
                            </td>
                        </tr>

                        <?php $this->Hood_Species_load_Options_HTML(); ?>
                    </tbody>
                </table>
                <div class="action_btn">
                    
                    <button class="wilkes_delete_all">Delete All</button>
                    <div class="action_btn_add">
                        <button class="wilkes_create_row">Add New +</button>
                        <button class="wilkes_save_data">Save</button>
                    </div>
                </div>
                
            </div>
        <?php
    }

    /**
     * @return null
     */
    function Hood_Species_load_Options_HTML() {
        $wilkes_Hood_Species_Options = get_option('wilkes_Hood_Species_Options');

        if (!$wilkes_Hood_Species_Options || !is_array($wilkes_Hood_Species_Options)) {
            return;
        }

        foreach ($wilkes_Hood_Species_Options as $key => $option) {
            echo '
            <tr data-id="' . ($key + 1) . '">
                <td>
                    <input type="text" class="wilkes_item_id" value="' . esc_attr($option['wilkes_item_id']) . '" />
                </td>
                <td>
                    <button class="delete_option" data-id="' . ($key + 1) . '">Remove</button>
                </td>
            </tr>
            ';
        }

    }

     /**
     * Sanitize an array of data
     * @param  array   $NonSanitzedData
     * @return mixed
     */
    public function sanitizeData(array $NonSanitzedData) {
        $sanitizedData = null;

        $sanitizedData = array_map(function ($data) {
            if (gettype($data) == 'array') {
                return $this->sanitizeData($data);
            } else {
                return sanitize_text_field($data);
            }
        }, $NonSanitzedData);

        return $sanitizedData;
    }

    public function saveOptions() {
        if (sanitize_text_field($_POST['action']) != 'wilkes_save_options') {
            $this->output['response_type'] = esc_html('invalid_action');
            $this->output['output'] = '<b>' . esc_html__('Action is invalid', 'wilkes') . '</b>';
            echo json_encode($this->output);
            wp_die();
        }

        $this->saveOptionsToDb();

        echo json_encode($this->output);
        wp_die();
    }

    public function saveOptionsToDb() {
        $sanitizedData = $this->sanitizeData($_POST);

        update_option('wilkes_Hood_Species_Options', $sanitizedData['organizedData']);
        $this->output['response_type'] = esc_html('success');
        $this->output['output'] = '<b>' . esc_html__('Data saved successfully', 'wilkes') . '</b>';
        echo json_encode($this->output);
        wp_die();
    }

    public function deleteOptions() {
        if (sanitize_text_field($_POST['action']) != 'wilkes_delete_option') {
            $this->output['response_type'] = esc_html('invalid_action');
            $this->output['output'] = '<b>' . esc_html__('Action is invalid', 'ptw') . '</b>';
            echo json_encode($this->output);
            wp_die();
        }

        $this->deleteOptionsFromDb();

        echo json_encode($this->output);
        wp_die();
    }

    public function deleteOptionsFromDb() {
        $sanitizedData = $this->sanitizeData($_POST);

        $wilkes_Hood_Species_Options = get_option('wilkes_Hood_Species_Options');

        if ($wilkes_Hood_Species_Options) {
            $deleteAction = $sanitizedData['deleteAction'];

            if ($deleteAction == 'single_delete') {
                $optionID = intval($sanitizedData['id']) - 1;

                if (isset($wilkes_Hood_Species_Options[$optionID])) {
                    unset($wilkes_Hood_Species_Options[$optionID]);
                    update_option('wilkes_Hood_Species_Options', $wilkes_Hood_Species_Options);
                    $this->output['response_type'] = esc_html('success');
                    $this->output['output'] = esc_html__('Data deleted successfully', 'ptw');
                    echo json_encode($this->output);
                    wp_die();
                } else {
                    $this->output['response_type'] = esc_html('invalid_action');
                    $this->output['output'] = esc_html__('Data is not found', 'ptw');
                    echo json_encode($this->output);
                    wp_die();
                }
            }

            if ($deleteAction == 'delete_all') {
                if (delete_option('wilkes_Hood_Species_Options')) {
                    $this->output['response_type'] = esc_html('success');
                    $this->output['type'] = esc_html('delete_all');
                    $this->output['output'] = esc_html__('All data deleted successfully', 'ptw');
                    echo json_encode($this->output);
                    wp_die();
                }
            }
        }

    }


}

new wilkesHoodSpeciesStockList();
