<?php

require_once 'functions.php';
enableDebug(true); // Set to false in production

session_start();
include('db.php'); // Include database connection

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Get the logged-in username and role
$username = $_SESSION['username'];

$sql = "
SELECT r.role_name 
FROM user u
JOIN user_role_map urm ON u.user_ID = urm.user_ID
JOIN role r ON urm.role_id = r.role_id
WHERE u.user_email = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$roles = [];
while ($row = $result->fetch_assoc()) {
    $roles[] = $row['role_name'];
}
if (empty($roles)) {
    $roles[] = 'User';
}

// Assign role variable
$role = $user['user_role'] ?? 'User';

// Restrict access to only Subject Coordinators
if (!in_array('Coordinator', $roles)) {
    $_SESSION['error'] = "Access Denied. Only Subject Coordinators can create exams.";
    header("Location: dashboard.php");
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['parameters'])) {

    $exam_ID = isset($_POST['exam_ID']) ? (int) $_POST['exam_ID'] : null;
    $truss_ID = isset($_POST['truss_ID']) ? (int) $_POST['truss_ID'] : null;
    $parameters = $_POST['parameters']; // array of parameter rows


    $insert_stmt = $conn->prepare("INSERT INTO parameter (parameter_name, parameter_lower, parameter_upper, exam_ID, truss_ID) VALUES (?, ?, ?, ?, ?)");

    foreach ($parameters as $param) {
        $name = $param['name'];
        $lower = $param['lower'];
        $upper = $param['upper'];

        $insert_stmt->bind_param("sddii", $name, $lower, $upper, $exam_ID, $truss_ID);
        $insert_stmt->execute();
    }

    $_SESSION['success'] = "Parameters saved successfully.";
    header("Location: dashboard.php");
    exit();
}

$exam_result = $conn->query("SELECT exam_ID, exam_name FROM exam ORDER BY time_created DESC");

$truss_result = $conn->query("SELECT truss_ID, truss_name, exam_ID FROM trussimage ORDER BY truss_name ASC");
$trusses = [];
while ($row = $truss_result->fetch_assoc()) {
    $trusses[$row['exam_ID']][] = $row; // group by exam_ID
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Exam Step 1</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="custom.css">
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/img/favicon.ico">
</head>

<body>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card p-4 shadow-lg login-card text-white">
            <div class="text-left">
                <a href="create_exam_step2.php">
                    <u>Back</u>
            </div>
            <div class="text-center">
                <a href="dashboard.php"><img src="assets/img/logo_unisaonline.png" alt="Logo" class="mb-3"
                        width="220"></a>
            </div>
            <div class="card-body text-center">
                <h4>
                    Welcome, you are logged in as
                    <strong>
                        <?php echo htmlspecialchars(implode(' & ', $roles)); ?>
                    </strong>
                </h4>


                <form method="POST" action="">
                    <div class="mb-3">
                        <select name="exam_ID" id="examSelect" class="form-select form-control text-light" required
                            onchange="filterTrusses(this.value)">
                            <option value="" disabled selected>Select Exam</option>
                            <?php while ($exam = $exam_result->fetch_assoc()): ?>
                                <option value="<?= $exam['exam_ID'] ?>"><?= htmlspecialchars($exam['exam_name']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <select name="truss_ID" id="trussSelect" class="form-select form-control text-light mt-3"
                            required>
                            <option value="" disabled selected>Select Truss</option>
                        </select>
                    </div>


                    <table class="table table-bordered table-sm text-white" id="parameterTable">
                        <thead>
                            <tr>
                                <th>Parameter Name</th>
                                <th>Lower Bound</th>
                                <th>Upper Bound</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>

                    <button type="button" class="btn btn-secondary mb-3" onclick="addRow()">Add Parameter</button>
                    <button type="submit" class="btn btn-light w-100">Finish</button>
                </form>

            </div>
        </div>
    </div>


    <script>
        function addRow() {
            const table = document.getElementById('parameterTable').querySelector('tbody');
            const rowCount = table.rows.length;

            const row = document.createElement('tr');
            row.innerHTML = `
        <td><input type="text" name="parameters[${rowCount}][name]" class="form-control form-control-sm text-dark" required></td>
        <td><input type="number" step="any" name="parameters[${rowCount}][lower]" class="form-control form-control-sm text-dark" required></td>
        <td><input type="number" step="any" name="parameters[${rowCount}][upper]" class="form-control form-control-sm text-dark" required></td>
        <td><button type="button" class="btn btn-sm btn-danger" onclick="this.closest('tr').remove()">Remove</button></td>
    `;
            table.appendChild(row);
        }
    </script>
    <script>
        const trussOptions = <?= json_encode($trusses); ?>;

        function filterTrusses(examID) {
            const select = document.getElementById('trussSelect');
            select.innerHTML = '<option value="" disabled selected>Select Truss</option>';

            if (trussOptions[examID]) {
                trussOptions[examID].forEach(t => {
                    const opt = document.createElement('option');
                    opt.value = t.truss_ID;
                    opt.textContent = t.truss_name;
                    select.appendChild(opt);
                });
            }
        }
    </script>


</body>

</html>