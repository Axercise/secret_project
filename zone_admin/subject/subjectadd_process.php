<?php

use Dom\Mysql;

    include "../../database/db.php";
    session_start();

    $subject_name = $_POST['subject_name'];
    $subject_code = $_POST['subject_code'];

    //เช็คดูก่อนว่ามีข้อมูลในตารางไปเเล้วหรือยัง
    $check = "SELECT * FROM subjects 
    WHERE subject_name = ? AND subject_code = ?";
    $stmt_check = $conn->prepare($check);
    $stmt_check->bind_param("ss", $subject_name , $subject_code );
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();


    if(mysqli_num_rows($result_check) >= 1){
        $_SESSION['error'] = "คุณได้เพิ่มวิชานี้ไปเเล้ว";
        header("Location:subject.php");
        exit();
    }else if(empty($subject_name)){
        $_SESSION['error'] = "กรุณาใส่ชื่อวิชาก่อนส่งข้อมูล";
        header("Location:subject.php");
        exit();
    }

    // เพิ่มวิชาในตาราง
    $sql = "INSERT INTO subjects 
    (subject_name , subject_code) VALUES (? , ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss" , $subject_name , $subject_code);
    $stmt->execute();
    $result = $stmt->get_result();

    if($stmt->affected_rows > 0){
        $_SESSION['msg'] = "เพิ่มวิชาใหม่สำเร็จ";
        header("Location:subject.php");
        exit();
    }

?>

