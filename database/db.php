<?php

    $conn = mysqli_connect("localhost","root","","secret_project");
    // $conn = mysqli_connect('sql100.infinityfree.com','if0_40309042','197425bank','if0_40309042_secret_project');

    if($conn == true){
        // echo "connect"; 
    }else{
        echo "errorrrrrr";
    }

    mysqli_set_charset($conn, "utf8");
?>