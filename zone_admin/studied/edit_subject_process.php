<?php

    include "../../database/db.php";
    session_start();

    $id = $_POST['id'];
    $subject_id = $_POST['subject_id'];
    $teacher_id = $_POST['teacher_id'];

    $sql = "UPDATE student_subjects SET subject_id = ? , teacher_id = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii" , $subject_id , $teacher_id , $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($stmt->affected_rows > 0){
        $_SESSION['msg'] = "อัพเดตข้อมูลสำเร็จ";
        header("location:studied.php");
        exit();
    }else{
        $_SESSION['error'] = "เกิดข้อผิดพลาดบางอย่าง";
        header("location:studied.php");
        exit();
    }

?>