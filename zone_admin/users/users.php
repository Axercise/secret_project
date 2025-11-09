<?php

    include "../../database/db.php";
    session_start();

    if(!isset($_SESSION['first_pass_id']) || $_SESSION['role'] !== 'admin'){
        header('location:../../index.php');
        exit();
    }

    if(isset($_GET['logout'])){
        session_destroy();
        header("location:../../index.php");
        exit();
    }

    // Search Firstname Lastname 
    if(isset($_GET['keyword']) && $_GET['keyword'] != ""){
        $keyword = $_GET['keyword'];
    
        $check = "SELECT * FROM users WHERE firstname LIKE ? OR lastname LIKE ? ORDER BY no ASC ";
        $stmt_check = $conn->prepare($check);
        $search = "%".$keyword."%";
        $stmt_check->bind_param("ss" , $search , $search);
        $stmt_check->execute();
        $result = $stmt_check->get_result();
    }elseif(isset($_GET['keyword']) && $_GET['keyword'] == ""){
        // ถ้า keyword ว่าง
    }

    // Search Class
    if(isset($_GET['class']) && $_GET['class'] != "" && isset($_GET['role'])){
        $class = $_GET['class'];
        $role = $_GET['role'];  
        
        $check = "SELECT * FROM users WHERE class LIKE ? AND role = ? ORDER BY no ASC";
        $stmt_check = $conn->prepare($check);
        $search = "%".$class."%";
        $stmt_check->bind_param("ss" , $search , $role);
        $stmt_check->execute();
        $result = $stmt_check->get_result();
    }elseif(isset($_GET['class']) && $_GET['class'] == "" &&  isset($_GET['role'])){
        $class = $_GET['class'];
        $role = $_GET['role'];
        
        $check = "SELECT * FROM users WHERE role = ? ORDER BY first_pass_id ASC";
        $stmt_check = $conn->prepare($check);
        $stmt_check->bind_param("s" , $role);
        $stmt_check->execute();
        $result = $stmt_check->get_result();
    }

    // Delete
    if(isset($_GET['delete'])){
        $id = intval($_GET['delete']);

        $del = "DELETE FROM users WHERE id = ?";    
        $stmtDel = $conn->prepare($del);
        $stmtDel->bind_param("i" , $id);
        
        if($stmtDel->execute()){
            $_SESSION['delete'] = "ลบผู้ใช้งานเป็นที่เรียบร้อยเเล้ว";
            header("Location:users.php");
            exit();
        }else{
            $_SESSION['error'] = "เกิดข้อผิดพลาดบางอย่าง";
            header("Location:users.php");
            exit();
        }
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/admin.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="icon" type="image/x-icon" href="../../img/tepleela_logo.png">
    <link rel="stylesheet" href="path/to/font-awesome/css/font-awesome.min.css">
    <title>ระบบผู้ดูเเล</title>
    
</head>
<body>

    <div class="logo">
        <a href="../indexadmin.php"><img src="../../img/tepleela_logo.png" alt=""></a>
    </div>

    <div class="container">

        <div class="grid-bar"> 

        <!-- Search Name -->
        <form action="users.php" method="get">
            <input type="text" name="keyword" placeholder="ค้นหาชื่อ-นามสกุล"
            value="<?php
                if(isset($_GET['keyword'])){
                    echo htmlspecialchars($_GET['keyword']);
                }else{
                    echo "";
                }
            ?>">
            <button type="submit">ค้นหา</button>
        </form>
        
        <br>
        <!-- Search Class -->
        <form action="users.php" method="get">

            <input type="text" name="class" placeholder="ค้นหาห้องเรียน" style="margin-left:10px"
            value="<?php 
            if(isset($_GET['class'])){
                echo htmlspecialchars($_GET['class']);
            }else{
                echo "";
            }

            ?>">

            <select name="role" id="" class="select-btn">
                <option value="student">นักเรียน</option>            
                <option value="teacher">คุณครู</option>
            </select>

            <button type="submit">ค้นหา</button>
        </form>

            <a href="add_users.php" style="width: 160px;">เพิ่มข้อมูลผู้ใช้งาน</a>

        </div>

        <h3 style="margin-left:30px;">** การลบข้อมูลส่งผลถึงข้อมูลที่เชื่อมโยงอยู่จะหายไป</h3>

        <div class="user-table">

            <table>

                <tr>
                    <td class="table-first center">ลำดับ</td>
                    <td class="center">id</td>
                    <td class="center">คำนำหน้า</td>
                    <td class="center">ชื่อ</td>
                    <td class="center">นามสกุล</td>
                    <td class="center">ห้องเรียน</td>
                    <td class="center">เลขที่</td>
                    <td class="center">ตำเเหน่ง</td>
                    <td class="center">รหัสประจำตัวนักเรียน | ครู</td>
                    <td class="center">รหัสประจำตัวประชาชน</td>
                    <td class="center">เเก้ไขข้อมูล</td>
                    <td class="center">ลบข้อมูล</td>
                    <td class="table-last center">ตรวจสอบผลการประเมิน</td>
                </tr>
                
                <?php if(isset($result)) :?>
                <?php if(mysqli_num_rows($result) > 0) : ?>
                <?php 
                    $count = 1;
                ?> 
                <?php while($rows = mysqli_fetch_assoc($result)) :?>
                
                <tr>
                    <td class="table-first center"><?php echo $count++ ?></td>
                    <td class="center"><?php echo $rows['id']?></td>
                    <td class="center"><?php echo $rows['prefix']?></td>
                    <td><?php echo $rows['firstname']?></td>
                    <td><?php echo $rows['lastname']?></td>
                    <td class="center"><?php echo $rows['class']?></td>
                    <td class="center"><?php echo $rows['no']?></td>
                    <td class="center"><?php echo $rows['role']?></td>
                    <td class="center"><?php echo $rows['first_pass_id']?></td>
                    <td class="center"><?php echo $rows['citizen_id']?></td>
                    <td class="center"><a href="edit_users.php?id=<?php echo $rows['id']?>" class="edit-btn">เเก้ไข</a></td>
                    <td class="center"><a href="users.php?delete=<?php echo $rows['id']?>" class="del-btn" data-id="<?php echo $rows['id'] ?>" data-prefix="<?php echo $rows['prefix']?>" data-firstname="<?php echo $rows['firstname']?>" data-lastname="<?php echo $rows['lastname']?>">ลบ</a></td>
                    <?php if($rows['role'] === 'student') :?>
                        <td class="table-last center"><a href="evaluation_result.php?id=<?php echo $rows['id']?>" class="result-eva-btn">ผลประเมิน</a></td>
                    <?php elseif($rows['role'] === 'teacher') : ?>
                        <td class="table-last center"><a href="evaluation_list.php?id=<?php echo $rows['id']?>" class="result-eva-btn">ผลประเมิน</a></td>
                    <?php endif; ?>
                </tr>

                <?php endwhile; ?>

                <?php endif; ?>
                
                <?php else :?>

                <h3 style="margin-left:30px">กรุณาเลือกห้องเรียน</h3>

                <?php endif; ?>

            </table>
        </div>

    </div>

    <?php
        if(isset($_SESSION['msg'])){
        $msg = json_encode($_SESSION['msg']);        
            echo "
            <script type='text/javascript'>
                Swal.fire({
                title: 'ดำเนินการเสร็จสิ้น',
                text: '$msg',
                icon: 'success'
                });
            </script>
            ";
            unset($_SESSION['msg']);
        }

        if(isset($_SESSION['error'])){
        $err = json_encode($_SESSION['error']);        
            echo "
            <script type='text/javascript'>
                Swal.fire({
                title: 'เกิดข้อผิดพลาด',
                text: '$err',
                icon: 'error'
                });
            </script>
            ";
            unset($_SESSION['error']);
        }

        if(isset($_SESSION['delete'])){
        $del = json_encode($_SESSION['delete']);
            echo "
            <script type='text/javascript'>
                Swal.fire({
                title: 'ลบข้อมูลเสร็จสิ้น',
                text: '$del',
                icon: 'success'
                });
            </script>
            ";
            unset($_SESSION['delete']);
        }
    ?>

    <script>
        document.querySelectorAll(".del-btn").forEach(link => {
        link.addEventListener("click", function(e) {
        e.preventDefault(); // ป้องกันไม่ให้ลิงก์วิ่งไปทันที

        let id = this.getAttribute("data-id");
        let prefix = this.getAttribute("data-prefix");
        let firstname = this.getAttribute("data-firstname");
        let lastname = this.getAttribute("data-lastname");

        Swal.fire({
            title: 'คุณแน่ใจหรือไม่?',
            text: "คุณต้องการลบผู้ใช้งาน " + prefix + firstname + " " + lastname + " ใช่หรือไม่",
            icon: 'warning', 
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'ใช่, ลบเลย!',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "users.php?delete=" + id;
            }
        })
        });
        });
    </script>
</body>