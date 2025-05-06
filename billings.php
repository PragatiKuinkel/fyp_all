<?php
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Database connection
require_once '../includes/dbconnection.php';

// Initialize variables
$vendors = [];
$error = null;
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';

try {
    // Base query to get vendors with bill information
    $query = "
        SELECT 
            u.id as vendor_id,
            u.full_name as vendor_name,
            COUNT(b.id) as total_bills,
            MAX(b.created_at) as last_bill_date,
            CASE 
                WHEN COUNT(CASE WHEN b.status = 'pending' THEN 1 END) > 0 THEN 'Pending'
                ELSE 'Reviewed'
            END as status
        FROM users u
        LEFT JOIN bills b ON u.id = b.vendor_id
        WHERE u.role = 'vendor'
    ";

    // Add search condition if search query exists
    if (!empty($searchQuery)) {
        $query .= " AND u.full_name LIKE :search";
    }

    $query .= " GROUP BY u.id, u.full_name ORDER BY last_bill_date DESC";

    $stmt = $dbh->prepare($query);
    
    if (!empty($searchQuery)) {
        $searchParam = "%{$searchQuery}%";
        $stmt->bindParam(':search', $searchParam);
    }

    $stmt->execute();
    $vendors = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Billings error: " . $e->getMessage());
    $error = "Error fetching vendors: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billings - EventPro</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="light-mode">
    <!-- Sidebar -->
    <div class="sidebar">
        <?php include 'includes/sidebar.php'; ?>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="content-header">
            <h1>Billings</h1>
            
            <!-- Search Bar -->
            <form action="billings.php" method="GET" class="search-form">
                <div class="search-input">
                    <input type="text" name="search" placeholder="Search vendors..." value="<?php echo htmlspecialchars($searchQuery); ?>">
                    <button type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>

        <?php if ($error): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>

        <div class="content-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Vendor Name</th>
                            <th>Number of Bills</th>
                            <th>Last Bill Sent</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($vendors)): ?>
                        <tr>
                            <td colspan="5" class="text-center">No vendors found</td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($vendors as $vendor): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($vendor['vendor_name']); ?></td>
                                <td><?php echo htmlspecialchars($vendor['total_bills']); ?></td>
                                <td><?php echo $vendor['last_bill_date'] ? date('M d, Y', strtotime($vendor['last_bill_date'])) : 'No bills'; ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo strtolower($vendor['status']); ?>">
                                        <?php echo htmlspecialchars($vendor['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="view_bills.php?vendor_id=<?php echo $vendor['vendor_id']; ?>" class="btn btn-primary btn-sm">
                                        View Bills
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="../assets/js/admin.js"></script>
    <style>
        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .search-form {
            flex: 0 0 300px;
        }

        .search-input {
            position: relative;
        }

        .search-input input {
            width: 100%;
            padding: 8px 35px 8px 12px;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            background: var(--input-bg);
            color: var(--text-color);
        }

        .search-input button {
            position: absolute;
            right: 0;
            top: 0;
            height: 100%;
            padding: 0 12px;
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
        }

        .search-input button:hover {
            color: var(--text-color);
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .table th,
        .table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        .table th {
            font-weight: 600;
            background: var(--card-bg);
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.9em;
            font-weight: 500;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-reviewed {
            background: #d4edda;
            color: #155724;
        }

        .btn-sm {
            padding: 4px 8px;
            font-size: 0.9em;
        }

        @media (max-width: 768px) {
            .content-header {
                flex-direction: column;
                gap: 15px;
            }

            .search-form {
                width: 100%;
            }

            .table-responsive {
                overflow-x: auto;
            }
        }
    </style>
</body>
</html> 