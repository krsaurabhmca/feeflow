<?php
$page_title = 'Collect Fee';
require_once 'templates/header.php';

$institute_id = get_institute_id();
$message = '';
$receipt_id = null;

// Select student if passed via GET
$selected_student_id = $_GET['student_id'] ?? null;

// Fetch Students for select
$stmt = $pdo->prepare("SELECT id, name, roll_no FROM students WHERE institute_id = ? ORDER BY name ASC");
$stmt->execute([$institute_id]);
$students = $stmt->fetchAll();

// Fetch Fee Categories
$stmt = $pdo->prepare("SELECT * FROM fee_categories WHERE institute_id = ? ORDER BY category_name ASC");
$stmt->execute([$institute_id]);
$categories = $stmt->fetchAll();

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['collect_fee'])) {
    $student_id = $_POST['student_id'];
    $fee_cat_id = $_POST['fee_category_id'] ?: null;
    $custom_fee_name = ($_POST['custom_fee_name'] ?? '') ?: ($fee_cat_id ? null : 'General Fee');
    $amount = $_POST['amount'];
    $payment_date = $_POST['payment_date'];
    $method = $_POST['payment_method'];
    $remarks = $_POST['remarks'];

    // Sequential Receipt No: [Prefix][4-digit Serial]
    $inst_stmt = $pdo->prepare("SELECT receipt_prefix FROM institutes WHERE id = ?");
    $inst_stmt->execute([$institute_id]);
    $inst_data = $inst_stmt->fetch();
    $prefix = $inst_data['receipt_prefix'] ?: str_pad($institute_id, 3, '0', STR_PAD_LEFT) . '-';

    $last_rec = $pdo->prepare("SELECT COUNT(*) FROM fees WHERE institute_id = ?");
    $last_rec->execute([$institute_id]);
    $next_serial = str_pad($last_rec->fetchColumn() + 1, 4, '0', STR_PAD_LEFT);
    $receipt_no = $prefix . $next_serial;

    $stmt = $pdo->prepare("INSERT INTO fees (institute_id, student_id, fee_category_id, custom_fee_name, amount, payment_date, payment_method, receipt_no, remarks) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$institute_id, $student_id, $fee_cat_id, $custom_fee_name, $amount, $payment_date, $method, $receipt_no, $remarks])) {
        $receipt_id = $pdo->lastInsertId();
        $message = "Fee collected successfully! Receipt No: $receipt_no";
    }
}
?>

<div class="auth-container" style="min-height: auto; align-items: flex-start; padding-top: 0;">
    <div class="glass-card fade-in" style="width: 100%; max-width: 800px; padding: 2rem;">
        <h3>Fee Payment Form</h3>
        
        <?php if ($message): ?>
            <div class="alert alert-success">
                <?php echo $message; ?>
                <div style="margin-top: 1rem; display: flex; gap: 10px;">
                    <a href="receipt.php?id=<?php echo $receipt_id; ?>" class="btn btn-primary" style="background: var(--dark);"><i class="fas fa-print"></i> Print Receipt</a>
                    <a href="receipt.php?id=<?php echo $receipt_id; ?>&layout=a4_half" class="btn btn-secondary" style="background: white; border: 1px solid #e2e8f0; color: var(--dark);"><i class="fas fa-file-pdf"></i> Export (2-in-A4)</a>
                </div>
            </div>
        <?php
endif; ?>

        <form action="collect_fee.php" method="POST">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div class="form-group">
                    <label>Select Student</label>
                    <select name="student_id" class="form-control" required>
                        <option value="">-- Search Student --</option>
                        <?php foreach ($students as $student): ?>
                            <option value="<?php echo $student['id']; ?>" <?php echo $selected_student_id == $student['id'] ? 'selected' : ''; ?>>
                                <?php echo $student['name']; ?> (<?php echo $student['roll_no']; ?>)
                            </option>
                        <?php
endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Payment Date</label>
                    <input type="date" name="payment_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div class="form-group">
                    <label>Fee Category (Optional)</label>
                    <select name="fee_category_id" id="fee_category_id" class="form-control" onchange="updateAmount(this)">
                        <option value="">-- Custom Fee Name --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>" data-amount="<?php echo $cat['default_amount']; ?>">
                                <?php echo $cat['category_name']; ?>
                            </option>
                        <?php
endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Fee Name (If Category is empty)</label>
                    <input type="text" name="custom_fee_name" id="custom_fee_name" class="form-control" placeholder="e.g. Exam Fee Oct">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div class="form-group">
                    <label>Amount Collected (₹)</label>
                    <input type="number" step="0.01" name="amount" id="amount" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Payment Method</label>
                    <select name="payment_method" class="form-control">
                        <option value="Cash">Cash</option>
                        <option value="Online">Online / UPI</option>
                        <option value="Bank Transfer">Bank Transfer</option>
                        <option value="Cheque">Cheque</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>Remarks (Optional)</label>
                <textarea name="remarks" class="form-control" rows="2"></textarea>
            </div>

            <button type="submit" name="collect_fee" class="btn btn-primary btn-block">Generate Receipt & Save</button>
        </form>
    </div>
</div>

<script>
function updateAmount(select) {
    const selectedOption = select.options[select.selectedIndex];
    const amount = selectedOption.getAttribute('data-amount');
    const customNameInput = document.getElementById('custom_fee_name');
    
    if (amount) {
        document.getElementById('amount').value = amount;
        customNameInput.disabled = true;
        customNameInput.value = '';
    } else {
        customNameInput.disabled = false;
    }
}
</script>

<?php require_once 'templates/footer.php'; ?>
