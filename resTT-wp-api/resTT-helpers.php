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
      
    $responsePost = array(
      'id' => $post->ID,
      'title' => $post->post_title,
      'slug' => $post->post_name,
      'image' => get_the_post_thumbnail_url($post->ID, 'large'),
      'data' => date('d/m/Y', strtotime($post->post_date)),
      'categories' => getCleanCategories($post->ID),
    );
    
    if($getContent == true){
      $responsePost['content'] = $post->post_content;
    }

    return $responsePost;
  }

  function filterPostContent($post){
    return array(
      'content' => $post->post_content,
    );
  }

  function GenerateSlugPerTitle($title){
    $titleLowerCase = strtolower($title);
    $stringLimpa = sanitizeString($titleLowerCase);
    $slug = replaceUnderlinePerUnderscore($stringLimpa);
    return $slug;
  }

  function sanitizeString($str) {
    $str = preg_replace('/[áàãâä]/ui', 'a', $str);
    $str = preg_replace('/[éèêë]/ui', 'e', $str);
    $str = preg_replace('/[íìîï]/ui', 'i', $str);
    $str = preg_replace('/[óòõôö]/ui', 'o', $str);
    $str = preg_replace('/[úùûü]/ui', 'u', $str);
    $str = preg_replace('/[ç]/ui', 'c', $str);
    $str = preg_replace('/[^a-z0-9]/i', '_', $str);
    $str = preg_replace('/_+/', '_', $str);
    return $str;
  }

  function replaceUnderlinePerUnderscore($str){
    return preg_replace('/_/', '-', $str);
  }

?>