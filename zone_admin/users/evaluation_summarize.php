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
    <link rel="stylesheet" href="../../css/admin.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="icon" type="image/x-icon" href="../img/tepleela_logo.png">
    <link rel="stylesheet" href="path/to/font-awesome/css/font-awesome.min.css">
</head>
<style>
    .button{
        cursor: pointer;
        color:green;
    }
    #content{
        display:none;
    }
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

    ?>

    <div class="logo">
        <a href="../indexadmin.php"><img src="../../img/tepleela_logo.png" alt=""></a>
    </div>

    <div class="container">
        <div class="suggest-btn" onclick="toggle('content')">➤ ข้อเสนอเเนะ</div>
        <table>
            
            <tr>
                <td class="table-first center">ลำดับ</td>
                <td class="center">คำถาม</td>
                <td class="center">น้อยที่สุด</td>
                <td class="center">น้อย</td>
                <td class="center">ปานกลาง</td>
                <td class="center">มาก</td>
                <td class="center">มากที่สุด</td>
                <td class="table-last center">ค่าสรุป</td>
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

            <tr>
                <td class="table-first center"><?php echo $i++; ?></td>
                <td><?php echo $q['question']; ?></td>
                <td class="center"><?php echo $q['scores'][1] ?? 0; ?></td>
                <td class="center"><?php echo $q['scores'][2] ?? 0; ?></td>
                <td class="center"><?php echo $q['scores'][3] ?? 0; ?></td>
                <td class="center"><?php echo $q['scores'][4] ?? 0; ?></td>
                <td class="center"><?php echo $q['scores'][5] ?? 0; ?></td>
                <td class="table-last center"><?php echo number_format($avg,2)?></td>
            </tr>
            <?php endforeach; ?>
        </table>
            
        
        <table class="" id="content">
            <tr>
                <td class="table-first center">ลำดับ</td>
                <td class="table-last center">ข้อเสนอเเนะ</td>
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
        
            <tr>
                <td class="table-first center"><?php echo $count++?></td>
                <td class="table-last"><?php echo $rows['content']?></td>
            </tr>
            <?php endwhile ;?>
        </table>

                
    </div>
</body>
<script>
    function toggle(id){
        var box = document.getElementById(id);
        if(box.style.display === "none"){
            box.style.display = "block";
        }else{
            box.style.display = "none";
        }
    }
</script>