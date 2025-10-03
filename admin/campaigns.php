<?php
session_start();
require_once '../db.php';

// Only admin access
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin'){
    header('Location: ../index.php');
    exit;
}

$err = $ok = null;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $title = trim($_POST['title'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $campaign_date = $_POST['campaign_date'] ?? '';

    if ($action === 'add') {
        if($title && $location && $campaign_date){
            $stmt = $pdo->prepare("INSERT INTO campaigns (title, location, campaign_date) VALUES (?, ?, ?)");
            $stmt->execute([$title, $location, $campaign_date]);
            $ok = "Campaign added successfully!";
        } else $err = "All fields are required.";
    } elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id){
            $stmt = $pdo->prepare("DELETE FROM campaigns WHERE id=?");
            $stmt->execute([$id]);
            $ok = "Campaign deleted successfully!";
        }
    }
}

// Fetch all campaigns
$campaigns = $pdo->query("SELECT * FROM campaigns ORDER BY campaign_date DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Campaigns - Blood Donation</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<style>
body { background-color:#f8f9fa; }
.navbar { background-color: #dc3545; }
.navbar-brand, .navbar-nav .nav-link { color: white !important; }
.card-campaign { border-top: 5px solid #dc3545; border-radius:0.75rem; box-shadow:0 5px 15px rgba(0,0,0,0.1); }
</style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg">
  <div class="container-fluid">
    <a class="navbar-brand" href="dashboard.php">Blood Donation Admin</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="nav">
    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
  <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="donors.php">Donors</a></li>
        <li class="nav-item"><a class="nav-link" href="campaigns.php">Campaigns</a></li>
        <li class="nav-item"><a class="nav-link" href="alerts.php">Alerts</a></li>
        <li class="nav-item"><a class="nav-link" href="analytics.php">Analytics</a></li>
        <li class="nav-item"><a class="nav-link" href="inventory.php">Inventory</a></li>
        <li class="nav-item"><a class="nav-link active" href="requests.php">Requests</a></li>
        <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container py-5">
    <h3 class="mb-4 text-center text-danger">Manage Campaigns</h3>

    <?php if($err): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($err); ?></div>
    <?php endif; ?>
    <?php if($ok): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($ok); ?></div>
    <?php endif; ?>

    <!-- Add Campaign Form -->
    <div class="card card-campaign p-3 mb-4">
        <h5 class="text-danger">Add New Campaign</h5>
        <form method="post" class="row g-2">
            <input type="hidden" name="action" value="add">
            <div class="col-md-4">
                <input type="text" name="title" class="form-control" placeholder="Campaign Title" required>
            </div>
            <div class="col-md-4">
                <input type="text" name="location" class="form-control" placeholder="Location" required>
            </div>
            <div class="col-md-3">
                <input type="date" name="campaign_date" class="form-control" required>
            </div>
            <div class="col-md-1">
                <button class="btn btn-danger w-100"><i class="bi bi-plus-circle"></i></button>
            </div>
        </form>
    </div>

    <!-- Campaigns List -->
    <div class="row g-3">
        <?php if($campaigns): ?>
            <?php foreach($campaigns as $c): ?>
                <div class="col-md-6">
                    <div class="card card-campaign p-3">
                        <h6 class="text-danger mb-1"><?php echo htmlspecialchars($c['title']); ?></h6>
                        <p><strong>Location:</strong> <?php echo htmlspecialchars($c['location']); ?><br>
                        <strong>Date:</strong> <?php echo htmlspecialchars($c['campaign_date']); ?></p>
                        <form method="post">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?php echo $c['id']; ?>">
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i> Delete</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info">No campaigns found.</div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
