<?php

    include "../database/db.php";
    session_start();

    if(!isset($_SESSION['first_pass_id']) || $_SESSION['role'] !== 'admin'){
        header('location:../index.php');
        exit();
    }

    if(isset($_GET['logout'])){
        session_destroy();
        header("location:../index.php");
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

    <div class="logo">
        <img src="../img/tepleela_logo.png" alt="">
    </div>

    <div class="container">

            <div class="logout">
                <a href="" class="btn-logout">ออกจากระบบ</a>
            </div>

        <div class="head-grid">

            <div class="head-text">

                <p class="welcome">ยินดีต้อนรับ</p>
                <p class="name">Admin Console</p>

            </div>

            <div class="head-img">
                <img src="../img/img4.jpg" alt="" width="300px">
            </div>

        </div>

        <table>

            <tr>
                <td class="table-first">ลำดับ</td>
                <td style="text-align:center;">รายการ</td>
                <td class="table-last">เเก้ไข</td>
            </tr>
            <tr>
                <td class="table-first">1</td>
                <td>เเก้ไขเพิ่มเติมวิชา</td>
                <td class="table-last"><a href="subject/subject.php" class="edit-btn">เเก้ไข</a></td>
            </tr>
            <tr>
                <td class="table-first">2</td>
                <td>เเก้ไขคำถามเเบบฟอร์ม</td>
                <td class="table-last"><a href="question/question.php" class="edit-btn">เเก้ไข</a></td>
            </tr>
            <tr>
                <td class="table-first">3</td>
                <td>เเก้ไขวิชาที่เรียนในเเต่ละห้อง</td>
                <td class="table-last"><a href="studied/studied.php" class="edit-btn">เเก้ไข</a></td>
            </tr>
            <tr>
                <td class="table-first">4</td>
                <td>เเก้ไขข้อมูลนักเรียน | คุณครู</td>
                <td class="table-last"><a href="users/users.php" class="edit-btn">เเก้ไข</a></td>
            </tr>
            <tr>
                
            </tr>
        </table>
        
        

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