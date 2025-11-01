<?php

    include "../database/db.php";
    session_start();

    if(!isset($_SESSION['first_pass_id']) || $_SESSION['role'] !== 'teacher'){
        header('location:../log_re/login.php');
        exit();
    }

    if(isset($_GET['logout'])){
        session_destroy();
        header("location:../log_re/login.php");
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
<style>
    
</style>
<body>

    <?php
    
        $teacher_id = $_GET['teacher_id'];
        $subject_id = $_GET['subject_id'];

        $sql_summary = "SELECT 
                    evaluation_subjective.id,
                    evaluation_subjective.evaluations_id,
                    evaluation_subjective.content,
                    evaluation_items.id AS item_id,
                    evaluation_items.question,
                    evaluation_answer.score,
                    COUNT(evaluation_answer.score) AS total_responses
                    FROM evaluation_answer 
                    JOIN evaluations ON evaluation_answer.evaluation_id = evaluations.id
                    JOIN evaluation_items ON evaluation_answer.item_id = evaluation_items.id
                    JOIN evaluation_subjective ON evaluation_subjective.evaluations_id = evaluations.id
                    WHERE evaluations.teacher_id = ? AND evaluations.subject_id = ?
                    GROUP BY evaluation_items.id, evaluation_answer.score
                    ORDER BY evaluation_items.id, evaluation_answer.score";
        $stmt = $conn->prepare($sql_summary);
        $stmt->bind_param("ii", $teacher_id, $subject_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $questions = [];
        while($row = mysqli_fetch_assoc($result)) {
        $questions[$row['item_id']]['question'] = $row['question'];
        $questions[$row['item_id']]['scores'][$row['score']] = $row['total_responses'];

        }

        // ดึงข้อมูลเเสดงครูผู้สอนกับวิชา

        $teacher_query = "SELECT * FROM users WHERE id = $teacher_id";
        $tch = mysqli_query($conn,$teacher_query);

        $subject_query = "SELECT * FROM subjects WHERE id = $subject_id";
        $sbj = mysqli_query($conn,$subject_query);

    ?>

    <div class="logo">
        <img src="../img/tepleela_logo.png" alt="">
    </div>

    <div class="container">

        <div class="head-box">

            <!-- Text -->
            <div class="">

                <div class=""><h1>ผลประเมินครูผู้สอน</h1></div>

                <?php while($subject = mysqli_fetch_assoc($sbj)) : ?>
                
                <div><h2>รายวิชา <?php echo $subject['subject_code']. ' ' .
                $subject['subject_name'] ?></h2></div>

                <?php endwhile ?>

                <?php while($teacher = mysqli_fetch_assoc($tch)) : ?>
                
                <div><h3>ครูผู้สอน <?php echo $teacher['prefix'] .
                $teacher['firstname'] . ' ' . $teacher['lastname'] ?></h3></div>

                <?php endwhile ?>

            </div>

            <!-- Img -->
            <!-- <div class="head-img">
                <img src="../img/img1.png" alt="" class="">
            </div> -->
        
        </div>


            <div class="summarize-btn" onclick="toggle('content')" id="text">ข้อเสนอเเนะ</div>

        <table id="summarize">
            
            <tr class="table-head">
                <td class="front">ลำดับ</td>
                <td>คำถาม</td>
                <td>น้อยที่สุด</td>
                <td>น้อย</td>
                <td>ปานกลาง</td>
                <td>มาก</td>
                <td>มากที่สุด</td>
                <td class="last">ค่าสรุป</td>
            </tr>
            <?php $i = 1; foreach($questions as $q): ?>
            
            <!-- สรุปค่าเฉลี่ย -->
            <?php
            
            $s1 = $q['scores'][1] ?? 0;
            $s2 = $q['scores'][2] ?? 0;
            $s3 = $q['scores'][3] ?? 0;
            $s4 = $q['scores'][4] ?? 0;
            $s5 = $q['scores'][5] ?? 0;
            
            $avg = ((1*$s1)+(2*$s2)+(3*$s3)+(4*$s4)+(5*$s5)) / ($s1+$s2+$s3+$s4+$s5);
            
            ?>

            <tr class="table-row">
                <td style="text-align:center;" class="first-column front"><?php echo $i++; ?></td>
                <td><?php echo $q['question']; ?></td>
                <td style="text-align:center;"><?php echo $q['scores'][1] ?? 0; ?></td>
                <td style="text-align:center;"><?php echo $q['scores'][2] ?? 0; ?></td>
                <td style="text-align:center;"><?php echo $q['scores'][3] ?? 0; ?></td>
                <td style="text-align:center;"><?php echo $q['scores'][4] ?? 0; ?></td>
                <td style="text-align:center;"><?php echo $q['scores'][5] ?? 0; ?></td> 
                <td style="text-align:center;" class="last"><?php echo number_format($avg,2)?></td>
            </tr>
            <?php endforeach; ?>
        </table>
            
        
        <table id="content">
            <tr class="table-head">
                <td class="front" style="width:100px;height:20px;">ลำดับ</td>
                <td class="last">ข้อเสนอเเนะ</td>
            </tr>

                <?php
                    $count = 1;
                    $content_sql = "SELECT evaluations_id
                                    , content
                                    FROM evaluation_subjective 
                                    JOIN evaluations ON evaluations.id = evaluation_subjective.evaluations_id
                                    WHERE teacher_id = ? AND subject_id = ?";
                    $stmt_c = $conn->prepare($content_sql);
                    $stmt_c->bind_param("ii", $teacher_id, $subject_id);
                    $stmt_c->execute();
                    $content_result = $stmt_c->get_result();

                ?>

                <?php while($rows = mysqli_fetch_assoc($content_result)) : ?>
        
            <tr class="table-row">
                <td class="first-column front" style=""><?php echo $count++?></td>
                <td class="last" style=""><?php echo $rows['content']?></td>
            </tr>
            <?php endwhile ;?>
        </table>

                
    </div>
</body>
<script>
    function toggle(id) {
    var box = document.getElementById(id); // content 
    var summarize = document.getElementById("summarize");
    var text = document.getElementById("text");

    // ถ้า content ถูกซ่อนอยู่ → แสดง content / ซ่อน summarize
    if (box.style.display === "none" || box.style.display === "") {
        // ตรวจสอบหน้าจอ
        if (window.innerWidth <= 700) {
            box.style.display = "block";
        } else {
            box.style.display = "table";
        }
        summarize.style.display = "none";
        text.innerHTML = "ผลประเมิน";
    } 
    // ถ้า content แสดงอยู่ → ซ่อน content / แสดง summarize
    else {
        box.style.display = "none";
        if (window.innerWidth <= 700) {
            summarize.style.display = "block";
        } else {
            summarize.style.display = "table";
        }
        text.innerHTML = "ข้อเสนอแนะ";
    }
}
</script>