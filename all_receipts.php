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

<div class="glass-card fade-in" style="padding: 1.5rem, margin-bottom: 1.5rem;">
    <form action="all_receipts.php" method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; align-items: flex-end;">
        <div class="form-group" style="margin-bottom: 0;">
            <label>Search Student/Receipt</label>
            <input type="text" name="search" class="form-control" placeholder="Name or Receipt #" value="<?php echo $search; ?>">
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
        <div style="display: flex; gap: 10px;">
            <button type="submit" class="btn btn-primary" style="flex: 1;"><i class="fas fa-search"></i> Filter</button>
            <a href="all_receipts.php" class="btn btn-secondary" title="Clear Filters"><i class="fas fa-rotate-left"></i></a>
        </div>
    </form>
</div>

<div class="glass-card fade-in" style="margin-top: 1.5rem; padding: 1rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; padding: 0 0.5rem;">
        <h3 style="margin: 0;">Transaction History</h3>
        <button onclick="window.print()" class="btn btn-secondary btn-sm" style="font-size: 0.8rem; padding: 5px 12px;"><i class="fas fa-print"></i> Print List</button>
    </div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Receipt No</th>
                    <th>Student Details</th>
                    <th>Category</th>
                    <th>Mode</th>
                    <th style="text-align: right;">Amount</th>
                    <th class="no-print">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($fees):
    foreach ($fees as $fee): ?>
                <tr>
                    <td><?php echo date('d-M-Y', strtotime($fee['payment_date'])); ?></td>
                    <td><span style="font-family: monospace; font-weight: 700;">#<?php echo $fee['receipt_no']; ?></span></td>
                    <td>
                        <strong><?php echo $fee['student_name']; ?></strong><br>
                        <small style="color: var(--secondary);">Roll: <?php echo $fee['roll_no']; ?></small>
                    </td>
                    <td><?php echo $fee['category_name'] ?: $fee['custom_fee_name']; ?></td>
                    <td><span class="badge" style="background: #f1f5f9; padding: 3px 8px; border-radius: 5px; font-size: 0.75rem;"><?php echo $fee['payment_method']; ?></span></td>
                    <td style="text-align: right; font-weight: 800; color: var(--black);">₹<?php echo number_format($fee['amount'], 2); ?></td>
                    <td class="no-print">
                        <div style="display: flex; gap: 5px;">
                            <a href="receipt.php?id=<?php echo $fee['id']; ?>" class="btn btn-primary" style="padding: 5px 10px; font-size: 0.75rem;" title="View Receipt"><i class="fas fa-eye"></i></a>
                            <a href="receipt.php?id=<?php echo $fee['id']; ?>&layout=a4_half" class="btn btn-secondary" style="padding: 5px 10px; font-size: 0.75rem;" title="Print Half A4"><i class="fas fa-print"></i></a>
                        </div>
                    </td>
                </tr>
                <?php
    endforeach;
else: ?>
                <tr><td colspan="7" style="text-align: center; padding: 3rem; color: var(--secondary);">No transactions found matching your filters.</td></tr>
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
