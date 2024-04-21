<?php
// Add a menu item to the admin menu
function custom_settings_menu() {
    add_menu_page(
        'Leonardo.ai',
        'Leonardo.ai',
        'manage_options',
        'custom-settings',
        'custom_settings_page',
        'dashicons-admin-generic', // You can change the icon if needed
        30
    );
}
add_action('admin_menu', 'custom_settings_menu');

// Create the settings page
function custom_settings_page() {
    ?>
    <div class="wrap">
        <h1>Leonardo.ai</h1>
        <form method="post" action="options.php">
            <?php settings_fields('custom-settings-group'); ?>
            <?php do_settings_sections('custom-settings'); ?>
            <?php submit_button(); ?>
        </form>

        <a href="#" class="api-test" style="font-size: 15px; font-weight: bold; text-decoration: none;">Quick test</a>
        <div class="api-test-result" style="margin-top: 10px;"></div>

    </div>
    <?php
}

// Register and initialize settings
function custom_settings_init() {
    register_setting(
        'custom-settings-group',
        'leonardo_model'
    );

    register_setting(
        'custom-settings-group',
        'leonardo_api_key'
    );

    register_setting(
        'custom-settings-group',
        'leonardo_post_types'
    );

    register_setting(
        'custom-settings-group',
        'leonardo_image_height'
    );

    register_setting(
        'custom-settings-group',
        'leonardo_image_width'
    );

    register_setting(
        'custom-settings-group',
        'leonardo_alchemy'
    );

    add_settings_section(
        'custom-settings-section',
        'Leonardo.ai',
        'custom_settings_section_callback',
        'custom-settings'
    );

    add_settings_field(
        'leonardo_model',
        'Model',
        'leonardo_model_callback',
        'custom-settings',
        'custom-settings-section'
    );

    add_settings_field(
        'leonardo_api_key',
        'API Key',
        'leonardo_api_key_callback',
        'custom-settings',
        'custom-settings-section'
    );

    /*
    add_settings_field(
        'leonardo_post_types',
        'Type of Posts',
        'leonardo_post_types_callback',
        'custom-settings',
        'custom-settings-section'
    );
    */
    add_settings_field(
        'leonardo_image_width',
        'Width',
        'leonardo_image_width_callback',
        'custom-settings',
        'custom-settings-section'
    );
    add_settings_field(
        'leonardo_image_height',
        'Height',
        'leonardo_image_height_callback',
        'custom-settings',
        'custom-settings-section'
    );
    add_settings_field(
        'leonardo_alchemy',
        'Alchemy',
        'leonardo_alchemy_callback',
        'custom-settings',
        'custom-settings-section'
    );
}
add_action('admin_init', 'custom_settings_init');

// Callback functions for the fields
function custom_settings_section_callback() {
    echo '';
}

function leonardo_model_callback() {

    $models = (new Leonardo_API)->get_models();
    
    $model_options = [];

    foreach( $models as $model ) {
        $model_options[ $model['id'] ] = $model['name'];
    }

    $selected_model = esc_attr(get_option('leonardo_model'));

    echo '<select name="leonardo_model">';
    echo '<option>Choose a model</option>';
    foreach ($model_options as $value => $label) {
        echo '<option value="' . esc_attr($value) . '" ' . selected($selected_model, $value, false) . '>' . esc_html($label) . '</option>';
    }
    echo '</select>';
}

function leonardo_api_key_callback() {
    $api_key = esc_attr(get_option('leonardo_api_key'));
    echo '<input type="text" name="leonardo_api_key" value="' . $api_key . '" style="width: 500px;" />';
}

function leonardo_post_types_callback() {
    $post_type_options = array(
        'post' => 'Posts',
        'page' => 'Pages',
    );

    $selected_post_types = (array) get_option('leonardo_post_types');

    echo '<select name="leonardo_post_types[]" multiple="multiple">';
    foreach ($post_type_options as $value => $label) {
        echo '<option value="' . esc_attr($value) . '" ' . selected(in_array($value, $selected_post_types), true, false) . '>' . esc_html($label) . '</option>';
    }
    echo '</select>';
}

function leonardo_image_height_callback() {
    $height = esc_attr(get_option('leonardo_image_height'));
    echo '<input type="number" name="leonardo_image_height" value="' . $height . '" />';
    echo "<br/><span class='note'>Must be a multiple of 8</span>";
}

function leonardo_image_width_callback() {
    $width = esc_attr(get_option('leonardo_image_width'));
    echo '<input type="number" name="leonardo_image_width" value="' . $width . '" />';
    echo "<br/><span class='note'>Must be a multiple of 8</span>";
}

function leonardo_alchemy_callback() {
    $alchemy_checked = get_option('leonardo_alchemy') ? 'checked="checked"' : '';
    echo '<input type="checkbox" name="leonardo_alchemy" ' . $alchemy_checked . ' value="1" />';
}