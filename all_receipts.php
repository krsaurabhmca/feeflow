<?php
$page_title = 'Financial Transactions';
require_once 'templates/header.php';

$institute_id = get_institute_id();

// Filters
$search = $_GET['search'] ?? '';
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';
$category_id = $_GET['category_id'] ?? '';

// Build Query
$query = "SELECT f.*, s.name as student_name, s.roll_no, fc.category_name 
          FROM fees f 
          JOIN students s ON f.student_id = s.id 
          LEFT JOIN fee_categories fc ON f.fee_category_id = fc.id 
          WHERE f.institute_id = ?";
$params = [$institute_id];

if ($search) {
    $query .= " AND (s.name LIKE ? OR f.receipt_no LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if ($start_date) {
    $query .= " AND f.payment_date >= ?";
    $params[] = $start_date;
}
if ($end_date) {
    $query .= " AND f.payment_date <= ?";
    $params[] = $end_date;
}
if ($category_id) {
    $query .= " AND f.fee_category_id = ?";
    $params[] = $category_id;
}

$query .= " ORDER BY f.payment_date DESC, f.created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$fees = $stmt->fetchAll();

// Fetch Categories for Filter
$cat_stmt = $pdo->prepare("SELECT * FROM fee_categories WHERE institute_id = ?");
$cat_stmt->execute([$institute_id]);
$categories = $cat_stmt->fetchAll();
?>

<div class="glass-card fade-in" style="padding: 1.5rem; margin-bottom: 1.5rem;">
    <form action="all_receipts.php" method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; align-items: flex-end;">
        <div class="form-group" style="margin-bottom: 0;">
            <label>Search Student/Receipt</label>
            <input type="text" name="search" class="form-control" placeholder="Name or Receipt #" value="<?php echo htmlspecialchars($search); ?>">
        </div>
        <div class="form-group" style="margin-bottom: 0;">
            <label>Category</label>
            <select name="category_id" class="form-control">
                <option value="">All Categories</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo $cat['id']; ?>" <?php echo $category_id == $cat['id'] ? 'selected' : ''; ?>><?php echo $cat['category_name']; ?></option>
                <?php
endforeach; ?>
            </select>
        </div>
        <div class="form-group" style="margin-bottom: 0;">
            <label>From Date</label>
            <input type="date" name="start_date" class="form-control" value="<?php echo $start_date; ?>">
        </div>
        <div class="form-group" style="margin-bottom: 0;">
            <label>To Date</label>
            <input type="date" name="end_date" class="form-control" value="<?php echo $end_date; ?>">
        </div>
        <div style="display: flex; gap: 8px;">
            <button type="submit" class="btn btn-primary" style="flex: 1; height: 42px;"><i class="fas fa-filter"></i> Filter</button>
            <a href="export_collection.php?search=<?php echo urlencode($search); ?>&category_id=<?php echo $category_id; ?>&start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>" class="btn btn-secondary" style="height: 42px;" title="Export Excel"><i class="fas fa-file-excel"></i> Export</a>
            <a href="all_receipts.php" class="btn btn-secondary" style="height: 42px;" title="Reset"><i class="fas fa-rotate-left"></i></a>
        </div>
    </form>
</div>

<div class="glass-card fade-in" style="margin-top: 1rem; padding: 0;">
    <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.25rem 1.5rem; border-bottom: 1px solid #f1f5f9;">
        <h3 style="margin: 0; font-size: 1.1rem;"><i class="fas fa-history" style="color: var(--primary); opacity: 0.8;"></i> Transaction History</h3>
        <button onclick="window.print()" class="btn btn-secondary btn-sm" style="font-size: 0.75rem;"><i class="fas fa-print"></i> Print List</button>
    </div>
    <div class="table-container">
        <table style="width: 100%;">
            <thead>
                <tr>
                    <th style="padding: 0.75rem 1.5rem;">Date</th>
                    <th>Receipt No</th>
                    <th>Student Details</th>
                    <th>Category</th>
                    <th style="text-align: right;">Amount</th>
                    <th class="no-print" style="text-align: center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($fees):
    foreach ($fees as $fee): ?>
                <tr>
                    <td style="padding: 0.75rem 1.5rem; white-space: nowrap; font-size: 0.8rem; font-weight: 600; color: var(--secondary);">
                        <?php echo date('d M, Y', strtotime($fee['payment_date'])); ?>
                    </td>
                    <td><span style="font-family: monospace; font-weight: 700; color: var(--black); font-size: 0.85rem;">#<?php echo $fee['receipt_no']; ?></span></td>
                    <td>
                        <div style="font-weight: 700; color: var(--black);"><?php echo $fee['student_name']; ?></div>
                        <div style="font-size: 0.7rem; color: #94a3b8;">Roll: <?php echo $fee['roll_no']; ?></div>
                    </td>
                    <td><span class="badge" style="background: var(--light); color: var(--dark); font-weight: 700; font-size: 0.7rem; border: 1px solid #e2e8f0;"><?php echo $fee['category_name'] ?: ($fee['custom_fee_name'] ?: 'General'); ?></span></td>
                    <td style="text-align: right; font-weight: 900; color: var(--black); font-size: 1rem;">₹<?php echo number_format($fee['amount'], 2); ?></td>
                    <td class="no-print" style="text-align: center;">
                        <div style="display: flex; gap: 5px; justify-content: center;">
                            <a href="receipt.php?id=<?php echo $fee['id']; ?>" class="btn btn-secondary" style="padding: 6px 10px; font-size: 0.75rem; border-radius: 8px;" title="View"><i class="fas fa-eye"></i></a>
                            <a href="delete_fee.php?id=<?php echo $fee['id']; ?>" class="btn btn-secondary" style="padding: 6px 10px; font-size: 0.75rem; border-radius: 8px; color: var(--danger);" title="Cancel" data-confirm="Are you sure you want to cancel this receipt? This action will reverse the payment entry." data-title="Cancel Receipt?"><i class="fas fa-trash-can"></i></a>
                        </div>
                    </td>
                </tr>
                <?php
    endforeach;
else: ?>
                <tr><td colspan="6" style="text-align: center; padding: 4rem; color: var(--secondary);">No records found.</td></tr>
                <?php
endif; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
@media print {
    .sidebar, .mobile-nav, .no-print, form { display: none !important; }
    .main-content { padding: 0 !important; }
    body { background: white; }
    .glass-card { border: none !important; box-shadow: none !important; }
}
</style>

<?php require_once 'templates/footer.php'; ?>
