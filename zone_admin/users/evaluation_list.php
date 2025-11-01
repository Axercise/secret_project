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

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">  
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เว็บไซต์ประเมินคุณครูผู้สอน</title>
    <link rel="stylesheet" href="../css/student.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="icon" type="image/x-icon" href="../img/tepleela_logo.png">
    <link rel="stylesheet" href="path/to/font-awesome/css/font-awesome.min.css">
</head>
<body>

    <?php

        // qury find teacher สอนไรบ้าง นับเเค่ 1 count ถ้าเจอ 1 ค่า
        $teacher_id = $_GET['id'];

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

    <div class="container">
        <table>
            <tr>
                <td>ลำดับที่</td>
                <td>รหัสวิชา</td>
                <td>ชื่อวิชา</td>
                <td>จำนวนการประเมิน</td>
                <td>ผลการประเมิน</td>
            </tr>
        
            <?php if(mysqli_num_rows($result) > 0) :?>
            
            <?php while($rows = mysqli_fetch_assoc($result)) :?>

            <tr>
                <td><?php echo $count++; ?></td>
                <td><?php echo $rows['subject_code']?></td>
                <td><?php echo $rows['subject_name']?></td>
                <td><?php echo $rows['total_evaluations']?></td>
                <td><a href="evaluation_summarize.php?teacher_id=<?php echo $teacher_id?>&subject_id=<?php echo $rows['id']?>">ผลการประเมิน</a></td>
            </tr>

            <?php endwhile; ?>

            <?php else:?>

                <h1>คุณครูคนนี้ไม่ยังมีข้อมูลการประเมินจากนักเรียน</h1>

            <?php endif; ?>

            
        </table>
    </div>
</body>