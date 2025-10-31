<?php

    $conn = mysqli_connect('localhost','root','','secret_project');

    if($conn == true){
        // echo "connect"; 
    }else{
        echo "error";
    }


?>