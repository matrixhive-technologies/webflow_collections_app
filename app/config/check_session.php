<?php
if(isset($_REQUEST['key'])){
    $parts = explode('090##934',$_REQUEST['key']);
    if(!empty($parts[0])){
        session_id($parts[0]);
    }
}
session_start();