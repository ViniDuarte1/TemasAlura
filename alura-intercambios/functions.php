<?php
function alura_intercambios_registrando_taxonomia()
{
    register_taxonomy(
        'paises',
        'destinos',
        array(
            'labels' => array('name' => 'Países'),
            'hierarchical' => true
        )
    );
}
add_action('init', 'alura_intercambios_registrando_taxonomia');

function alura_intercambios_registrando_post_customizado()
{
    register_post_type(
        'destinos',
        array(
            'labels' => array('name' => 'Destinos'),
            'public' => true,
            'menu_position' => 0,
            'supports' => array('title', 'editor', 'thumbnail'),
            'menu_icon' => 'dashicons-admin-site'
        )
    );
}
add_action('init', 'alura_intercambios_registrando_post_customizado');

function adiciona_recursos()
{
    add_theme_support(feature: 'custom-logo');
    add_theme_support(feature: 'post-thumbnails');
}
add_action('after_setup_theme', 'adiciona_recursos');

function registra_menu()
{
    register_nav_menu(
        location: 'menu-navegacao',
        description: 'Menu navegação'
    );
}
add_action('init', 'registra_menu');

function registra_post_banner()
{
    register_post_type(
        'banners',
        array(
            'labels' => array('name' => 'Banner'),
            'public' => true,
            'menu_position' => 1,
            'menu_icon' => 'dashicons-format-image',
            'supports' => array('title', 'thumbnail')
        )
    );
}
add_action('init', 'registra_post_banner');

function registra_metabox()
{
    add_meta_box(
        'reg_metabox',
        'texto para a home',
        'funcao_callback',
        'banners'
    );
}
add_action('add_meta_boxes', 'registra_metabox');

function funcao_callback($post)
{
    $texto_home_1 = get_post_meta($post->ID, '_texto_home_1', true);
    $texto_home_2 = get_post_meta($post->ID, '_texto_home_2', true);
?>
    <label for="texto_home_1">Texto 1</label>
    <input type="text" name="texto_home_1" style="width: 100%" value="<?= $texto_home_1 ?>" />
    <br>
    <br>
    <label for="texto_home_2">Texto 2</label>
    <input type="text" name="texto_home_2" style="width: 100%" value="<?= $texto_home_2 ?>" />
<?php
}

function salva_dados_metabox($post_id){
    foreach ($_POST as $key => $value) {
        if ($key !== 'texto_home_1' && $key !== 'texto_home_2') {
            continue;
        }

        update_post_meta(
                $post_id,
            '_' . $key,
            $_POST[$key]
        );
    }
}
add_action('save_post', 'salva_dados_metabox');

function pegatextobanner()
{

    $args = array(
        'post_type' => 'banners',
        'post_status' => 'publish',
        'posts_per_page' => 1
    );
    $query = new WP_Query($args);

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $texto1 = get_post_meta(get_the_ID(), '_texto_home_1', true);
            $texto2 = get_post_meta(get_the_ID(), '_texto_home_2', true);
            return array(
                'texto_1' => $texto1,
                'texto_2' => $texto2
            );
        }
    }
}

function adiciona_js()
{

    $textosBanner = pegatextobanner();

    if (is_front_page()) {
        wp_enqueue_script('typed-js', get_template_directory_uri() . '/js/typed.min.js', array(), false, true);
        wp_enqueue_script('texto-banner-js', get_template_directory_uri() . '/js/texto-banner.js', array('typed-js'), false, true);
        wp_localize_script('texto-banner-js', 'data', $textosBanner);
    }
}
add_action('wp_enqueue_scripts', 'adiciona_js');