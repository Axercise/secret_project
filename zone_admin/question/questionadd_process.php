<?php

use Dom\Mysql;

    include "../../database/db.php";
    session_start();

    $question = $_POST['question'];

    //เช็คดูก่อนว่ามีข้อมูลในตารางไปเเล้วหรือยัง
    $check = "SELECT * FROM evaluation_items
    WHERE question = ? ";
    $stmt_check = $conn->prepare($check);
    $stmt_check->bind_param("s" , $question);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();


    if(mysqli_num_rows($result_check) >= 1){
        $_SESSION['error'] = "คำถามที่คุณเพิ่มมันซ้ำกับคำถามก่อนหน้า";
        header("Location:question.php");
        exit();
    }else if(empty($question)){
        $_SESSION['error'] = "กรุณาใส่รายละเอียดคำถาม";
        header("Location:question.php");
        exit();
    }

    // เพิ่มวิชาในตาราง
    $sql = "INSERT INTO evaluation_items 
    (question) VALUES (?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s" , $question);
    $stmt->execute();
    $result = $stmt->get_result();

    if($stmt->affected_rows > 0){
        $_SESSION['msg'] = "เพิ่มคำถามใหม่เสร็จสิ้น";
        header("Location:question.php");
        exit();
    }
    
?>

