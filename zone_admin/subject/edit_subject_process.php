<?php

    include('../../database/db.php');
    session_start();
    
    $subject_id = $_POST['id'];
    $subject_name = $_POST['subject_name'];
    $subject_code = $_POST['subject_code'];

    $sql = "UPDATE subjects SET
            subject_name = ? , subject_code = ?
            WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssi' , $subject_name , $subject_code , $subject_id);
    $stmt->execute();

    if($stmt->affected_rows > 0){
        $_SESSION['msg'] = "เเก้ไขข้อมูลสำเร็จ";
        header('Location:subject.php');
        exit();
    }else{
        $_SESSION['error'] = "กรุณาเเก้ไขข้อมูลก่อนบันทึก";
        header('Location:subject.php');
        exit();
    }
 
?>