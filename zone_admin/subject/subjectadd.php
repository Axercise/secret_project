<?php 

    include "../../database/db.php";
    session_start();

    if(!isset($_SESSION['first_pass_id']) || $_SESSION['role'] !== 'admin'){
        header('location:../../index.php');
        exit();
    }

    if(isset($_GET['logout'])){
        session_destroy();
        header("location:../../index.php");
        exit();
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/admin.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="icon" type="image/x-icon" href="../../img/tepleela_logo.png">
    <link rel="stylesheet" href="path/to/font-awesome/css/font-awesome.min.css">
    <title>ระบบผู้ดูเเล</title>
    
</head>
ิ<body>
    <div class="container">
        <form action="subjectadd_process.php" method="POST">
            <label for="subject_name">ชื่อวิชา</label>
            <input type="text" name="subject_name" placeholder="ชื่อวิชา">
            <label for="subject_code">รหัสวิชา</label>
            <input type="text" name="subject_code" placeholder="รหัสวิชา">
            <button type="submit">เพิ่มวิชา</button>
        </form>
    </div>
</body>
