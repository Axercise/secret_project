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

    $id = $_GET['id'];

    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i",$id);
    $stmt->execute();
    $result = $stmt->get_result();

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

        <?php if(mysqli_num_rows($result) === 1) :?>
        <?php while($rows = mysqli_fetch_assoc($result)) :?>

        <form action="edit_users_process.php" method="POST" class="form-set1">
            
        <input type="hidden" name="id" value="<?php echo $rows['id']?>">

        <label for="prefix">คำนำหน้า</label>            
            <select name="prefix" id="" class="select-form-set1">
                <option value="">-</option>
                <option value="เด็กชาย" <?php if($rows['prefix'] == 'เด็กชาย') echo "selected"?>>เด็กชาย</option>
                <option value="เด็กหญิง" <?php if($rows['prefix'] == 'เด็กหญิง') echo "selected"?>>เด็กหญิง</option>
                <option value="นาย" <?php if($rows['prefix'] == 'นาย') echo "selected"?>>นาย</option>
                <option value="นาง" <?php if($rows['prefix'] == 'นาง') echo "selected"?>>นาง</option>
                <option value="นางสาว" <?php if($rows['prefix'] == 'นางสาว') echo "selected"?>>นางสาว</option>
            </select>

            <label for="firstname">ชื่อ</label>
            <input type="text" name="firstname" value="<?php echo $rows['firstname']?>">

            <label for="lastname">นามสกุล</label>
            <input type="text" name="lastname" value="<?php echo $rows['lastname']?>">

            <label for="class">ห้อง</label>
            <input type="text" name="class" value="<?php echo $rows['class']?>">

            <label for="no">เลขที่</label>
            <input type="text" name="no" value="<?php echo $rows['no']?>">

            <label for="first_pass_id">เลขประจำตัวนักเรียน | คุณครู</label>
            <input type="text" name="first_pass_id" maxlength="5" value="<?php echo $rows['first_pass_id']?>">

            <label for="citizen_id">รหัสประจำตัวประชาชน</label>
            <input type="text" name="citizen_id" maxlength="13" value="<?php echo $rows['citizen_id']?>">
            
            <label for="role">บทบาท</label>
            <select name="role" id="" class="select-form-set1">
                <option value="">-</option>
                <option value="admin" <?php if($rows['role'] == 'admin') echo "selected" ?>>ผู้ดูเเล</option>
                <option value="teacher" <?php if($rows['role'] == 'teacher') echo "selected" ?>>คุณครู</option>
                <option value="student" <?php if($rows['role'] == 'student') echo "selected" ?>>นักเรียน</option>
            </select>
            <button type="submit" class="confirm-btn" >บันทึกการเเก้ไข</button>
        </form>

        <?php endwhile;?>

        <?php endif;?>

    </div>

</body>