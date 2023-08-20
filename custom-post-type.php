<?php 
// Função para registrar o custom post type e a taxonomia
function register_imoveis_post_type() {
    $labels = array(
        'name' => 'Imóveis',
        'singular_name' => 'Imóvel',
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'supports' => array('title', 'editor', 'thumbnail', 'custom-fields','elementor'),
    );

    register_post_type('imovel', $args);

    // Registrar a taxonomia "categoria" para o post type "imovel"
    register_taxonomy('imovel_category', 'imovel', array(
        'label' => 'Categorias',
        'hierarchical' => true,
        'public' => true,
        'rewrite' => array('slug' => 'imovel-category'),
        'show_in_nav_menus' => true, // Esta opção permite que a taxonomia seja exibida nos menus
    ));
}
add_action('init', 'register_imoveis_post_type');

// Adicionar a coluna de categorias na lista de posts
function add_category_column($columns) {
    $new_columns = array();

    // Insere a coluna de categorias antes da coluna de data
    $new_columns['cb'] = $columns['cb'];
    $new_columns['title'] = $columns['title'];
    $new_columns['imovel_category'] = 'Categorias';
    $new_columns['date'] = $columns['date'];

    return $new_columns;
}
add_filter('manage_imovel_posts_columns', 'add_category_column');

// Personalizar a exibição dos valores da coluna de categorias
function display_category_column($column, $post_id) {
    if ($column === 'imovel_category') {
        $categories = get_the_terms($post_id, 'imovel_category');
        if (!empty($categories)) {
            $category_names = array();
            foreach ($categories as $category) {
                $category_names[] = $category->name;
            }
            echo implode(', ', $category_names);
        } else {
            echo '-';
        }
    }
}
add_action('manage_imovel_posts_custom_column', 'display_category_column', 10, 2);

// Adicionar o filtro de categorias acima da lista de posts
function add_category_filter() {
    global $typenow;

    if ($typenow === 'imovel') {
        $taxonomy = 'imovel_category';
        $selected = isset($_GET[$taxonomy]) ? $_GET[$taxonomy] : '';

        wp_dropdown_categories(array(
            'show_option_all' => 'Mostrar Todos',
            'taxonomy' => $taxonomy,
            'name' => $taxonomy,
            'orderby' => 'name',
            'selected' => $selected,
            'hierarchical' => true,
            'depth' => 3,
            'show_count' => false,
            'hide_empty' => true,
        ));
    }
}
add_action('restrict_manage_posts', 'add_category_filter');


add_action('save_post', 'set_default_thumbnail_for_imovel');




?>
