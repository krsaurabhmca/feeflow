<?php
$page_title = 'Classes & Courses';
require_once 'templates/header.php';

$institute_id = get_institute_id();
$message = '';

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_class'])) {
        $name = $_POST['class_name'];
        $stmt = $pdo->prepare("INSERT INTO classes (institute_id, class_name) VALUES (?, ?)");
        $stmt->execute([$institute_id, $name]);
        $message = "Class added successfully!";
    }
}

// Fetch Classes
$stmt = $pdo->prepare("SELECT * FROM classes WHERE institute_id = ? ORDER BY class_name ASC");
$stmt->execute([$institute_id]);
$classes = $stmt->fetchAll();
?>

<div class="grid" style="display: grid; grid-template-columns: 1fr 2fr; gap: 1.5rem;">
    <!-- Add Class Form -->
    <div class="glass-card" style="padding: 1.5rem; height: fit-content;">
        <h3>Add New Class</h3>
        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php
endif; ?>
        <form action="classes.php" method="POST">
            <div class="form-group">
                <label for="class_name">Class Name / Course Name</label>
                <input type="text" name="class_name" id="class_name" class="form-control" placeholder="e.g. Class 10, BCA, Web Dev" required>
            </div>
            <button type="submit" name="add_class" class="btn btn-primary btn-block">Save Class</button>
        </form>
    </div>

    <!-- Classes List -->
    <div class="glass-card" style="padding: 1.5rem;">
        <h3>Manage Classes</h3>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Class Name</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($classes):
    foreach ($classes as $class): ?>
                        <tr>
                            <td>#<?php echo $class['id']; ?></td>
                            <td><strong><?php echo $class['class_name']; ?></strong></td>
                            <td><?php echo date('d M, Y', strtotime($class['created_at'])); ?></td>
                            <td>
                                <a href="edit_class.php?id=<?php echo $class['id']; ?>" style="color: var(--primary); margin-right: 10px;">Edit</a>
                                <a href="delete_class.php?id=<?php echo $class['id']; ?>" style="color: var(--danger);" data-confirm="Are you sure you want to delete this class? All associated students may be affected." data-title="Delete Class?">Delete</a>
                            </td>
                        </tr>
                    <?php
    endforeach;
else: ?>
                        <tr><td colspan="4" style="text-align: center; color: var(--secondary);">No classes found</td></tr>
                    <?php
endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'templates/footer.php'; ?>
