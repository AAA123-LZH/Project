<h2>View My Registered Courses</h2>
<div id="message"></div>
<ul id="my-courses">
    <?php
    // 统一的session用户ID检查
    $student_id = isset($_SESSION['student_id']) ? (int)$_SESSION['student_id'] : (isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0);
    
    if($student_id > 0) {
        // 首先检查数据库连接和表结构
        $test_query = "SHOW TABLES LIKE 'registrations'";
        $test_result = mysqli_query($conn, $test_query);
        
        if(mysqli_num_rows($test_result) == 0) {
            echo "<li>Error: registrations table does not exist</li>";
        } else {
            // 使用更简单的查询先测试
            $sql = "SELECT r.course_id, c.course_name 
                    FROM registrations r 
                    JOIN courses c ON r.course_id = c.course_id 
                    WHERE r.student_id = ?";
            
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("i", $student_id);
                $stmt->execute();
                $res = $stmt->get_result();
                
                if ($res && $res->num_rows > 0) {
                    while($row = $res->fetch_assoc()) {
                        $course_id = (int)$row['course_id'];
                        $course_name = htmlspecialchars($row['course_name']);
                        echo "
                        <li id='mycourse-{$course_id}'>
                            {$course_name} (ID: {$course_id})
                            <button onclick='dropCourse({$course_id})'>Drop</button>
                        </li>";
                    }
                } else {
                    echo "<li>No registered courses found for student ID: {$student_id}</li>";
                    // 调试：检查registrations表中是否有数据
                    $debug_sql = "SELECT * FROM registrations LIMIT 5";
                    $debug_result = mysqli_query($conn, $debug_sql);
                    echo "<li>Debug - Total registrations in system: " . mysqli_num_rows($debug_result) . "</li>";
                }
                $stmt->close();
            } else {
                echo "<li>Error preparing statement: " . $conn->error . "</li>";
            }
        }
    } else {
        echo "<li>Please log in to view your courses</li>";
    }
    ?>
</ul>

<script>
function dropCourse(courseId){
    console.log('Dropping course ID:', courseId);
    
    if (!courseId || courseId <= 0) {
        alert('Invalid course ID: ' + courseId);
        return;
    }
    
    if (!confirm('Are you sure you want to drop this course?')) {
        return;
    }
    
    fetch('drop_course.php', {
        method: 'POST',
        headers: {'Content-Type':'application/json'},
        body: JSON.stringify({course_id: courseId})
    })
    .then(res => res.json())
    .then(data => {
        console.log('Server response:', data);
        const msgDiv = document.getElementById('message');
        msgDiv.textContent = data.message;

        if(data.status === 'success'){
            msgDiv.style.color = 'green';
            const item = document.getElementById('mycourse-' + courseId);
            if(item) {
                item.remove();
            }
        } else {
            msgDiv.style.color = 'red';
        }
    })
    .catch(err => {
        console.error('Error:', err);
        const msgDiv = document.getElementById('message');
        msgDiv.textContent = 'Request failed. Please try again.';
        msgDiv.style.color = 'red';
    });
}
</script>