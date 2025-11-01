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

    // ลบคำถาม ส่งเป็น GET
    if(isset($_GET['delete'])){
        $id = intval($_GET['delete']);

        $del = "DELETE FROM evaluation_items WHERE id = ?";
        $stmtDel = $conn->prepare($del);
        $stmtDel->bind_param("i" , $id);
        
        if($stmtDel->execute()){
            $_SESSION['delete'] = "ลบคำถามเป็นที่เรียบร้อยเเล้ว";
            header("Location:question.php");
            exit();
        }else{
            $_SESSION['error'] = "เกิดข้อผิดพลาดบางอย่าง";
            header("Location:question.php");
            exit();
        }
    }

    // ลบหลายคำถาม
    if(isset($_POST['delete_ids'])){
        $ids = $_POST['delete_ids'];
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $moreDel = "DELETE FROM evaluation_items WHERE id IN ($placeholders) "; // IN คือเลือกตัวไหนบ้าง
        $stmtMoreDel = $conn->prepare($moreDel);
        
        $types = str_repeat('i' , count($ids)); // จำนวนที่เก็บใน array
        $stmtMoreDel->bind_param($types, ...$ids); // ...ด้านหน้า เอา argument ใน array ออกมาทีละตัว

        if($stmtMoreDel->execute()){
            $_SESSION['delete'] = "ลบคำถามทั้งหมดเป็นที่เรียบร้อยเเล้ว";
            header("Location:question.php");     
            exit();
        }else{
            $_SESSION['error'] = "เกิดข้อผิดพลาดบางอย่าง";
            header("Location:question.php");
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
    <?php

    // search query
    $keyword = ""; //ต้องค่าไม่ว่าง keyword ถึงจะทำงาน
    if(isset($_GET['keyword']) && $_GET['keyword'] != ""){
        $keyword = $_GET['keyword'];
        $sql = "SELECT * FROM evaluation_items 
        WHERE question LIKE ? ";
        $stmt = $conn->prepare($sql);
        $search = "%".$keyword."%";
        $stmt->bind_param("s",$search);
        $stmt->execute();
        $result = $stmt->get_result();
        $count = 1;
    }else{
        $sql = "SELECT * FROM evaluation_items";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $count = 1;
    }
    
    ?>

    <div class="container">

    <!-- Search -->
    <form action="question.php" method="GET">
        <input type="text" name="keyword" placeholder="ค้นหาวิชา" 
        value="<?php 
            if(isset($_GET['keyword'])){
                echo htmlspecialchars($_GET['keyword']); //ส่ง keyword เเสดงออกเป็น keyword
            }else{
                echo "";
            }
        ?>">
        <button type="submit">ค้นหาคำถาม</button>
    </form>

    <!-- Table -->
    <form method="post" action="question.php" id="multiDeleteForm">
    <table>
        <tr>
            <td>ลำดับ</td>
            <td>id</td>
            <td>รายการคำถาม</td>
            <td>เเก้ไข</td>
            <td>ลบ</td>
            <td><input type="checkbox" id="selectAll"></td>
        </tr>

        <?php if(mysqli_num_rows($result) > 0) : ?>

        <?php while($rows = mysqli_fetch_assoc($result)) : ?>

        <tr>
            <td><?php echo $count++ ?></td>
            <td><?php echo $rows['id']?></td>
            <td><?php echo $rows['question']?></td>
            <td><a href="edit_question.php?id=<?php echo $rows['id']?>">เเก้ไข</a></td>
            <td><a href="question.php?delete=<?php echo $rows['id']?>" class="btn-delete" data-id="<?php echo $rows['id']?>" >ลบ</a></td>
            <td><input type="checkbox" name="delete_ids[]" value="<?php echo $rows['id']; ?>"></td>
        </tr>

        <?php endwhile ?>   
            
        <?php else : ?>
            <div>
                <p>ไม่มีคำถามอยู่เลย</p>
            </div>
        <?php endif ?>

    </table>
            <a href="questionadd.php">เพิ่มคำถาม</a>
            <button type="button" id="deleteSelected">ลบคำถามที่เลือก</button>
    </div>
    </form>

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
        document.querySelectorAll(".btn-delete").forEach(link => {
        link.addEventListener("click", function(e) {
        e.preventDefault(); // ป้องกันไม่ให้ลิงก์วิ่งไปทันที

        let id = this.getAttribute("data-id");
        

        Swal.fire({
            title: 'คุณแน่ใจหรือไม่?',
            text: "คุณต้องการลบคำถามนี้ใช่หรือไม่",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'ใช่, ลบเลย!',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "question.php?delete=" + id;
            }
        })
        });
        });
        

    document.getElementById("selectAll").addEventListener("change", function() {
    let checked = this.checked;
    document.querySelectorAll('input[name="delete_ids[]"]').forEach(cb => cb.checked = checked);
});

// ลบหลายรายการ
    document.getElementById("deleteSelected").addEventListener("click", function() {
    let checkedBoxes = document.querySelectorAll('input[name="delete_ids[]"]:checked');
    if (checkedBoxes.length === 0) {
        Swal.fire('แจ้งเตือน', 'กรุณาเลือกคำถามที่ต้องการลบ', 'warning');
        return;
    }

    Swal.fire({
        title: 'คุณแน่ใจหรือไม่?',
        text: "คุณต้องการลบคำถามที่เลือกทั้งหมดใช่หรือไม่",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'ใช่, ลบเลย!',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById("multiDeleteForm").submit();
        }
    });
});
    
</script>

</body>