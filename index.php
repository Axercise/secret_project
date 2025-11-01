<?php

    include '../database/db.php';
    session_start();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบประเมินครูผู้สอน</title>
    <link rel="stylesheet" href="../css/login.css">
    <link rel="stylesheet" href="path/to/font-awesome/css/font-awesome.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<?php
    
    if(isset($_SESSION['message'])){
        $msg = json_encode($_SESSION['message']);
        echo "
            <script type='text/javascript'>
                Swal.fire({
                title: '$msg',
                text: 'เกิดข้อผิดพลาดบางอย่าง',
                icon: 'error'
                });
            </script>
            ";
            unset($_SESSION['message']);
    }

    
    ?>

    <div class="form-container">

        <div class="form-box">

            <div class="logo">
                <img src="../img/tepleela_logo.png" alt="" width="90px">
            </div>

            <div class="head-text">
                <span>ระบบประเมินการสอนของคุณครู | </span>  <span style="color:rgb(255, 103, 164)">โรงเรียนเทพลีลา</span>
            </div>
            
            <hr style="opacity:0.2;">

            <form action="log_re/login_process.php" method="post">

                <input type="text" name="first_pass_id" maxlength="5" pattern="[0-9]*" inputmode="numeric" placeholder="รหัสประจำตัวนักเรียน">
                <input type="password" name="citizen_id" maxlength="13" placeholder="รหัสประจำตัวประชาชน" >
                <button type="submit">เข้าสู่ระบบ</button>
            </form>

            <div class="bottom-text">
                <p class="first-text-btm">พัฒนาเเละดูเเลระบบโดย | นายอนรรฆ สาสุข</p>
                <p>Copyright © Tammadon Suksa. All rights reserved.</p>
            </div>

        </div>

        <div class="img-box">
            <img src="../img/img2.png" alt="" width="500px" >
        </div>

    </div>

    

</body>
</html>