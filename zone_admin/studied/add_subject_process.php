<?php

    include "../../database/db.php";
    session_start();

    $class = $_POST['class'];
    $subject_id = $_POST['subject_id'];
    $teacher_id = $_POST['teacher_id'];

    $sql = "INSERT INTO student_subjects
            (class , subject_id , teacher_id) 
            VALUES (? , ? , ?) ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sii' , $class , $subject_id , $teacher_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if($stmt->affected_rows > 0){
        $_SESSION['msg'] = "เพิ่มวิชาเสร็จสิ้น";
        header("Location:add_subject.php?class=" . urlencode($class));
        exit();
    }else{
        $_SESSION['error'] = "มีข้อผิดพลาดบางอย่างเกิดขึ้น";
        header("Location:add_subject.php?class=" . urlencode($class));
        exit();
    }

?>