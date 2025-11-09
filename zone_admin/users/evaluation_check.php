<?php

    include('../../database/db.php');
    session_start();

    $teacher_id = $_GET['teacher_id'];
    $subject_id = $_GET['subject_id'];
    $student_id = $_GET['id'];
    $class = $_GET['class'];

    if(!isset($_SESSION['first_pass_id'])){
        header("location:../../index.php");
        exit();
    }

    if(!isset($teacher_id)){
        header('location:indexstudent.php');
        exit();
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เเบบประเมินคุณครูผู้สอน</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="path/to/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="../../css/admin.css">
</head>
<body>

    <div class="logo">
        <a href="../indexadmin.php"><img src="../../img/tepleela_logo.png" alt=""></a>
    </div>

    <div class="container">
        <!-- logo -->
            
        
        <?php

            // get evaluation_id
            $evaId = "SELECT * FROM evaluations 
            WHERE student_id = ? AND teacher_id = ? AND subject_id = ?";
            $stmt_evaId = $conn->prepare($evaId);
            $stmt_evaId->bind_param('iii',$student_id,$teacher_id,$subject_id);
            $stmt_evaId->execute();
            $evaId_result = $stmt_evaId->get_result();
            $rows_eva = mysqli_fetch_assoc($evaId_result);
            $evaluation_id = $rows_eva['id'];

        
        ?>

        <?php
        
            // query answer
            $sql_ans = "SELECT evaluation_items.id, evaluation_items.question, evaluation_answer.score
                        FROM evaluation_items 
                        LEFT JOIN evaluation_answer  
                        ON evaluation_answer.item_id = evaluation_items.id AND evaluation_answer.evaluation_id = ?
                        ORDER BY evaluation_items.id ASC
                        ";
            $stmt_ans = $conn->prepare($sql_ans);
            $stmt_ans->bind_param("i", $evaluation_id);
            $stmt_ans->execute();
            $result_ans = $stmt_ans->get_result();


        ?>

        <?php

            // select question
            $sql_ans = "SELECT * FROM evaluation_items";
            $result = mysqli_query($conn,$sql_ans);

            $count = 1;
            
        ?>

        <?php
        
            // Get teacher
            $sql_teacher = 
            "SELECT 
            subjects.subject_name,
            subjects.subject_code,
            users.prefix,
            users.firstname,
            users.lastname
            FROM student_subjects
            JOIN subjects ON student_subjects.subject_id = subjects.id
            JOIN users ON student_subjects.teacher_id = users.id
            WHERE teacher_id = ? AND subject_id = ? AND student_subjects.class = ?";
            $stmt_teacher = $conn->prepare($sql_teacher);
            $stmt_teacher->bind_param("iis",$teacher_id,$subject_id,$class);
            $stmt_teacher->execute();
            $teacher_data = $stmt_teacher->get_result();

            // Get student
            $sql_std = "SELECT * FROM users WHERE id = ? ";
            $stmt_std = $conn->prepare($sql_std);
            $stmt_std->bind_param("i" ,$student_id);
            $stmt_std->execute();
            $std_data = $stmt_std->get_result();

        ?>

        <div class="head-grid">

            <!-- Text -->
            <div class="head-text">

                <div><h1>เเบบประเมินคุณครูผู้สอน </h1></div>

                <?php while($teacher = mysqli_fetch_assoc($teacher_data)) : ?>

                <div>
                    <h2> รายวิชา <?php echo $teacher["subject_code"] . ' ' ?>
                    <?php echo $teacher["subject_name"] ?> </h2>
                    
                    <h3>คุณครูผู้สอน <?php echo $teacher['prefix'] . '' . $teacher['firstname'] . " "
                    . $teacher['lastname'];
                    ?></h3>
                </div>

                <?php while($student = mysqli_fetch_assoc($std_data)) : ?>

                <div>
                    <p> นักเรียนผู้ประเมิน <?php echo $student['prefix'] . '' . $student['firstname'] . " " . 
                    $student['lastname'];
                    ?></p> 
                </div>

                <?php endwhile ?>

                <?php endwhile ?>

            </div>

            <!-- Img -->
            <div class="head-img">
                <img src="../../img/img1.png" alt="" width="300px">
            </div>
        
        </div>

        
        <!-- Container Form -->
        <div class="container-form">


            <form action="evaluate_process.php" method="post">
                <!-- ประเมินครูคนไหน -->
                <input type="hidden" name="teacher_id" value="<?php echo $teacher_id?>">
                <!-- ประเมินครูคนนี้ เเล้วครูคนนี้สอนวิชาอะไร -->
                <input type="hidden" name="subject_id" value="<?php echo $subject_id?>">
                    
                <table>
                    <tr class="">
                        <td class="table-first center">ลำดับที่</td>
                        <td class="center">เนื้อหา</td>
                        <td class="center">น้อยที่สุด</td>
                        <td class="center">น้อย</td>
                        <td class="center">ปานกลาง</td>
                        <td class="center">มาก</td>
                        <td class="table-last center">มากที่สุด</td>
                    </tr>

                    <?php while($row = mysqli_fetch_assoc($result_ans)) : ?>

                    <tr>
                        <td class="table-first center"><?php echo $count++ ?></td>
                        <td><?php echo $row['question']?></td>
                        <td class="center"><input type="radio" value="1" name="score[<?php echo $row['id'] ?>]" <?php if ($row['score'] == 1) echo "checked"; ?> disabled></td>
                        <td class="center"><input type="radio" value="2" name="score[<?php echo $row['id'] ?>]" <?php if ($row['score'] == 2) echo "checked"; ?> disabled></td>
                        <td class="center"><input type="radio" value="3" name="score[<?php echo $row['id'] ?>]" <?php if ($row['score'] == 3) echo "checked"; ?> disabled></td>
                        <td class="center"><input type="radio" value="4" name="score[<?php echo $row['id'] ?>]" <?php if ($row['score'] == 4) echo "checked"; ?> disabled></td>
                        <td class="table-last center"><input type="radio" value="5" name="score[<?php echo $row['id'] ?>]" <?php if ($row['score'] == 5) echo "checked"; ?> disabled></td>
                    </tr>

                    <?php endwhile ?>

                    <tr>
                        <?php
                        
                        $sql_content = "SELECT * FROM evaluation_subjective WHERE evaluations_id = $evaluation_id";
                        $content = mysqli_query($conn,$sql_content);
                        
                        while($rows = mysqli_fetch_assoc($content)):
                        ?>
            
                        <td class="table-first center"><?php echo $count++ ?></td>
                        <td style="">ข้อเสนอเเนะเพิ่มเติม</td>
                        <td colspan="5" class="table-last center content"><input type="text" name="content" value="<?php echo $rows['content']?>" disabled></td>
                        <?php endwhile; ?>
                    </tr>
                </table>

                        
                        
            </form>

        </div>