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

    $id = intval($_GET['id']);

    $sql = "SELECT * FROM student_subjects WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i" , $id);
    $stmt->execute();
    $result = $stmt->get_result();

    $rows = mysqli_fetch_assoc($result);
    $subject_id = $rows['subject_id'];
    $teacher_id = $rows['teacher_id'];
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
        <form method="post" action="edit_subject_process.php" class="form-set1">
            <input type="hidden" name="id" value="<?php echo $id?>">

            <!-- เลือกวิชา -->
            <label for="subject">เลือกวิชา</label>
            <select name="subject_id" id="subject" class="form-control select2">
                <?php
                    $result_subjects = $conn->query("SELECT id, subject_code, subject_name FROM subjects ORDER BY subject_name ASC");
                    while($row = mysqli_fetch_assoc($result_subjects)) {
                        if($row['id'] == $subject_id){
                            $selected = "selected";
                        }else{
                            $selected = "";
                        }
                        echo "<option value='{$row['id']}' $selected>[{$row['subject_code']}] {$row['subject_name']}</option>";
                    }
                ?>
            </select>

            <!-- เลือกครู -->
            <label for="teacher">เลือกครูผู้สอน</label>
            <select name="teacher_id" id="teacher" class="form-control select2">
                <?php 
                    $result_teacher = $conn->query("SELECT id , prefix , firstname , lastname FROM users WHERE role = 'teacher' ORDER BY firstname ASC ");
                    while($row = mysqli_fetch_assoc($result_teacher)){
                        if($row['id'] == $teacher_id){
                            $selected = "selected";
                        }else{
                            $selected = ""; 
                        }
                        echo "<option value='{$row['id']}' $selected>{$row['prefix']}{$row['firstname']} {$row['lastname']}</option>";
                    }
                ?>
            </select>

            <button type="submit" class="confirm-btn" style="margin-top:20px;">บันทึก</button>
        </form>

    </div>

    <script>
    // search with jquery
    $(document).ready(function() {
        $('.select2').select2({
            width: '100%',
            allowClear: true
        });
    });
    </script>
</body>

