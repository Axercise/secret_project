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

    // ลบวิชา ส่งเป็น GET
    if(isset($_GET['delete'])){
        $id = intval($_GET['delete']);

        $del = "DELETE FROM student_subjects WHERE id = ?";
        $stmtDel = $conn->prepare($del);
        $stmtDel->bind_param("i" , $id);
        
        if($stmtDel->execute()){
            $_SESSION['delete'] = "ลบวิชาเป็นที่เรียบร้อยเเล้ว";
            header("Location:studied.php");
            exit();
        }else{
            $_SESSION['error'] = "เกิดข้อผิดพลาดบางอย่าง";
            header("Location:studied.php");
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
<style>
    .box{
        border-radius:8px;
    }
    .box-item{
        display:flex;
        justify-content:space-around;
        align-items: center;
        cursor: pointer;
    }
    .item-list{
        display:none;
    }
    .add-btn{
        
    }
</style>
<body>
    <?php
    
    // มีห้องเรียนอะไรบ้าง
    $classes = ["1/1","1/2","1/3","1/4","1/5","1/6","1/7","1/8","1/9","1/10","2/1","2/2","2/3","2/4","2/5","2/6","2/7","2/8","2/9","2/10","3/1","3/2","3/3","3/4","3/5","3/6","3/7","3/8","3/9","3/10","4/1","4/2","4/3","4/4","4/5","4/6","4/7","5/1","5/2","5/3","5/4","5/5","5/6","5/7","6/1","6/2","6/3","6/4","6/5","6/6","6/7"];
    
    ?>

    <div class="logo">
        <a href="../indexadmin.php"><img src="../../img/tepleela_logo.png" alt=""></a>
    </div>

    <div class="container">
    <?php foreach($classes as $index => $class): ?>
        <div class="box">
            <!-- แถวหัวข้อห้อง -->
            <div class="box-item" onclick="toggleSubjects('subjects-<?php echo $index; ?>')">
                <h3><?php echo $class; ?></h3>
                <div>
                    <a href="add_subject.php?class=<?php echo urlencode($class); ?>" class="add-btn">เพิ่มวิชา</a> <!-- encode url class จะได้ตรวจสอบ ห้องได้  -->
                </div>
            </div>

            <!-- กล่องที่ซ่อนอยู่ -->
            <div id="subjects-<?php echo $index; ?>" class="item-list">
                <table>
                    <tr>
                        <td class="table-first center">รหัสวิชา</td>
                        <td class="center">ชื่อวิชา</td>
                        <td class="center">ครูผู้สอน</td>
                        <td class="center">เเก้ไข</td>
                        <td class="table-last center">ลบ</td>
                    </tr>
                    
                    <?php
                    $sql = "SELECT student_subjects.id , student_subjects.class , subjects.subject_code , subjects.subject_name , users.prefix , users.firstname , users.lastname
                            FROM student_subjects 
                            JOIN subjects ON student_subjects.subject_id = subjects.id
                            JOIN users ON student_subjects.teacher_id = users.id
                            WHERE student_subjects.class = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("s", $class);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    // check ว่า table student_subjects ที่ class มัน = กับตัวใน table ก็จะเเสดงข้อมูลมา
                    if(mysqli_num_rows($result) > 0) : ?>

                    <?php while($rows = mysqli_fetch_assoc($result)) : ?>
                                <tr>
                                    <td class="table-first center"><?php echo $rows['subject_code']?></td>
                                    <td><?php echo $rows['subject_name']?></td>
                                    <td><?php echo $rows['prefix'] . $rows['firstname'] . " " . $rows['lastname'] ?></td>
                                    <td class="center"><a href="edit_subject.php?id=<?php echo $rows['id'] ?>" class="edit-btn">แก้ไข</a> </td>
                                    <td class="table-last center"><a href="studied.php?delete=<?php echo $rows['id'] ?>" class="del-btn" data-id="<?php echo $rows['id']?>" data-name="<?php echo $rows['subject_name']?>" data-class="<?php echo $rows['class']?>">ลบ</a></td>
                                </tr>
                    <?php endwhile ?>

                    <?php else : ?> 

                        <tr><td colspan='5' style="border-radius:15px 15px 15px 15px;">ยังไม่มีรายวิชา</td></tr>
                    
                    <?php endif ?>
                </table>
            </div>
        </div>
    <?php endforeach; ?>
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
            $error = json_encode($_SESSION['error']);        
                echo "
                <script type='text/javascript'>
                    Swal.fire({
                    title: 'เกิดข้อผิดพลาด',
                    text: '$error',
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
    // toggle display
    function toggleSubjects(id){
        var box = document.getElementById(id);
        if(box.style.display === "none"){
            box.style.display = "block";
        }else{
            box.style.display = "none";
        }
    }

    //delete
    document.querySelectorAll(".del-btn").forEach(link => {
        link.addEventListener("click", function(e) {
        e.preventDefault(); // ป้องกันไม่ให้ลิงก์วิ่งไปทันที

        let id = this.getAttribute("data-id");
        let subject_name = this.getAttribute("data-name");
        let classes = this.getAttribute("data-class");

        Swal.fire({
            title: 'คุณแน่ใจหรือไม่?',
            text: "คุณต้องการลบวิชา " + subject_name + " ออกจากห้อง " + classes + " ใช่หรือไม่",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'ใช่, ลบเลย!',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "studied.php?delete=" + id;
            }
        })
        });
        });

</script>

</body>