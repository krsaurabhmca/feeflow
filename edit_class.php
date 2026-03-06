<?php
$page_title = 'Edit Class';
require_once 'templates/header.php';

$id = $_GET['id'] ?? null;
$institute_id = get_institute_id();

if (!$id)
    redirect('classes.php');

$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_class'])) {
    $name = $_POST['class_name'];

    $stmt = $pdo->prepare("UPDATE classes SET class_name = ? WHERE id = ? AND institute_id = ?");
    if ($stmt->execute([$name, $id, $institute_id])) {
        $message = "Class updated successfully!";
    }
}

$stmt = $pdo->prepare("SELECT * FROM classes WHERE id = ? AND institute_id = ?");
$stmt->execute([$id, $institute_id]);
$class = $stmt->fetch();

if (!$class)
    redirect('classes.php');
?>

<div class="glass-card" style="max-width: 500px; margin: 2rem auto; padding: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h3>Edit Class</h3>
        <a href="classes.php" class="btn btn-secondary btn-sm">Back</a>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-success"><?php echo $message; ?></div>
    <?php
endif; ?>

    <form action="" method="POST">
        <div class="form-group">
            <label>Class Name / Course Name</label>
            <input type="text" name="class_name" class="form-control" value="<?php echo htmlspecialchars($class['class_name']); ?>" required placeholder="e.g. Class 10, BCA, Web Dev">
        </div>
        <button type="submit" name="update_class" class="btn btn-primary btn-block">Update Class</button>
    </form>
</div>

<?php require_once 'templates/footer.php'; ?>
