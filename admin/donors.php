<?php
require_once '../db.php';
require_once '../auth.php';
require_admin();

// Handle Add / Edit / Delete
$err = $ok = null;
if($_SERVER['REQUEST_METHOD']==='POST'){
    $action = $_POST['action'] ?? '';
    $name  = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass  = trim($_POST['password'] ?? '');
    $blood = trim($_POST['blood_group'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    if($action==='add'){
        if($name && $email && $pass){
            $stmt = $pdo->prepare("INSERT INTO users (name,email,password,role,blood_group,phone) VALUES (?,?,?,?,?,?)");
            $stmt->execute([$name,$email,$pass,'donor',$blood,$phone]);
            $ok = "Donor added successfully.";
        } else $err="All fields required.";
    } elseif($action==='edit'){
        $id = (int)($_POST['id'] ?? 0);
        if($id && $name && $email){
            $stmt = $pdo->prepare("UPDATE users SET name=?, email=?, blood_group=?, phone=? ".($pass?" ,password=? ":"")." WHERE id=? AND role='donor'");
            if($pass){
                $stmt->execute([$name,$email,$blood,$phone,$pass,$id]);
            } else {
                $stmt->execute([$name,$email,$blood,$phone,$id]);
            }
            $ok = "Donor updated.";
        }
    } elseif($action==='delete'){
        $id = (int)($_POST['id'] ?? 0);
        if($id) { $pdo->prepare("DELETE FROM users WHERE id=? AND role='donor'")->execute([$id]); $ok="Donor deleted."; }
    }
}

// Fetch donors
$donors = $pdo->query("SELECT * FROM users WHERE role='donor' ORDER BY name")->fetchAll();
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Donors - BDMS Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background-color:#f8f9fa; }
.navbar { background-color: #dc3545; }
.navbar-brand, .navbar-nav .nav-link { color: white !important; }
.card { border-radius:0.75rem; box-shadow:0 5px 15px rgba(0,0,0,0.1); }
</style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="dashboard.php">BDMS Admin</a>
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

<div class="container py-4">
    <h3 class="text-center text-danger mb-4">Manage Donors</h3>

    <?php if($err) echo "<div class='alert alert-danger'>$err</div>"; ?>
    <?php if($ok) echo "<div class='alert alert-success'>$ok</div>"; ?>

    <!-- Add Donor Form -->
    <div class="card p-3 mb-4">
        <h5 class="mb-3">Add New Donor</h5>
        <form method="post" class="row g-2">
            <input type="hidden" name="action" value="add">
            <div class="col-md-4"><input type="text" name="name" class="form-control" placeholder="Full Name" required></div>
            <div class="col-md-4"><input type="email" name="email" class="form-control" placeholder="Email" required></div>
            <div class="col-md-2"><input type="password" name="password" class="form-control" placeholder="Password" required></div>
            <div class="col-md-2"><input type="text" name="blood_group" class="form-control" placeholder="Blood Group"></div>
            <div class="col-md-2"><input type="text" name="phone" class="form-control" placeholder="Phone"></div>
            <div class="col-md-2"><button class="btn btn-danger w-100">Add Donor</button></div>
        </form>
    </div>

    <!-- Donors Table -->
    <div class="card p-3">
        <div class="table-responsive">
        <table class="table table-striped align-middle">
            <thead class="table-dark">
                <tr><th>Name</th><th>Email</th><th>Blood</th><th>Phone</th><th>Actions</th></tr>
            </thead>
            <tbody>
            <?php foreach($donors as $d): ?>
            <tr>
                <td><?php echo htmlspecialchars($d['name']); ?></td>
                <td><?php echo htmlspecialchars($d['email']); ?></td>
                <td><?php echo htmlspecialchars($d['blood_group']); ?></td>
                <td><?php echo htmlspecialchars($d['phone']); ?></td>
                <td class="d-flex gap-1 flex-wrap">
                    <!-- Edit Modal Trigger -->
                    <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $d['id']; ?>">Edit</button>
                    <form method="post" style="display:inline">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?php echo $d['id']; ?>">
                        <button class="btn btn-sm btn-danger" onclick="return confirm('Delete donor?')">Delete</button>
                    </form>
                </td>
            </tr>

            <!-- Edit Modal -->
            <div class="modal fade" id="editModal<?php echo $d['id']; ?>" tabindex="-1">
            <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header"><h5>Edit Donor</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <form method="post">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="id" value="<?php echo $d['id']; ?>">
                        <div class="mb-2"><input type="text" name="name" value="<?php echo $d['name']; ?>" class="form-control" required></div>
                        <div class="mb-2"><input type="email" name="email" value="<?php echo $d['email']; ?>" class="form-control" required></div>
                        <div class="mb-2"><input type="text" name="blood_group" value="<?php echo $d['blood_group']; ?>" class="form-control"></div>
                        <div class="mb-2"><input type="text" name="phone" value="<?php echo $d['phone']; ?>" class="form-control"></div>
                        <div class="mb-2"><input type="password" name="password" class="form-control" placeholder="Leave blank to keep password"></div>
                        <button class="btn btn-danger w-100">Save Changes</button>
                    </form>
                </div>
            </div>
            </div>
            </div>

            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
