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

    $subject_id = $_GET['id'];
    
    // เเสดงข้อมูลล่าสุด ที่ตรงกับ id เพื่อเเก้ value
    $sql = "SELECT * FROM subjects WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i' , $subject_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if(mysqli_num_rows($result) === 0){
        echo "ไม่พบข้อมูลนี้";
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

    <div class="logo">
        <a href="../indexadmin.php"><img src="../../img/tepleela_logo.png" alt=""></a>
    </div>

    <div class="container">
        <form action="edit_subject_process.php" method='POST' class="form-set1">
            <?php while($rows = mysqli_fetch_assoc($result)) : ?>
                
                <input type="hidden" name="id" value="<?php echo $rows['id']?>">
                <label for="subject_name">ชื่อวิชา</label>
                <input type="text" name="subject_name" value="<?php echo $rows['subject_name']?>">
                <label for="subject_code">รหัสวิชา</label>
                <input type="text" name="subject_code" value="<?php echo $rows['subject_code']?>">

            <?php endwhile ?>

            <button type="submit" class="confirm-btn">ยืนยันการเเก้ไข</button>
        </form>
    </div>
</body>