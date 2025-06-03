<?php 
// Create nonce for AJAX request
$nonce = wp_create_nonce('ken_transformer_ajax');

// Properly enqueue scripts and styles
function ken_transformer_admin_scripts() {
    wp_enqueue_script('ken-transformer-admin', plugin_dir_url(__FILE__) . 'js/admin.js', array('jquery'), '1.0.0', true);
    wp_localize_script('ken-transformer-admin', 'kenTransformerData', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('ken_transformer_ajax')
    ));
    wp_enqueue_style('ken-transformer-admin', plugin_dir_url(__FILE__) . 'css/admin.css', array(), '1.0.0');
}
add_action('admin_enqueue_scripts', 'ken_transformer_admin_scripts');
?>

<div class="wrap">
<?php
// Securely check activation status
if (get_option('ken_transformer_importer_connect_status')) {
    try {
        $html = base64_decode(get_option('ken_transformer_importer_connect_status'));
        // Validate that the decoded string is valid XML before loading
        if (simplexml_load_string($html)) {
            $xml = simplexml_load_string($html);
            $status = isset($xml->status) ? base64_encode($xml->status) : '';
            $site = isset($xml->site) ? $xml->site : '';
            
            // Sanitize the server hostname for comparison
            $current_host = isset($_SERVER['HTTP_HOST']) ? sanitize_text_field($_SERVER['HTTP_HOST']) : '';
            
            if (($site == $current_host) && ($status == "YWN0aXZhdGVk")) {
                echo "<div id=\"message\" class=\"updated fade\" style='color:blue;'><p>Activated</p></div>\n";
            }
        }
    } catch (Exception $e) {
        // Log error securely, don't expose to user
        error_log('Error in KenPlayer activation check: ' . $e->getMessage());
    }
}
?>

<form method="post" name="formsearch">
    <table class="form-table">
        <tr>
            <th style="width:100px;"><label for="ken_transformer_license_key_ok">Purchased Email</label></th>
            <td><input class="regular-text" type="text" id="ken_transformer_license_key_ok" name="ken_transformer_license_key_ok" value="<?php echo esc_attr(get_option('ken_transformer_license_key_ok')); ?>"></td>
        </tr>
        <tr>
            <th style="width:100px;"><label for="ken_transformer_order_code">Order Number#:</label></th>
            <td><input class="regular-text" type="text" id="ken_transformer_order_code" name="ken_transformer_order_code" value=""></td>
        </tr>
    </table>
    <p class="submit">
        <input type="submit" name="activate_license" id="active" value="Activate" class="button-primary" />
    </p>
    <input type="hidden" name="type" value="update_options" />
    <div id="formstatus"></div>
    <div id="loading">LOADING!</div>
</form>
</div>