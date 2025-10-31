<?php

    include('../../database/db.php');
    session_start();
    
    $question_id = $_POST['id'];
    $question = $_POST['question'];

    $sql = "UPDATE evaluation_items SET
            question = ? 
            WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('si' , $question , $question_id);
    $stmt->execute();

    if($stmt->affected_rows > 0){
        $_SESSION['msg'] = "เเก้ไขข้อมูลสำเร็จ";
        header('Location:question.php');
        exit();
    }else{
        $_SESSION['error'] = "กรุณาเเก้ไขข้อมูลก่อนบันทึก";
        header('Location:question.php');
        exit();
    }
 
?>