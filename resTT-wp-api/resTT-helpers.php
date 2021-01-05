<?php
  function getParamOrDefault($params, $nameParam, $default){
    $actualParam = $params->get_param($nameParam);
    return isset($actualParam) ? $actualParam : $default;
  }

  function getCleanCategories($postId){
    $categories = get_the_category($postId);

    $categoriesArray = [];
    foreach($categories as $category){
      array_push($categoriesArray, array(
        'slug' => $category->slug,
        'name' => $category->name,
        'id' => $category->cat_ID,
      ));
    }

    return $categoriesArray;
  }

  function filterPostParams($post, $getContent = false){
    if(!isset($post))
      return null;
      
    $thumbnail = get_the_post_thumbnail_url($post->ID, 'large');
    $image = is_null($thumbnail) ? get_option('img_NE') : $thumbnail;

    $responsePost = array(
      'id' => $post->ID,
      'title' => $post->post_title,
      'slug' => $post->post_name,
      'image' => $image,
      'data' => get_post_time(get_option('date_format'), true, $post, true),
      'categories' => getCleanCategories($post->ID),
    );
    
    if($getContent == true){
      $responsePost['content'] = clearPostContent($post->post_content);
    }

    return $responsePost;
  }

  function filterPostContent($post){
    return array(
      'content' => clearPostContent($post->post_content),
    );
  }

  function clearPostContent($postContent){
    // $noHtmlContent = wp_filter_nohtml_kses($postContent);
    $contentNotExtraSpaces = preg_replace("/\s|&nbsp;/ui", ' ', $postContent);
    $contentNotHtmlComments = preg_replace('/<!--(.*)-->/Uis', '', $contentNotExtraSpaces);

    return $contentNotHtmlComments;
  }

  function GenerateLinkMenu($url, $object_type){
    $link = $object_type === 'custom'
      ? $url
      : removeHost($url);

    return $link;
  }

  function removeHost($url){
    [$http, $urlWithoutServer] = explode($_SERVER['HTTP_HOST'], $url);
    return $urlWithoutServer;
  }
?>