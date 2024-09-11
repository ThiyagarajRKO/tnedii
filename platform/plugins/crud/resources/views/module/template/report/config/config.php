<?php

    $template   = base_path().'/platform/plugins/crud/resources/views/module/template/report/';
    $controller = file_get_contents( $template.'controller.tpl' );
    $grid       = file_get_contents(  $template.'grid.tpl' );
    $model       = file_get_contents(  $template.'model.tpl' );  

    $build_controller       = blend($controller,$codes);       
    $build_grid             = blend($grid,$codes);    
    $build_model            = blend($model,$codes);    

    file_put_contents(  $dirC ."{$ctr}Controller.php" , $build_controller) ;    
    file_put_contents(  $dirM ."{$ctr}.php" , $build_model) ;     
    file_put_contents(  $dir."/index.blade.php" , $build_grid) ;  

?>                