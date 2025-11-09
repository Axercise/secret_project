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
    
    $class = htmlspecialchars($_GET['class']);

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
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <title>ระบบผู้ดูเเล</title>
    
</head>
<body>

    <div class="logo">
        <a href="../indexadmin.php"><img src="../../img/tepleela_logo.png" alt=""></a>
    </div>

    <div class="container">

        <form method="post" action="add_subject_process.php" class="form-set1">
        <input type="hidden" name="class" value="<?php echo $class?>">

        <!-- เลือกวิชา -->
        <label for="subject">เลือกวิชา</label>
        <select name="subject_id" id="subject" class="form-control select2">
            <option value="">เลือกวิชา</option>
            <?php
                $result = $conn->query("SELECT id, subject_code, subject_name FROM subjects ORDER BY subject_name ASC");
                while($row = mysqli_fetch_assoc($result)){
                    echo "<option value='{$row['id']}'>[{$row['subject_code']}] {$row['subject_name']}</option>";
                }
            ?>
        </select>

        <!-- เลือกครู -->
        <label for="teacher">เลือกครูผู้สอน</label>
        <select name="teacher_id" id="teacher" class="form-control select2">
            <option value="">เลือกครู</option>
            <?php
                $result = $conn->query("SELECT id, prefix, firstname, lastname FROM users WHERE role='teacher' ORDER BY firstname ASC");
                while($row = mysqli_fetch_assoc($result)){
                    echo "<option value='{$row['id']}'>{$row['prefix']}{$row['firstname']} {$row['lastname']}</option>";
                }
            ?>
        </select>

        <br>
        <button type="submit" class="confirm-btn" style="margin-top:20px">บันทึก</button>
        </form>

        
    </div>
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
            $error = json_encode($_SESSION['error']);        
                echo "
                <script type='text/javascript'>
                    Swal.fire({
                    title: 'เกิดข้อผิดพลาด',
                    text: '$error',
                    icon: 'error'
                    });
                </script>
                ";
            unset($_SESSION['error']);
        }

    ?>
    <script>
    // search with jquery
    $(document).ready(function() {
        $('.select2').select2({
            width: '100%',
            placeholder: "พิมพ์เพื่อค้นหา...",
            allowClear: true
        });
    });
    </script>

</body>