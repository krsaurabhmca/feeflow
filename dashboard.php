<?php
$page_title = 'Overview';
require_once 'templates/header.php';

$institute_id = get_institute_id();

// Fetch Stats
$total_students = $pdo->prepare("SELECT COUNT(*) FROM students WHERE institute_id = ?");
$total_students->execute([$institute_id]);
$total_students = $total_students->fetchColumn();

$total_collection = $pdo->prepare("SELECT SUM(amount) FROM fees WHERE institute_id = ?");
$total_collection->execute([$institute_id]);
$total_collection = $total_collection->fetchColumn() ?? 0;

$today_collection = $pdo->prepare("SELECT SUM(amount) FROM fees WHERE institute_id = ? AND payment_date = CURDATE()");
$today_collection->execute([$institute_id]);
$today_collection = $today_collection->fetchColumn() ?? 0;

$total_classes = $pdo->prepare("SELECT COUNT(*) FROM classes WHERE institute_id = ?");
$total_classes->execute([$institute_id]);
$total_classes = $total_classes->fetchColumn();
?>

<div class="stats-grid fade-in">
    <div class="glass-card stat-card">
        <div class="stat-label">Students</div>
        <div class="stat-value"><?php echo $total_students; ?></div>
        <div style="color: var(--primary); font-size: 0.75rem;"><i class="fas fa-layer-group"></i> Active</div>
    </div>
    <div class="glass-card stat-card">
        <div class="stat-label">Total Collection</div>
        <div class="stat-value">₹<?php echo number_format($total_collection / 1000, 1); ?>k</div>
        <div style="color: var(--success); font-size: 0.75rem;"><i class="fas fa-arrow-trend-up"></i> Growth</div>
    </div>
    <div class="glass-card stat-card">
        <div class="stat-label">Today</div>
        <div class="stat-value">₹<?php echo number_format($today_collection, 0); ?></div>
        <div style="color: var(--warning); font-size: 0.75rem;"><i class="fas fa-calendar-check"></i> Collected</div>
    </div>
    <div class="glass-card stat-card">
        <div class="stat-label">Classes</div>
        <div class="stat-value"><?php echo $total_classes; ?></div>
        <div style="color: var(--secondary); font-size: 0.75rem;"><i class="fas fa-school"></i> Courses</div>
    </div>
</div>

<div class="grid-2">
    <div class="glass-card" style="padding: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3><i class="fas fa-history" style="opacity: 0.5;"></i> Recent Fees</h3>
            <a href="reports.php" style="color: var(--primary); font-size: 0.8rem; font-weight: 700; text-decoration: none;">VIEW ALL</a>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Receipt</th>
                        <th>Student</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
$stmt = $pdo->prepare("SELECT f.*, s.name as student_name FROM fees f JOIN students s ON f.student_id = s.id WHERE f.institute_id = ? ORDER BY f.created_at DESC LIMIT 5");
$stmt->execute([$institute_id]);
$recent_fees = $stmt->fetchAll();
if ($recent_fees):
    foreach ($recent_fees as $fee): ?>
                        <tr>
                            <td>#<?php echo $fee['receipt_no']; ?></td>
                            <td><strong><?php echo $fee['student_name']; ?></strong><br><small style="color: var(--secondary);"><?php echo date('d M', strtotime($fee['payment_date'])); ?></small></td>
                            <td style="color: var(--success); font-weight: 700;">₹<?php echo number_format($fee['amount'], 0); ?></td>
                        </tr>
                    <?php
    endforeach;
else: ?>
                        <tr><td colspan="3" style="text-align: center; padding: 2rem;">No recent activity.</td></tr>
                    <?php
endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="glass-card" style="padding: 1.5rem;">
        <h3><i class="fas fa-star" style="opacity: 0.5;"></i> Shortcuts</h3>
        <div style="display: grid; gap: 1rem; margin-top: 1.5rem;">
            <a href="students.php" class="btn btn-primary" style="background: var(--dark); border-radius: 1rem;"><i class="fas fa-plus-circle"></i> New Registration</a>
            <a href="collect_fee.php" class="btn btn-primary" style="border-radius: 1rem;"><i class="fas fa-indian-rupee-sign"></i> Collect Payment</a>
            <a href="profile.php" class="btn btn-secondary" style="background: white; border: 1px solid #e2e8f0; color: var(--dark); border-radius: 1rem;"><i class="fas fa-sliders"></i> App Settings</a>
        </div>
    </div>
</div>

<?php require_once 'templates/footer.php'; ?>
