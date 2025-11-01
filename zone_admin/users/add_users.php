<?php

    include "../../database/db.php";
    session_start();

    if(!isset($_SESSION['first_pass_id']) || $_SESSION['role'] !== 'admin'){
        header('location:../../log_re/index.php');
        exit();
    }

    if(isset($_GET['logout'])){
        session_destroy();
        header("location:../../log_re/index.php");
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
<body>
    <div class="container">
        <form action="add_users_process.php" method="POST">

            <label for="prefix">คำนำหน้า</label>
            <select name="prefix" id="">
                <option value="">-</option>
                <option value="เด็กชาย">เด็กชาย</option>
                <option value="เด็กหญิง">เด็กหญิง</option>
                <option value="นาย">นาย</option>
                <option value="นาง">นาง</option>
                <option value="นางสาว">นางสาว</option>
            </select>

            <label for="firstname">ชื่อ</label>
            <input type="text" name="firstname">

            <label for="lastname">นามสกุล</label>
            <input type="text" name="lastname">

            <label for="class">ห้อง</label>
            <input type="text" name="class">

            <label for="no">เลขที่</label>
            <input type="text" name="no">

            <br>
            <label for="first_pass_id">เลขประจำตัวนักเรียน | คุณครู</label>
            <input type="text" name="first_pass_id" maxlength="5">

            <label for="citizen_id">รหัสประจำตัวประชาชน</label>
            <input type="text" name="citizen_id" maxlength="13">
            
            <label for="role">บทบาท</label>
            <select name="role" id="">
                <option value="">-</option>
                <option value="admin">ผู้ดูเเล</option>
                <option value="teacher">คุณครู</option>
                <option value="student">นักเรียน</option>
            </select>
            <button type="submit">เพิ่มข้อมูลผู้ใช้งาน</button>
        </form>
    </div>
</body>

<?php
        if(isset($_SESSION['msg'])){
        $msg = json_encode($_SESSION['msg']);        
            echo "
            <script type='text/javascript'>
                Swal.fire({
                title: 'ดำเนินการเสร็จสิ้น',
                text: '$msg',
                icon: 'success'
                });
            </script>
            ";
            unset($_SESSION['msg']);
        }

        if(isset($_SESSION['error'])){
        $err = json_encode($_SESSION['error']);        
            echo "
            <script type='text/javascript'>
                Swal.fire({
                title: 'เกิดข้อผิดพลาด',
                text: '$err',
                icon: 'error'
                });
            </script>
            ";
            unset($_SESSION['error']);
        }
?>