<?php
$page_title = 'Fee Categories';
require_once 'templates/header.php';

$institute_id = get_institute_id();
$message = '';

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_category'])) {
    $name = $_POST['category_name'];
    $amount = $_POST['default_amount'] ?: 0;

    $stmt = $pdo->prepare("INSERT INTO fee_categories (institute_id, category_name, default_amount) VALUES (?, ?, ?)");
    if ($stmt->execute([$institute_id, $name, $amount])) {
        $message = "Fee category '$name' created successfully!";
    }
}

// Fetch Categories
$stmt = $pdo->prepare("SELECT * FROM fee_categories WHERE institute_id = ? ORDER BY category_name ASC");
$stmt->execute([$institute_id]);
$categories = $stmt->fetchAll();
?>

<?php if ($message): ?>
    <div class="alert alert-success fade-in"><?php echo $message; ?></div>
<?php
endif; ?>

<div class="grid-2 fade-in">
    <div class="glass-card" style="padding: 1.5rem; height: fit-content;">
        <h3><i class="fas fa-plus-circle" style="opacity: 0.5;"></i> New Category</h3>
        <p style="color: var(--secondary); font-size: 0.85rem; margin-bottom: 1.5rem;">Create a category with a pre-set amount to speed up fee collection.</p>
        <form action="fee_categories.php" method="POST">
            <div class="form-group">
                <label>Category Label</label>
                <input type="text" name="category_name" class="form-control" placeholder="e.g. Admission Fee" required>
            </div>
            <div class="form-group">
                <label>Default Amount (₹)</label>
                <input type="number" step="0.01" name="default_amount" class="form-control" placeholder="0.00">
            </div>
            <button type="submit" name="add_category" class="btn btn-primary btn-block"><i class="fas fa-save"></i> Save Category</button>
        </form>
    </div>

    <div class="glass-card" style="padding: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
            <h3 style="margin: 0;"><i class="fas fa-list-ul" style="opacity: 0.5;"></i> Active Categories</h3>
            <span class="badge badge-red"><?php echo count($categories); ?> Total</span>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Category Name</th>
                        <th style="text-align: right;">Default Rate</th>
                        <th style="text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($categories):
    foreach ($categories as $cat): ?>
                        <tr>
                            <td><strong><?php echo $cat['category_name']; ?></strong></td>
                            <td style="text-align: right; color: var(--black); font-weight: 700;">₹<?php echo number_format($cat['default_amount'], 2); ?></td>
                            <td style="text-align: center;">
                                <a href="delete_category.php?id=<?php echo $cat['id']; ?>" class="btn-secondary" style="padding: 5px 10px; border-radius: 6px; color: var(--danger); text-decoration: none;" onclick="return confirm('Delete this category?')">
                                    <i class="fas fa-trash-can"></i>
                                </a>
                            </td>
                        </tr>
                    <?php
    endforeach;
else: ?>
                        <tr><td colspan="3" style="text-align: center; padding: 3rem; color: var(--secondary);">No fee categories registered.</td></tr>
                    <?php
endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'templates/footer.php'; ?>
