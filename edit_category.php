<?php
require_once 'templates/header.php';
$id = $_GET['id'] ?? null;
$institute_id = get_institute_id();

if (!$id)
    redirect('fee_categories.php');

$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_category'])) {
    $name = $_POST['category_name'];
    $amount = $_POST['default_amount'];

    $stmt = $pdo->prepare("UPDATE fee_categories SET category_name = ?, default_amount = ? WHERE id = ? AND institute_id = ?");
    if ($stmt->execute([$name, $amount, $id, $institute_id])) {
        $message = "Category updated!";
    }
}

$stmt = $pdo->prepare("SELECT * FROM fee_categories WHERE id = ? AND institute_id = ?");
$stmt->execute([$id, $institute_id]);
$category = $stmt->fetch();

if (!$category)
    redirect('fee_categories.php');
?>

<div class="glass-card" style="max-width: 500px; margin: 2rem auto; padding: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h3>Edit Fee Category</h3>
        <a href="fee_categories.php" class="btn btn-secondary btn-sm">Back</a>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-success"><?php echo $message; ?></div>
    <?php
endif; ?>

    <form action="" method="POST">
        <div class="form-group">
            <label>Category Label</label>
            <input type="text" name="category_name" class="form-control" value="<?php echo htmlspecialchars($category['category_name']); ?>" required>
        </div>
        <div class="form-group">
            <label>Default Amount (₹)</label>
            <input type="number" step="0.01" name="default_amount" class="form-control" value="<?php echo $category['default_amount']; ?>">
        </div>
        <button type="submit" name="update_category" class="btn btn-primary btn-block">Update Category</button>
    </form>
</div>

<?php require_once 'templates/footer.php'; ?>
