<?php
require_once 'templates/header.php';
$id = $_GET['id'] ?? null;
$institute_id = get_institute_id();

if (!$id)
    redirect('students.php');

$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_student'])) {
    $name = $_POST['name'];
    $roll_no = $_POST['roll_no'];
    $class_id = $_POST['class_id'];
    $phone = $_POST['phone'];
    $parent_name = $_POST['parent_name'];
    $session = $_POST['session'];

    $stmt = $pdo->prepare("UPDATE students SET name = ?, roll_no = ?, class_id = ?, phone = ?, parent_name = ?, session = ? WHERE id = ? AND institute_id = ?");
    if ($stmt->execute([$name, $roll_no, $class_id, $phone, $parent_name, $session, $id, $institute_id])) {
        $message = "Student record updated!";
    }
}

$stmt = $pdo->prepare("SELECT * FROM students WHERE id = ? AND institute_id = ?");
$stmt->execute([$id, $institute_id]);
$student = $stmt->fetch();

if (!$student)
    redirect('students.php');

$stmt = $pdo->prepare("SELECT * FROM classes WHERE institute_id = ?");
$stmt->execute([$institute_id]);
$classes = $stmt->fetchAll();
?>

<div class="glass-card" style="max-width: 600px; margin: 2rem auto; padding: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h3>Edit Student Record</h3>
        <a href="students.php" class="btn btn-secondary btn-sm">Back</a>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-success"><?php echo $message; ?></div>
    <?php
endif; ?>

    <form action="" method="POST">
        <div class="form-group">
            <label>Full Name</label>
            <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($student['name']); ?>" required>
        </div>
        <div class="grid-2" style="gap: 1rem;">
            <div class="form-group">
                <label>Roll Number</label>
                <input type="text" name="roll_no" class="form-control" value="<?php echo htmlspecialchars($student['roll_no']); ?>">
            </div>
            <div class="form-group">
                <label>Class/Course</label>
                <select name="class_id" class="form-control">
                    <?php foreach ($classes as $class): ?>
                        <option value="<?php echo $class['id']; ?>" <?php echo $student['class_id'] == $class['id'] ? 'selected' : ''; ?>>
                            <?php echo $class['class_name']; ?>
                        </option>
                    <?php
endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label>Phone Number</label>
            <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($student['phone']); ?>">
        </div>
        <div class="form-group">
            <label>Parent/Guardian</label>
            <input type="text" name="parent_name" class="form-control" value="<?php echo htmlspecialchars($student['parent_name']); ?>">
        </div>
        <div class="form-group">
            <label>Academic Session</label>
            <input type="text" name="session" class="form-control" value="<?php echo htmlspecialchars($student['session']); ?>" placeholder="e.g. 2024-2026">
        </div>
        <button type="submit" name="update_student" class="btn btn-primary btn-block">Update Record</button>
    </form>
</div>

<?php require_once 'templates/footer.php'; ?>
