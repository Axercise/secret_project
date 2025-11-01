<?php

    include "../../database/db.php";
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

    $id = $_GET['id'];
    $find_id_sql = "SELECT * FROM users WHERE id = ?";
    $send = $conn->prepare($find_id_sql);
    $send->bind_param('s',$id);
    $send->execute();
    $find_result = $send->get_result();

    if(mysqli_num_rows($find_result) === 1){
        $row = mysqli_fetch_assoc($find_result);
        // $student_id
        $student_id = $row['id'];
        $class = $row['class'];
    }else{
        echo "ไม่พบข้อมูลนักเรียน";
        exit();
    }

    $sql = "SELECT 
    student_subjects.id,
    student_subjects.class,
    student_subjects.subject_id,
    student_subjects.teacher_id,
    subjects.subject_code,
    subjects.subject_name,
    users.prefix,
    users.firstname,
    users.lastname
    FROM student_subjects 
    JOIN subjects ON student_subjects.subject_id = subjects.id
    JOIN users ON student_subjects.teacher_id = users.id
    WHERE student_subjects.class = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $class);
    $stmt->execute();
    $result = $stmt->get_result();

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
    <!-- Count วิชาที่ต้องประเมิน -->
    <?php
    
        $sql_subject_count = "SELECT COUNT(*) AS total
                            FROM student_subjects 
                            WHERE class = ?";
        $stmt_count = $conn->prepare($sql_subject_count);
        $stmt_count->bind_param('s',$class);
        $stmt_count->execute();
        $result_count = $stmt_count->get_result();
        // นับ count ทั้งหมด จาก class หนึ่ง class ว่าเรียนอะไรบ้าง ซึ่งมันจะมีหลาย rows นับจาก rows class
        $row_count = mysqli_fetch_assoc($result_count);
        $count_subject = $row_count['total'];   

        $sql_already_ans_count = "SELECT COUNT(DISTINCT subject_id) AS evaluated
                                    FROM evaluations 
                                    WHERE student_id = ?";
        $stmt_alr_ans = $conn->prepare($sql_already_ans_count);
        $stmt_alr_ans->bind_param('i',$student_id);
        $stmt_alr_ans->execute();
        $result_alr_ans = $stmt_alr_ans->get_result();
        // นับ count เอาอันที่ประเมินไปเเล้วดูจาก student_id ของ table evaluate มาลบกับ count_subject ที่นับจาก class
        $row_alr_ans = mysqli_fetch_assoc($result_alr_ans);
        $count_alr_subject = $row_alr_ans['evaluated'];

        $remaining = $count_subject - $count_alr_subject;

        
    ?>

    <div class="container">
        <div class="head-grid">

        <div class="head-text">
            <p class="welcome">ผลการประเมินของ</p>
            <p class="name"><?php echo $row['prefix'] . '' , $row['firstname'] . ' ' . $row['lastname'] . ' ม.' . 
            $row['class'] . ' ' . 'เลขที่' . ' ' . $row['no'] ?></p>


            <?php if($remaining > 0) : ?>

            <div class="have-subject">

                <span>เหลือวิชาที่ต้องประเมิน <?php echo " <span style='color:rgb(255, 103, 164 )'> $remaining </span>" ?> วิชา</span>

            </div>

            <?php else : ?>
                
            <div class="alr-subject">

                <p>นักเรียนประเมินครบทุกวิชาเเล้ว</p>

            </div>    
            
            <?php endif ?>

        </div>


        </div>


        <!-- Table -->
        <table>
            <tr class="table-head">
                <td class="front">ลำดับที่</td>
                <td>รหัสวิชา</td>
                <td>ชื่อวิชา</td>
                <td>คุณครูผู้สอน</td>
                <td class="last">สถานะ</td>
            </tr>

            <?php $count = 1?>

            <?php while($rows = mysqli_fetch_assoc($result)) : ?>

            <tr class="table-row">
                <td class="first-column front"><?php echo $count++ ?></td>
                <td><?php echo $rows['subject_code']?></td>
                <td><?php echo $rows['subject_name'] ?></td>
                <td><?php echo $rows['prefix'] . '' . $rows['firstname'] . ' ' . $rows['lastname'] ?> </td>
                <?php

                    $teacher_id = $rows['teacher_id'];
                    $subject_id = $rows['subject_id'];

                    // สร้างเงื่อนไขว่าประเมินไปรึยัง !
                    $check_evaluate = "SELECT id FROM evaluations WHERE student_id = ? AND teacher_id = ? AND subject_id = ?";

                    $check_evaluate_stmt = $conn->prepare($check_evaluate);
                    $check_evaluate_stmt->bind_param('iii',$student_id,$teacher_id,$subject_id);
                    $check_evaluate_stmt->execute();
                    $result_evaluate = $check_evaluate_stmt->get_result();

                ?>

                <?php if(mysqli_num_rows($result_evaluate) > 0) :?>
                <?php echo "<td class='last-column last'><a href='evaluation_check.php?teacher_id=$teacher_id&subject_id=$subject_id&id=$id&class=$class' >ตรวจสอบผลการประเมิน</a></td> " ?>
                <?php else: ?>
                <td class="alr_evaluate last"><div>ยังไม่ประเมิน</div></td>
                <?php endif ?>

            </tr>

            <?php endwhile; ?>

        </table>
    </div>

</body>