<?php
        $template = base_path().'/platform/plugins/crud/resources/views/module/template/native/';
        $controller = file_get_contents(  $template.'controller.tpl' );
        $controller_api = file_get_contents(  $template.'controller_api.tpl' );
        $grid = file_get_contents(  $template.'grid.tpl' );               
        $view = file_get_contents(  $template.'view.tpl' );
        $form = file_get_contents(  $template.'form.tpl' );
        $model = file_get_contents(  $template.'model.tpl' );
        $front = file_get_contents(  $template.'frontend.tpl' );
        $frontview = file_get_contents(  $template.'frontendview.tpl' ); 
        $frontform = file_get_contents(  $template.'frontform.tpl' );
        if(isset($config['subgrid']) && count($config['subgrid'])>=1)
        {
             $view = file_get_contents(  $template.'view_detail.tpl' );
        } else {
             $view = file_get_contents(  $template.'view.tpl' );
        }


        $build_controller       = blend($controller,$codes);   
        $build_controller_api   = blend($controller_api,$codes);    
        $build_view             = blend($view,$codes);    
        $build_form             = blend($form,$codes);    
        $build_grid             = blend($grid,$codes);    
        $build_model            = blend($model,$codes);    
        $build_front            = blend($front,$codes);   
        $build_frontview        = blend($frontview,$codes);   
        $build_frontform        = blend($frontform,$codes);                

        if(!is_null($request->input('rebuild')))
        {
            // rebuild spesific files
            if($request->input('c') =='y'){
                file_put_contents( $dirC."{$ctr}Controller.php" , $build_controller) ;    
                file_put_contents(  $dirApi ."{$ctr}Controller.php" , $build_controller_api) ;
            }
            if($request->input('m') =='y'){
                file_put_contents(  $dirM."{$ctr}.php" , $build_model) ;
            }    
            
            if($request->input('g') =='y'){
                file_put_contents(  $dir."/index.blade.php" , $build_grid) ;
            }    
            if($row->module_db_key !='')
            {            
                if($request->input('f') =='y'){
                    file_put_contents(  $dir."/form.blade.php" , $build_form) ;
                }    


                if($request->input('v') =='y'){
                    file_put_contents(  $dir."/view.blade.php" , $build_view) ;
                                                 
                }

                // Frontend Grid
                if($request->input('fg') =='y'){
                    file_put_contents(  $dir."/public/index.blade.php" , $build_front) ;
                } 
                // Frontend View
                if($request->input('fv') =='y'){
                    file_put_contents(  $dir."/public/view.blade.php" , $build_frontview) ;
                } 
                // Frontend Form
                if($request->input('ff') =='y'){
                    file_put_contents(  $dir."/public/form.blade.php" , $build_frontform) ;
                } 

            }  



        
        } else {
        
            file_put_contents(  $dirC ."{$ctr}Controller.php" , $build_controller) ;    
            file_put_contents(  $dirApi ."{$ctr}Controller.php" , $build_controller_api) ;    
            file_put_contents(  $dirM ."{$ctr}.php" , $build_model) ;
            file_put_contents(  $dir."/index.blade.php" , $build_grid) ; 
            file_put_contents( $dir."/form.blade.php" , $build_form) ;
            file_put_contents(  $dir."/view.blade.php" , $build_view) ;       
            file_put_contents(  $dir."/public/index.blade.php" , $build_front) ;  
            file_put_contents(  $dir."/public/view.blade.php" , $build_frontview) ; 
            file_put_contents(  $dir."/public/form.blade.php" , $build_frontform) ;                                     
        
        }
    
$lctr = strtolower($ctr);
$permissions = "[
        'name' => '$ctr',
        'flag' => '$lctr.index',
    ],
    [
        'name'        => 'Create',
        'flag'        => '$lctr.create',
        'parent_flag' => '$lctr.index',
    ],
    [
        'name'        => 'Edit',
        'flag'        => '$lctr.edit',
        'parent_flag' => '$lctr.index',
    ],
    [
        'name'        => 'Delete',
        'flag'        => '$lctr.destroy',
        'parent_flag' => '$lctr.index',
    ],
    [
        'name'        => 'Export',
        'flag'        => '$lctr.export',
        'parent_flag' => '$lctr.index',
    ],
    [
        'name'        => 'Print',
        'flag'        => '$lctr.print',
        'parent_flag' => '$lctr.index',
    ],
    ]; ?>";
        
    $existingPermissions = file_get_contents(base_path().'/platform/plugins/crud/config/permissions.php');
    $position = strpos($existingPermissions, "];");
    $newPermissions = substr_replace($existingPermissions, $permissions.' ', --$position);
    file_put_contents(base_path().'/platform/plugins/crud/config/permissions.php' , $newPermissions) ;

?>             