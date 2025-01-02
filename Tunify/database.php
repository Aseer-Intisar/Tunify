<?php
    $db_server="localhost";
    $db_user="root";
    $db_pass="";
    $db_name="tunify";
    $conn="";
    
    
    $conn=mysqli_connect($db_server,$db_user,$db_pass,$db_name);
    if($conn){
    
    echo"Welcome to Tunify";
    
    }

    else{ echo"404 Not Found";
    }

?>