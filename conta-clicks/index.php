<?php
/*
Plugin Name: Conta Click
Description: Um plugin para armazenar clicks realizados no botão customizável do site.
Version: 1.0
Author: Bruno Sousa (teste | Clube do Valor).
*/

// Função para criar a tabela personalizada
function create_custom_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'cclick_config';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        ip TEXT,
        time TIME,
        date DATE,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Registra a função para ser executada quando o plugin for ativado
register_activation_hook(__FILE__, 'create_custom_table');

// Página de dashboard do plugin
function cclick_config_page() {
    include(plugin_dir_path(__FILE__) . 'admin/dashboard.php');
}

function cclick_menu() {
    add_menu_page('Cclick', 'Cclick', 'manage_options', 'cclick-monitoring', 'cclick_config_page', 'dashicons-welcome-widgets-menus', 4);
}

add_action('admin_menu', 'cclick_menu');

// Função para salvar os clicks
function save_click() {
    if (isset($_POST['action']) && $_POST['action'] == 'save_click') {
        global $wpdb;

        $table_name = $wpdb->prefix . 'cclick_config';

        $ip = $_SERVER['REMOTE_ADDR'];
        $time = current_time('H:i:s');
        $date = current_time('Y-m-d');

        $wpdb->insert(
            $table_name,
            array(
                'ip' => $ip,
                'time' => $time,
                'date' => $date,
            )
        );

        wp_send_json_success('Click salvo com sucesso - agora foi!!! aaaaaaaaaeeeeeeeeeeeee');
    }

}

add_action('wp_ajax_save_click', 'save_click');
add_action('wp_ajax_nopriv_save_click', 'save_click');

// Função para adicionar o shortcode do botão de clique
function cclick_button_shortcode() {
    ob_start();
    ?>
    <form id="cclick-form" method="post" action="">
        <input type="hidden" name="action" value="save_click">
        <button type="button" class="btn btn-primary" id="cclick-button">Click</button>
    </form>
    <script>
        jQuery(document).ready(function($) {
            var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
            $('#cclick-button').on('click', function() {
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: $('#cclick-form').serialize(),
                    success: function(response) {
                        console.log(response.data);
                    },
                    error: function(error) {
                        console.log(error);
                    }
                });
            });
        });
    </script>
    <?php
    $output = ob_get_clean();
    return $output;
}

add_shortcode('cclick_button', 'cclick_button_shortcode');

// Adiciona a biblioteca Bootstrap
function enqueue_bootstrap_css() {
    wp_enqueue_style('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap/dist/css/bootstrap.min.css');
}

add_action('wp_enqueue_scripts', 'enqueue_bootstrap_css');

// Adiciona a biblioteca Jquery
function enqueue_jquery() {
    wp_deregister_script('jquery'); // Desregistrar a versão padrão do jQuery
    wp_register_script('jquery', 'https://code.jquery.com/jquery-3.6.0.min.js', array(), '3.6.0', true);
    wp_enqueue_script('jquery');
}

add_action('wp_enqueue_scripts', 'enqueue_jquery');
