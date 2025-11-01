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

    $question_id = $_GET['id'];
    
    // เเสดงข้อมูลล่าสุด ที่ตรงกับ id เพื่อเเก้ value
    $sql = "SELECT * FROM evaluation_items WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i' , $question_id);
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
    <div class="container">
        <form action="edit_question_process.php" method='POST'>
            <?php while($rows = mysqli_fetch_assoc($result)) : ?>
                
                <input type="hidden" name="id" value="<?php echo $rows['id']?>">
                <label for="question">รายละเอียดคำถาม</label>
                <input type="text" name="question" value="<?php echo $rows['question']?>">

            <?php endwhile ?>

            <button type="submit">ยืนยันการเเก้ไข</button>
        </form>
    </div>
</body>