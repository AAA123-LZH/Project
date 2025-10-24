<?php
session_start();
if (!isset($_SESSION['admin_id']) || $_SESSION['user_type'] != 'admin') {
    header("Location: login.html");
    exit();
}

$servername = "localhost:3306";
$username = "root";
$password = "";
$dbname = "course_registration";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle approve/reject/delete actions
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    $registration_id = intval($_POST['registration_id']);
    $action = $_POST['action'];
    
    if ($action == 'approve') {
        $sql = "UPDATE registrations SET status = 'approved' WHERE registration_id = ?";
    } elseif ($action == 'reject') {
        $sql = "UPDATE registrations SET status = 'rejected' WHERE registration_id = ?";
    } elseif ($action == 'delete') {
        $sql = "DELETE FROM registrations WHERE registration_id = ?";
    }
    
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("i", $registration_id);
        if ($stmt->execute()) {
            $message = "Operation completed successfully!";
        } else {
            $message = "Operation failed: " . $conn->error;
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Registrations - SETU</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            margin-bottom: 30px;
        }
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #007bff;
            text-decoration: none;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }
        .stat-pending { border-left: 4px solid #ffc107; }
        .stat-approved { border-left: 4px solid #28a745; }
        .stat-rejected { border-left: 4px solid #dc3545; }
        .stat-number {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }
        .stat-label {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }
        .registrations-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .registrations-table th,
        .registrations-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .registrations-table th {
            background: #f8f9fa;
            font-weight: bold;
            color: #333;
        }
        .status-pending {
            color: #ffc107;
            font-weight: bold;
        }
        .status-approved {
            color: #28a745;
            font-weight: bold;
        }
        .status-rejected {
            color: #dc3545;
            font-weight: bold;
        }
        .action-buttons {
            display: flex;
            gap: 5px;
        }
        .btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
        }
        .btn-approve {
            background: #28a745;
            color: white;
        }
        .btn-reject {
            background: #ffc107;
            color: black;
        }
        .btn-delete {
            background: #dc3545;
            color: white;
        }
        .btn:hover {
            opacity: 0.8;
        }
        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
        }
        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .no-data {
            text-align: center;
            color: #666;
            padding: 40px;
            font-style: italic;
        }
    </style>
</head>
<body>
    <a href="admin_home.php" class="back-link">← Back to Admin Menu</a>
    
    <div class="container">
        <h1>Manage Course Registrations</h1>

        <?php
        // Get statistics
        $stats = [
            'pending' => $conn->query("SELECT COUNT(*) as count FROM registrations WHERE status = 'pending'")->fetch_assoc()['count'],
            'approved' => $conn->query("SELECT COUNT(*) as count FROM registrations WHERE status = 'approved'")->fetch_assoc()['count'],
            'rejected' => $conn->query("SELECT COUNT(*) as count FROM registrations WHERE status = 'rejected'")->fetch_assoc()['count'],
            'total' => $conn->query("SELECT COUNT(*) as count FROM registrations")->fetch_assoc()['count']
        ];
        ?>

        <!-- Statistics -->
        <div class="stats">
            <div class="stat-card stat-pending">
                <div class="stat-number"><?php echo $stats['pending']; ?></div>
                <div class="stat-label">Pending</div>
            </div>
            <div class="stat-card stat-approved">
                <div class="stat-number"><?php echo $stats['approved']; ?></div>
                <div class="stat-label">Approved</div>
            </div>
            <div class="stat-card stat-rejected">
                <div class="stat-number"><?php echo $stats['rejected']; ?></div>
                <div class="stat-label">Rejected</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['total']; ?></div>
                <div class="stat-label">Total</div>
            </div>
        </div>

        <!-- Operation Message -->
        <?php if (isset($message)): ?>
            <div class="message <?php echo strpos($message, 'successfully') !== false ? 'success' : 'error'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <!-- Registrations List -->
        <h2>All Course Registrations</h2>

        <table class="registrations-table">
            <thead>
                <tr>
                    <th>Reg ID</th>
                    <th>Student</th>
                    <th>Course</th>
                    <th>Lecturer</th>
                    <th>Date Registered</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Get all registration records
                $sql = "
                    SELECT r.registration_id, r.date_registered, r.status,
                           s.student_id, s.full_name as student_name, s.username,
                           c.course_code, c.course_name, c.lecturer
                    FROM registrations r
                    JOIN students s ON r.student_id = s.student_id
                    JOIN courses c ON r.course_id = c.course_id
                    ORDER BY r.date_registered DESC
                ";
                
                $result = $conn->query($sql);
                
                if ($result && $result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $status_class = 'status-' . $row['status'];
                        $registration_date = date('Y-m-d H:i', strtotime($row['date_registered']));
                        
                        echo "<tr>
                                <td>{$row['registration_id']}</td>
                                <td>
                                    <strong>{$row['student_name']}</strong><br>
                                    <small>ID: {$row['student_id']} | {$row['username']}</small>
                                </td>
                                <td>
                                    <strong>{$row['course_code']}</strong><br>
                                    <small>{$row['course_name']}</small>
                                </td>
                                <td>{$row['lecturer']}</td>
                                <td>{$registration_date}</td>
                                <td><span class='{$status_class}'>" . ucfirst($row['status']) . "</span></td>
                                <td>
                                    <div class='action-buttons'>";
                        
                        if ($row['status'] == 'pending') {
                            echo "<form method='POST' style='display: inline;'>
                                    <input type='hidden' name='registration_id' value='{$row['registration_id']}'>
                                    <input type='hidden' name='action' value='approve'>
                                    <button type='submit' class='btn btn-approve'>Approve</button>
                                  </form>
                                  <form method='POST' style='display: inline;'>
                                    <input type='hidden' name='registration_id' value='{$row['registration_id']}'>
                                    <input type='hidden' name='action' value='reject'>
                                    <button type='submit' class='btn btn-reject'>Reject</button>
                                  </form>";
                        }
                        
                        echo "<form method='POST' style='display: inline;'>
                                <input type='hidden' name='registration_id' value='{$row['registration_id']}'>
                                <input type='hidden' name='action' value='delete'>
                                <button type='submit' class='btn btn-delete' onclick='return confirm(\"Are you sure you want to delete this registration?\")'>Delete</button>
                              </form>
                            </div>
                        </td>
                      </tr>";
                    }
                } else {
                    echo '<tr><td colspan="7" class="no-data">No course registrations found.</td></tr>';
                }
                
                $conn->close();
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>