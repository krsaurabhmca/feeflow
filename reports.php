<?php
$page_title = 'Collection Reports';
require_once 'templates/header.php';

$institute_id = get_institute_id();

// Filters
$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-d');

// Summary Stats for Range
$stmt = $pdo->prepare("SELECT SUM(amount) as total, COUNT(*) as count FROM fees WHERE institute_id = ? AND payment_date BETWEEN ? AND ?");
$stmt->execute([$institute_id, $start_date, $end_date]);
$summary = $stmt->fetch();

// Category-wise Breakdown
$stmt = $pdo->prepare("SELECT COALESCE(fc.category_name, f.custom_fee_name, 'Other') as label, SUM(f.amount) as value 
                      FROM fees f 
                      LEFT JOIN fee_categories fc ON f.fee_category_id = fc.id 
                      WHERE f.institute_id = ? AND f.payment_date BETWEEN ? AND ? 
                      GROUP BY label");
$stmt->execute([$institute_id, $start_date, $end_date]);
$categories_data = $stmt->fetchAll();

// Date-wise Collection
$stmt = $pdo->prepare("SELECT payment_date as label, SUM(amount) as value 
                      FROM fees 
                      WHERE institute_id = ? AND payment_date BETWEEN ? AND ? 
                      GROUP BY payment_date ORDER BY payment_date ASC");
$stmt->execute([$institute_id, $start_date, $end_date]);
$daily_data = $stmt->fetchAll();
?>

<div class="glass-card" style="padding: 1.5rem; margin-bottom: 2rem;">
    <form action="reports.php" method="GET" style="display: flex; gap: 1rem; align-items: flex-end;">
        <div class="form-group" style="margin-bottom: 0;">
            <label>Start Date</label>
            <input type="date" name="start_date" class="form-control" value="<?php echo $start_date; ?>">
        </div>
        <div class="form-group" style="margin-bottom: 0;">
            <label>End Date</label>
            <input type="date" name="end_date" class="form-control" value="<?php echo $end_date; ?>">
        </div>
        <button type="submit" class="btn btn-primary" style="height: 48px;"><i class="fas fa-arrows-rotate"></i> Update View</button>
        <a href="export_collection.php?start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>" class="btn btn-secondary" style="height: 48px; min-width: 150px;"><i class="fas fa-file-csv"></i> Export CSV</a>
    </form>
</div>

<div class="stats-grid">
    <div class="glass-card stat-card">
        <div class="stat-label">Total Collection (Period)</div>
        <div class="stat-value">₹<?php echo number_format($summary['total'] ?? 0, 2); ?></div>
        <div style="font-size: 0.8rem; color: var(--secondary);"><?php echo $start_date; ?> to <?php echo $end_date; ?></div>
    </div>
    <div class="glass-card stat-card">
        <div class="stat-label">Transaction Count</div>
        <div class="stat-value"><?php echo $summary['count']; ?></div>
        <div style="font-size: 0.8rem; color: var(--secondary);">Receipts Generated</div>
    </div>
</div>

<div class="grid-2" style="margin-top: 2rem;">
    <div class="glass-card" style="padding: 1.5rem;">
        <h3>Category Breakdown</h3>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Category</th>
                        <th style="text-align: right;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($categories_data):
    foreach ($categories_data as $row): ?>
                        <tr>
                            <td><?php echo $row['label']; ?></td>
                            <td style="text-align: right;">₹<?php echo number_format($row['value'], 2); ?></td>
                        </tr>
                    <?php
    endforeach;
else: ?>
                        <tr><td colspan="2" style="text-align: center;">No data</td></tr>
                    <?php
endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="glass-card" style="padding: 1.5rem;">
        <h3>Daily Collection</h3>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th style="text-align: right;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($daily_data):
    foreach ($daily_data as $row): ?>
                        <tr>
                            <td><?php echo date('d M, Y', strtotime($row['label'])); ?></td>
                            <td style="text-align: right;">₹<?php echo number_format($row['value'], 2); ?></td>
                        </tr>
                    <?php
    endforeach;
else: ?>
                        <tr><td colspan="2" style="text-align: center;">No data</td></tr>
                    <?php
endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'templates/footer.php'; ?>
