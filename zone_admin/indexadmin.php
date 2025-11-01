<?php

    include "../database/db.php";
    session_start();

    if(!isset($_SESSION['first_pass_id']) || $_SESSION['role'] !== 'admin'){
        header('location:../log_re/index.php');
        exit();
    }

    if(isset($_GET['logout'])){
        session_destroy();
        header("location:../log_re/index.php");
        exit();
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/admin.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="icon" type="image/x-icon" href="../img/tepleela_logo.png">
    <link rel="stylesheet" href="path/to/font-awesome/css/font-awesome.min.css">
    <title>ระบบผู้ดูเเล</title>
    
</head>

<body>
    <div class="container">

        <table>
            <tr>
                <td>ลำดับ</td>
                <td>รายการ</td>
                <td>เเก้ไข</td>
            </tr>
            <tr>
                <td>1</td>
                <td>เเก้ไขเพิ่มเติมวิชา</td>
                <td><a href="subject/subject.php">เเก้ไข</a></td>
            </tr>
            <tr>
                <td>2</td>
                <td>เเก้ไขคำถามเเบบฟอร์ม</td>
                <td><a href="question/question.php">เเก้ไข</a></td>
            </tr>
            <tr>
                <td>3</td>
                <td>เเก้ไขวิชาที่เรียนในเเต่ละห้อง</td>
                <td><a href="studied/studied.php">เเก้ไข</a></td>
            </tr>
            <tr>
                <td>4</td>
                <td>เเก้ไขข้อมูลนักเรียน | คุณครู</td>
                <td><a href="users/users.php">เเก้ไข</a></td>
            </tr>
            <tr>
                
            </tr>
        </table>
        
        <div class="logout">
            <a href="" class="btn-logout">ออกจากระบบ</a>
        </div>

    </div>

    <script>
        document.querySelectorAll(".btn-logout").forEach(link => {
        link.addEventListener("click", function(e) {
        e.preventDefault(); // ป้องกันไม่ให้ลิงก์วิ่งไปทันที

        Swal.fire({
            title: 'คุณแน่ใจหรือไม่?',
            text: "ต้องการออกจากระบบหรือไม่",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'ออกจากระบบ',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "indexadmin.php?logout='1'";
            }
        })
        });
        });
    </script>
</body>
</html>