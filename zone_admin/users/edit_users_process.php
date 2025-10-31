<?php

    include "../../database/db.php";
    session_start();

    $id = $_POST['id'];
    $prefix = $_POST['prefix'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $class = $_POST['class'];
    $no = $_POST['no'];
    $first_pass_id = intval($_POST['first_pass_id']);
    $citizen_id = intval($_POST['citizen_id']); 
    $role = $_POST['role'];

    $check = "SELECT * FROM users WHERE (first_pass_id = ? OR citizen_id = ?) AND id != ?";
    $stmt_check = $conn->prepare($check);
    $stmt_check->bind_param("ssi" , $first_pass_id , $citizen_id , $id);
    $stmt_check->execute();
    $result = $stmt_check->get_result();

    if(mysqli_num_rows($result) > 0){
        $_SESSION['error'] = "รหัสประจำตัวนักเรียน | ครู หรือรหัสประจำตัวประชาชนซ้ำกับข้อมูลในระบบ";
        header('Location:users.php');
        exit();
    }else{
        $sql = "UPDATE users SET prefix = ? , firstname = ? , lastname = ? , class = ? 
            , no = ? , first_pass_id = ? , citizen_id = ? , role = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssssssssi' , $prefix , $firstname , $lastname , $class , $no,
                            $first_pass_id , $citizen_id , $role , $id);
        $stmt->execute();

        if($stmt->affected_rows > 0){
            $_SESSION['msg'] = "เเก้ไขข้อมูลสำเร็จ";
            header('Location:users.php');
            exit();
        }else{
            $_SESSION['error'] = "กรุณาเเก้ไขข้อมูลก่อนบันทึก";
            header('Location:users.php');
            exit();
        }
    }

    
?>