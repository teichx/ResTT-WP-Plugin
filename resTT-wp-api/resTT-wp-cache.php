<?php
  function Cache($functionName, $functionCallback, $timeCaching = null){

    if(isset($functionName)){
      $cacheDuration = isset($timeCaching) ? $timeCaching : 60;

      $cachePath = plugin_dir_path( __FILE__ )."cache/".$functionName.".json";
      $arquivoValido = file_exists($cachePath) && (filemtime($cachePath) > (time() - $cacheDuration));
      if($arquivoValido){
        $content = file_get_contents($cachePath);
        $json = json_decode($content, 1);
        return $json;
      } 
      else {
        $data = $functionCallback();
        $json = json_encode($data);
        file_put_contents($cachePath, $json);
        return $data;
      }
    }
  }
?>