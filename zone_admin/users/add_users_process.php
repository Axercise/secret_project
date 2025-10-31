<?php

    include "../../database/db.php";
    session_start();

    $prefix = $_POST['prefix'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $class = $_POST['class'];
    $no = $_POST['no'];
    $first_pass_id = $_POST['first_pass_id'];
    $citizen_id = $_POST['citizen_id'];
    $role = $_POST['role'];

    $sql_check = "SELECT * FROM users WHERE first_pass_id = ? OR citizen_id = ? ";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("ss", $first_pass_id , $citizen_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if(mysqli_num_rows($result_check) > 0){
        $_SESSION['error'] = "มีบางข้อมูลที่ซ้ำกับในระบบ";
        header("Location:add_users.php");
        exit();
    }elseif(empty($first_pass_id) || empty($citizen_id)){
        $_SESSION['error'] = "กรุณากรอกรหัสประจำตัว";
        header("Location:add_users.php");
        exit();
    }
    else{
        $sql = "INSERT INTO users (prefix,firstname,lastname,class,no,first_pass_id,citizen_id,role) VALUES (?,?,?,?,?,?,?,?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssss",$prefix,$firstname,$lastname,$class,$no,$first_pass_id,$citizen_id,$role);
        $stmt->execute();
        $result = $stmt->get_result();

        if($stmt->affected_rows > 0){
            $_SESSION['msg'] = "เพิ่มข้อมูลผู้ใช้งานสำเร็จ";
            header("Location:add_users.php");
            exit();
        }else{
            $_SESSION['error'] = "เกิดข้อผิดพลาดบางอย่าง";
            header("Location:add_users.php");
            exit();
        }
    }

// ต่อไปเเสดงข้อมูลนักเรียน
?>