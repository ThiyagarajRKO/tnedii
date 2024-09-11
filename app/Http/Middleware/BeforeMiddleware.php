<?php
/**
 * Description Before MiddleWare
 *
 * @author Sabari Shankar.parthi
 */
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Foundation\Http\Middleware\TransformsRequest;

class BeforeMiddleware 
{
    
   public function handle($request, Closure $next)
    {
       
       $key = pack("H*", "0123456789abcdef0123456789abcdef");
       $iv =  pack("H*", "abcdef9876543210abcdef9876543210");
       if($request->query()){
           if($request->has('columns')){
               $decryptColumns = base64_decode($request->query('columns'));
//               $decryptColumns = openssl_decrypt($request->query('columns'), 'AES-128-CBC', $key, 0, $iv);
               $oldQueryString = $request->query->all(); // To not lose other params
               $oldQueryString['columns'] = $this->getColumns($decryptColumns);               
               
               $request->query = new \Symfony\Component\HttpFoundation\ParameterBag($oldQueryString);
           }
       }
        return $next($request);
    }
    
    protected function getColumns($decryptedColumns){
       $columns=json_decode($decryptedColumns);
       $originals =[];
       foreach($columns as $column){
           $originals[] = (array) $column;
       }
        return $originals;
    }
}
