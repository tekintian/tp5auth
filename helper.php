<?php
/**
 * @param string    $path
 * @param array     $param
 * @return bool
 */
function checkPath($path,$param=[]){
    $result =  \tp5auth\auth\Auth::checkPath($path,$param);
    return $result;
}
?>