<?php

    include("../database/db.php");
    session_start();

    $student_code = $_SESSION['first_pass_id'];
    $teacher_id = $_POST['teacher_id'];
    $subject_id = $_POST['subject_id'];
    $scores = $_POST['score'];
    $content = $_POST['content'];

    if(empty($scores)){
        $_SESSION['error'] = "เเก้ไข";
        header("location:evaluateform.php?teacher_id=$teacher_id&subject_id=$subject_id");
    }

    // get student
    $sql_std = "SELECT * FROM users WHERE first_pass_id = ? ";
    $stmt_std = $conn->prepare($sql_std);
    $stmt_std->bind_param("i" ,$student_code);
    $stmt_std->execute();
    $std_data = $stmt_std->get_result();

    $row = mysqli_fetch_assoc($std_data);

    $student_id = $row['id'];
    

    $sql = "INSERT INTO evaluations (student_id,teacher_id,subject_id) 
    VALUES (? , ? , ?) ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii" , $student_id,$teacher_id,$subject_id);
    $stmt->execute();
    
    // Insert answer and score
    $evaluation_id = $stmt->insert_id;

    foreach($scores as $item_id => $score){
        $sql_answer = "INSERT INTO evaluation_answer (evaluation_id,item_id,
        score) VALUES (? , ? , ?) ";
        $stmt_ans = $conn->prepare($sql_answer);
        $stmt_ans->bind_param("iii" , $evaluation_id,$item_id,$score);
        $stmt_ans->execute();
    }

    // Insert content comment
    $sql_cont = "INSERT INTO evaluation_subjective (evaluations_id,content)
    VALUES (?,?)";
    $stmt_cont = $conn->prepare($sql_cont);
    $stmt_cont->bind_param("is" , $evaluation_id,$content);
    $stmt_cont->execute();

    // report
    if($stmt_ans->affected_rows > 0 && $stmt_cont->affected_rows > 0){
        $_SESSION['save'] = "บันทึกเรียบร้อยเเล้ว";
        header("location:indexstudent.php");
        exit();
    }else{
        $_SESSION['error'] = "เกิดข้อผิดพลาดบางอย่าง";
        header("Location:indexstudent.php");
        exit();
    }

    

?>