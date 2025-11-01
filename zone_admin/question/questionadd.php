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
        <form action="questionadd_process.php" method="POST">
            <label for="question">คำถาม</label>
            <input type="text" name="question" placeholder="รายละเอียดคำถาม">
            <button type="submit">เพิ่มคำถามใหม่</button>
        </form>
    </div>
</body>
