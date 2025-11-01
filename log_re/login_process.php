<?php

    session_start();
    include '../database/db.php';

    if($_SERVER["REQUEST_METHOD"] === "POST"){

        $first_pass_id = $_POST["first_pass_id"];
        $citizen_id = $_POST['citizen_id'];

        $sql = "SELECT id,first_pass_id,citizen_id,role FROM users WHERE first_pass_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s" , $first_pass_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if(mysqli_num_rows($result) === 1){

            $rows = mysqli_fetch_assoc($result);

            // ยืนยัน password
            if($rows['citizen_id'] === $citizen_id){

                if($rows['role'] === "admin"){

                    // admin
                    $_SESSION["prefix"] = $rows['prefix'];
                    $_SESSION["firstname"] = $rows['firstname'];
                    $_SESSION["lastname"] = $rows['lastname'];
                    $_SESSION['first_pass_id'] = $rows['first_pass_id'];
                    $_SESSION['role'] = $rows['role'];
                    $_SESSION['alert'] = "เข้าสู่ระบบผู้ดูเเลสำเร็จ";
                    header("location:../zone_admin/indexadmin.php");
                    exit();

                }elseif($rows['role'] === "teacher"){
                    
                    // teacher
                    $_SESSION['id'] = $rows['id'];  
                    $_SESSION["prefix"] = $rows['prefix'];
                    $_SESSION["firstname"] = $rows['firstname'];
                    $_SESSION["lastname"] = $rows['lastname'];
                    $_SESSION['first_pass_id'] = $rows['first_pass_id'];
                    $_SESSION['role'] = $rows['role'];
                    $_SESSION['alert'] = "เข้าสู่ระบบคุณครูสำเร็จ";
                    header("location:../zone_teacher/indexteacher.php");
                    exit();

                }elseif($rows['role'] === "student"){

                    // student
                    $_SESSION["prefix"] = $rows['prefix'];
                    $_SESSION["firstname"] = $rows['firstname'];
                    $_SESSION["lastname"] = $rows['lastname'];
                    $_SESSION["class"] = $rows['class'];
                    $_SESSION["no"] = $rows['no'];
                    $_SESSION['first_pass_id'] = $rows['first_pass_id'];
                    $_SESSION['role'] = $rows['role'];
                    $_SESSION['alert'] = "เข้าสู่ระบบนักเรียนสำเร็จ";
                    header("location:../zone_student/indexstudent.php");
                    exit();
                }

        


            }else{
                $_SESSION['message'] = "รหัสผ่านไม่ถูกต้อง";
                header("location:index.php");
            }

        }else{
            $_SESSION['message'] = "เกิดข้อผิดพลาดบางอย่าง";
            header("location:index.php");
        }

    }else{
        $_SESSION['message'] = "เกิดข้อผิดพลาดบางอย่าง";
        header("location:index.php");
    }

?>

<!-- password hash กรอกข้อมูลนักเรียนใหม่เป็นพันคน
 เก็บไว้เป็นทางเลือก
 password_verify($citizen_id,$rows["citizen_id"]) === true
 -->