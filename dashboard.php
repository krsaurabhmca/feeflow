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

<div class="stats-grid fade-in" style="gap: 1rem; margin-bottom: 1rem;">
    <div class="glass-card stat-card" style="padding: 1rem;">
        <div class="stat-label">Students</div>
        <div class="stat-value"><?php echo $total_students; ?></div>
        <div style="color: var(--primary); font-size: 0.7rem; font-weight: 600;"><i class="fas fa-user-check"></i> ACTIVE</div>
    </div>
    <div class="glass-card stat-card" style="padding: 1rem;">
        <div class="stat-label">Total Revenue</div>
        <div class="stat-value">₹<?php echo number_format($total_collection / 1000, 1); ?>k</div>
        <div style="color: var(--success); font-size: 0.7rem; font-weight: 600;"><i class="fas fa-chart-line"></i> +12% VS LAST MONTH</div>
    </div>
    <div class="glass-card stat-card" style="padding: 1rem;">
        <div class="stat-label">Today</div>
        <div class="stat-value">₹<?php echo number_format($today_collection, 0); ?></div>
        <div style="color: var(--warning); font-size: 0.7rem; font-weight: 600;"><i class="fas fa-clock"></i> ON TRACK</div>
    </div>
    <div class="glass-card stat-card" style="padding: 1rem;">
        <div class="stat-label">Classes</div>
        <div class="stat-value"><?php echo $total_classes; ?></div>
        <div style="color: var(--secondary); font-size: 0.7rem; font-weight: 600;"><i class="fas fa-building"></i> VARIOUS COURSES</div>
    </div>
</div>

<div class="grid-2" style="gap: 1rem;">
    <div class="glass-card" style="padding: 1.25rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h3 style="font-size: 1rem; margin: 0;"><i class="fas fa-receipt" style="color: var(--primary); opacity: 0.8;"></i> Recent Collections</h3>
            <a href="all_receipts.php" style="color: var(--primary); font-size: 0.75rem; font-weight: 800; text-decoration: none; border-bottom: 2px solid transparent; transition: 0.2s;" onmouseover="this.style.borderBottomColor='var(--primary)'" onmouseout="this.style.borderBottomColor='transparent'">VIEW LOGS</a>
        </div>
        <div class="table-container">
            <table style="width: 100%;">
                <tbody>
                    <?php
$stmt = $pdo->prepare("SELECT f.*, s.name as student_name FROM fees f JOIN students s ON f.student_id = s.id WHERE f.institute_id = ? ORDER BY f.created_at DESC LIMIT 5");
$stmt->execute([$institute_id]);
$recent_fees = $stmt->fetchAll();
if ($recent_fees):
    foreach ($recent_fees as $fee): ?>
                        <tr>
                            <td style="padding: 0.6rem 0.5rem;"><span style="font-family: monospace; font-weight: 700; color: var(--secondary); font-size: 0.8rem;">#<?php echo $fee['receipt_no']; ?></span></td>
                            <td style="padding: 0.6rem 0.5rem;">
                                <div style="font-weight: 700; font-size: 0.85rem;"><?php echo $fee['student_name']; ?></div>
                                <div style="font-size: 0.7rem; color: #94a3b8;"><?php echo date('d M, Y', strtotime($fee['payment_date'])); ?></div>
                            </td>
                            <td style="padding: 0.6rem 0.5rem; text-align: right; color: var(--black); font-weight: 900;">₹<?php echo number_format($fee['amount'], 0); ?></td>
                        </tr>
                    <?php
    endforeach;
else: ?>
                        <tr><td colspan="3" style="text-align: center; padding: 2rem; color: var(--secondary);">No transactions recorded.</td></tr>
                    <?php
endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="glass-card" style="padding: 1.25rem;">
        <h3 style="font-size: 1rem; margin-bottom: 1.25rem;"><i class="fas fa-bolt" style="color: #f59e0b;"></i> Quick Actions</h3>
        <div style="display: grid; gap: 0.75rem;">
            <a href="students.php" class="nav-link" style="background: var(--light); color: var(--dark); padding: 1rem; border-radius: 0.75rem; font-weight: 700; display: flex; align-items: center; gap: 1rem; border: 1px solid #e2e8f0;">
                <div style="width: 40px; height: 40px; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--primary); box-shadow: var(--shadow-sm);"><i class="fas fa-user-plus"></i></div>
                Register Student
            </a>
            <a href="collect_fee.php" class="nav-link" style="background: var(--light); color: var(--dark); padding: 1rem; border-radius: 0.75rem; font-weight: 700; display: flex; align-items: center; gap: 1rem; border: 1px solid #e2e8f0;">
                <div style="width: 40px; height: 40px; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #10b981; box-shadow: var(--shadow-sm);"><i class="fas fa-indian-rupee-sign"></i></div>
                Collect Fee
            </a>
            <a href="profile.php" class="nav-link" style="background: var(--light); color: var(--dark); padding: 1rem; border-radius: 0.75rem; font-weight: 700; display: flex; align-items: center; gap: 1rem; border: 1px solid #e2e8f0;">
                <div style="width: 40px; height: 40px; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--secondary); box-shadow: var(--shadow-sm);"><i class="fas fa-cog"></i></div>
                System Settings
            </a>
        </div>
    </div>
</div>

<?php require_once 'templates/footer.php'; ?>
