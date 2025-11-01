<?php

    include "../database/db.php";
    session_start();

    if(!isset($_SESSION['first_pass_id']) || $_SESSION['role'] !== 'teacher'){
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
    <title>เว็บไซต์ประเมินคุณครูผู้สอน</title>
    <link rel="stylesheet" href="../css/teacher.css">
    <link rel="stylesheet" href="../css/student.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="icon" type="image/x-icon" href="../img/tepleela_logo.png">
    <link rel="stylesheet" href="path/to/font-awesome/css/font-awesome.min.css">
</head>
<body>
    <?php

        $teacher_id = $_SESSION['first_pass_id'];
        $find_id_sql = "SELECT * FROM users WHERE first_pass_id = ?";
        $send = $conn->prepare($find_id_sql);
        $send->bind_param('s',$teacher_id);
        $send->execute();
        $find_result = $send->get_result();

        if(mysqli_num_rows($find_result) === 1){
            $row = mysqli_fetch_assoc($find_result);
            // $teacher_id
            $teacher_id = $row['id'];
        }else{
            echo "ไม่พบข้อมูลนักเรียน";
            exit();
        }
        
    
        $sql = "SELECT
                subjects.id,
                subjects.subject_name,
                subjects.subject_code,
                COUNT(evaluations.id) AS total_evaluations
                FROM evaluations
                JOIN subjects ON subjects.id = evaluations.subject_id
                WHERE evaluations.teacher_id = ?
                GROUP BY subjects.id , subjects.subject_code , subjects.subject_name
                ORDER BY subjects.subject_name ASC 
                ";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i',$teacher_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $count = 1;

    ?>

    <div class="logo">
        <img src="../img/tepleela_logo.png" alt="">
    </div>

    <div class="container">

            <div class="logout">
                <a href="indexteacher.php?logout='1'" class="btn-logout">ออกจากระบบ</a>
            </div>

            <!-- head grid -->
        <div class="head-grid">

            <div class="head-text">
                <p class="welcome">ยินดีต้อนรับ</p>
                <p class="name"><?php echo $row['prefix'] . '' , $row['firstname'] . ' ' . $row['lastname'] ?></p>

            </div>

            <div class="head-img">
                <img src="../img/img1.png" alt="" width="300px">
            </div>

        </div>

        <table>
            <tr class="table-head">
                <td class="front">ลำดับที่</td>
                <td>รหัสวิชา</td>
                <td>ชื่อวิชา</td>
                <td>จำนวนการประเมิน</td>
                <td class="last">ผลการประเมิน</td>
            </tr>
        
            <?php if(mysqli_num_rows($result) > 0) :?>
            
            <?php while($rows = mysqli_fetch_assoc($result)) :?>

            <tr class="table-row">
                <td class="first-column front"><?php echo $count++; ?></td>
                <td><?php echo $rows['subject_code']?></td>
                <td><?php echo $rows['subject_name']?></td>
                <td style="text-align: center;"><?php echo $rows['total_evaluations']?></td>
                <td class="last-column last"><a href="evaluation_summarize.php?teacher_id=<?php echo $teacher_id?>&subject_id=<?php echo $rows['id']?>">ผลการประเมิน</a></td>
            </tr>

            <?php endwhile; ?>

            <?php else:?>

                <div class="head-grid"><h1>คุณครูคนนี้ไม่ยังมีข้อมูลการประเมินจากนักเรียน</h1></div>
                

            <?php endif; ?>

            
        </table>
    </div>

    <?php 
        // login
        if(isset($_SESSION['alert'])){
            $login = json_encode($_SESSION['alert']);
            echo "
            <script type='text/javascript'>
                Swal.fire({
                title: 'เข้าสู่ระบบสำเร็จ',
                text: $login ,
                icon: 'success'
                });
            </script>
            ";
            unset($_SESSION['alert']);
        }
    ?>

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
                window.location.href = "indexteacher.php?logout='1'";
            }
        })
        });
        });
    </script>
</body>
</html>