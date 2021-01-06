<?php
/**
 * Plugin Name: ResTT
 * Plugin URI: https://github.com/teichx/ResTT-WP-Plugin
 * Description: API Rest para otimizar a busca de dados para Front Ends
 * Version: 1.0
 * Author: Temístocles Schwartz
 * Author URI: https://www.linkedin.com/in/temistocles-schwartz/
 */

  define( 'resTT__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

  require_once( resTT__PLUGIN_DIR . 'resTT-helpers.php' );
  require_once( resTT__PLUGIN_DIR . 'resTT-wp-cache.php' );


  function resTT_menu_principal(){
    $menus = wp_get_nav_menu_items('menu-principal');
    $menuJson = [];
    foreach($menus as $menu){
      array_push($menuJson, array(
        'title' => $menu->title, 
        'slug' => GenerateLinkMenu($menu->url, $menu->object)
      ));
    }
    return $menuJson;
  }

  function resTT_post_content_per_slug($params){
    $post = get_posts([
      'name' => $params['slug'],
      'post_type' => 'post'
    ]);

    return filterPostContent($post[0]);
  }

  function resTT_post_content_per_id($params){
    $post = get_post(intval($params['id']));

    return isset($post) ? filterPostContent($post) : null;
  }

  function resTT_post_per_id($params){
    $post = get_post(intval($params['id']));

    return filterPostParams($post, true);
  }

  function resTT_post_per_slug($params){
    $post = get_posts([
      'name' => $params['slug'],
      'post_type' => 'post'
    ]);

    return filterPostParams($post[0], true);
  }

  function resTT_posts_ids(){
    $args = [
      'numberposts' => -1,
      'post_type' => 'post',
      'post_status'=>'publish',
      'fields' => 'ids',
    ];

    $postsIds = get_posts($args);

    return $postsIds;
  }

  function resTT_posts_default($params){
    $countPosts = intval(getParamOrDefault($params, 'count', 12));  //13
    $showContent = getParamOrDefault($params, 'content', false);    //
    $page = getParamOrDefault($params, 'page', 1);                  //2
    $offsetParam = getParamOrDefault($params, 'offset', 0);         //0

    $offset = ($countPosts * ($page - 1)) + $offsetParam;

    $posts = get_posts([
      'numberposts' => $countPosts,
      'post_type' => 'post',
      'post_status'=>'publish',
      'offset' => $offset,
    ]);
  
    $data = [];
    foreach($posts as $post) {
      array_push($data, filterPostParams($post, $showContent));
    }
  
    return $data;
  }

  add_action('rest_api_init', function(){
    register_rest_route('resTT/v1', 'posts', [
      'methods' => 'GET',
      'callback' => 'resTT_posts_default'
    ]);

    register_rest_route('resTT/v1', 'ids', [
      'methods' => 'GET',
      'callback' => 'resTT_posts_ids'
    ]);

    register_rest_route('resTT/v1', 'content/(?P<id>[0-9]+)', [
      'methods' => 'GET',
      'callback' => 'resTT_post_content_per_id'
    ]);

    register_rest_route('resTT/v1', 'content_per_slug/(?P<slug>[a-zA-Z0-9-]+)', [
      'methods' => 'GET',
      'callback' => 'resTT_post_content_per_slug'
    ]);
    
    register_rest_route('resTT/v1', 'post/(?P<id>[0-9]+)', [
      'methods' => 'GET',
      'callback' => 'resTT_post_per_id'
    ]);

    register_rest_route('resTT/v1', 'post_per_slug/(?P<slug>[a-zA-Z0-9-]+)', [
      'methods' => 'GET',
      'callback' => 'resTT_post_per_slug'
    ]);
    
    register_rest_route('resTT/v1', 'menu', [
      'methods' => 'GET',
      'callback' => 'resTT_menu_principal'
    ]);
    
  });

?>