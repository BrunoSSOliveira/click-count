<?php
// Configurações da tabela
global $wpdb;
$table_name = $wpdb->prefix . 'cclick_config';

// Defina o número de itens por página
$items_per_page = 5;

// Obtém o número total de registros
$total_items = $wpdb->get_var("SELECT COUNT(id) FROM $table_name");

// Obtém o número total de registros
$total_items = $wpdb->get_var("SELECT COUNT(id) FROM $table_name");

// Calcula o número total de páginas
$total_pages = ceil($total_items / $items_per_page);

// Obtém a página atual
$current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;

// Calcula o deslocamento para a consulta
$offset = ($current_page - 1) * $items_per_page;

// Consulta para obter os registros paginados
$clicks = $wpdb->get_results(
    $wpdb->prepare("SELECT * FROM $table_name ORDER BY id DESC LIMIT %d OFFSET %d", $items_per_page, $offset)
);

?>
<div class="wrap">
    <h1 class="wp-heading-inline">Plugin: Conta Clicks</h1>
    <hr class="wp-header-end">
</div>

<div class="col-md-8">
    <div class="card">
        <div class="card-body">
            <h2 class="card-title">Como Utilizar:</h2>
            <p class="card-text">Para Instalar o botão, você deverá inserir o shortcode <b>`[cclick_button]`</b> a qualquer lugar do seu site para exibir o botão.</p>
            <p class="card-text">Abaixo está disponível uma tabela para visualização dos clicks realizados.</p>
        </div>
    </div>
</div>

<div class="col-md-8">
    <div class="card">
        <div class="card-body">
            <h2 class="card-title">Clicks Realizados<?php if (!empty($clicks)) echo ' - ' . $total_items . ' clicks'; ?></h2>

            <?php
            if (!empty($clicks)) :
                ?>
                <!-- Tabela WordPress -->
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th scope="col" class="manage-column">ID</th>
                            <th scope="col" class="manage-column">IP</th>
                            <th scope="col" class="manage-column">Data</th>
                            <th scope="col" class="manage-column">Hora</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($clicks as $click) : ?>
                            <tr>
                                <td><?php echo $click->id; ?></td>
                                <td><?php echo $click->ip; ?></td>
                                <td><?php echo date('d/m/Y', strtotime($click->date)); ?></td>
                                <td><?php echo $click->time; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Paginação -->
                <div class="tablenav">
                    <div class="tablenav-pages">
                        <?php
                        echo paginate_links(
                            array(
                                'base'      => add_query_arg('paged', '%#%'),
                                'format'    => '',
                                'prev_text' => __('&laquo; Anterior'),
                                'next_text' => __('Próximo &raquo;'),
                                'total'     => $total_pages,
                                'current'   => $current_page,
                            )
                        );
                        ?>
                    </div>
                </div>
            <?php else : ?>
                <p>Nenhum click registrado ainda.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
