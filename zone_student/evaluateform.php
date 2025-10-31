<?php

    include('../database/db.php');
    session_start();

    $teacher_id = $_GET['teacher_id'];
    $subject_id = $_GET['subject_id'];
    $class = htmlspecialchars($_GET['class']);  
    $student_id = $_SESSION['first_pass_id'];

    if(!isset($_SESSION['first_pass_id'])){
        header("location:../log_re/login.php");
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
    <link rel="stylesheet" href="../css/evaluate.css">
</head>
<body>
    <div class="container">
        <!-- logo -->
            <img src="../img/tepleela_logo.png" alt="" class="logo">
        

        <?php
        
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
            users.lastname,
            student_subjects.class
            FROM student_subjects
            JOIN subjects ON student_subjects.subject_id = subjects.id
            JOIN users ON student_subjects.teacher_id = users.id
            WHERE teacher_id = ? AND subject_id = ? AND student_subjects.class = ?";
            $stmt_teacher = $conn->prepare($sql_teacher);
            $stmt_teacher->bind_param("iis",$teacher_id,$subject_id,$class);
            $stmt_teacher->execute();
            $teacher_data = $stmt_teacher->get_result();

            // Get student
            $sql_std = "SELECT * FROM users WHERE first_pass_id = ? ";
            $stmt_std = $conn->prepare($sql_std);
            $stmt_std->bind_param("i" ,$student_id);
            $stmt_std->execute();
            $std_data = $stmt_std->get_result();

        ?>
        <div class="container-head">

            <!-- Text -->
            <div class="head-text">

                <div><h1>เเบบประเมินคุณครูผู้สอน</h1></div>

                <?php while($teacher = mysqli_fetch_assoc($teacher_data)) : ?>

                <div>
                    <h2> รายวิชา <?php echo $teacher["subject_code"] . ' ' ?>
                    <?php echo $teacher["subject_name"] ?> </h2>
                    
                    <h3>คุณครูผู้สอน <?php echo $teacher['prefix'] . '' . $teacher['firstname'] . " "
                    . $teacher['lastname'];
                    ?></h3>
                </div>

                <?php endwhile ?>

                <?php while($student = mysqli_fetch_assoc($std_data)) : ?>

                <div>
                    <p> นักเรียนผู้ประเมิน <?php echo $student['prefix'] . '' . $student['firstname'] . " " . 
                    $student['lastname'];
                    ?></p> 
                </div>

                <?php endwhile ?>

            </div>

            <!-- Img -->
            <div class="head-img">
                <img src="../img/img1.png" alt="" class="">
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
                    <tr class="header-table">
                        <td class="front-head-table">ลำดับที่</td>
                        <td>เนื้อหา</td>
                        <td>น้อยที่สุด</td>
                        <td>น้อย</td>
                        <td>ปานกลาง</td>
                        <td>มาก</td>
                        <td class="last-head-table">มากที่สุด</td>
                    </tr>

                    <?php while($row = mysqli_fetch_assoc($result)) : ?>

                    <tr>
                        <td style="text-align:center ;" class="front-head-table"><?php echo $count++ ?></td>
                        <td><?php echo $row['question']?></td>
                        <td style="text-align:center ;"><input type="radio" value="1" name="score[<?php echo $row['id'] ?>]" required></td>
                        <td style="text-align:center ;"><input type="radio" value="2" name="score[<?php echo $row['id'] ?>]"></td>
                        <td style="text-align:center ;"><input type="radio" value="3" name="score[<?php echo $row['id'] ?>]"></td>
                        <td style="text-align:center ;"><input type="radio" value="4" name="score[<?php echo $row['id'] ?>]"></td>
                        <td style="text-align:center ;" class="last-head-table"><input type="radio" value="5" name="score[<?php echo $row['id'] ?>]"></td>
                    </tr>

                    <?php endwhile ?>

                    <tr>
                        <td style="text-align: center;" class="front-head-table"><?php echo $count++ ?></td>
                        <td>ข้อเสนอเเนะเพิ่มเติม</td>
                        <td colspan="5" class="last-head-table content"><input type="text" name="content" required></td>
                    </tr>
                </table>

                        <button type="submit" class="submit-btn">บันทึกผลการประเมิน</button>
                        
            </form>

        </div>

        
                
    <?php

        if(isset($_SESSION['error'])){
            echo "
            <script type='text/javascript'>
                Swal.fire({
                title: 'กรุณาตอบเเบบประเมิน',
                text: 'กรุณาตอบเเบบประเมินก่อนบันทึกผลการประเมิน',
                icon: 'error'
                });
            </script>
            ";
            unset($_SESSION['error']);
            // echo '<meta http-equiv="refresh" content="1;url=indexstudent.php" />';
        }

    ?>  
    
    
    
        
</body>
</html> 