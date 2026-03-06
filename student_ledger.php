<?php
require_once 'includes/config.php';

$student_id = $_GET['id'] ?? null;
$token = $_GET['token'] ?? null;

if (!$student_id && !$token)
    die("Invalid Access");

$query = "SELECT s.*, c.class_name, i.name as inst_name, i.phone as inst_phone, i.address as inst_address, i.email as inst_email, i.receipt_color, i.qr_payment_link as inst_upi
          FROM students s 
          JOIN institutes i ON s.institute_id = i.id
          LEFT JOIN classes c ON s.class_id = c.id";

if ($token) {
    $stmt = $pdo->prepare("$query WHERE s.ledger_token = ?");
    $stmt->execute([$token]);
}
else if (is_logged_in()) {
    $stmt = $pdo->prepare("$query WHERE s.id = ? AND s.institute_id = ?");
    $stmt->execute([$student_id, get_institute_id()]);
}
else {
    redirect('index.php');
}

$student = $stmt->fetch();
if (!$student)
    die("Student not found!");

// Fetch all Fee History
$stmt = $pdo->prepare("SELECT f.*, fc.category_name FROM fees f LEFT JOIN fee_categories fc ON f.fee_category_id = fc.id WHERE f.student_id = ? ORDER BY f.payment_date DESC");
$stmt->execute([$student['id']]);
$fees = $stmt->fetchAll();

$total_paid = array_sum(array_column($fees, 'amount'));

// Generate public link if token exists
$ledger_token = $student['ledger_token'] ?? null;
$public_link = $ledger_token ? BASE_URL . "student_ledger.php?token=" . $ledger_token : null;
$qr_data = BASE_URL . "student_ledger.php?id=" . $student['id'];
$qr_url = "https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl=" . urlencode($qr_data);
$theme_color = $student['receipt_color'] ?: '#dc2626';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Ledger - <?php echo $student['name']; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: <?php echo $theme_color; ?>; }
        body { padding: 20px; background: #f8fafc; padding-bottom: 40px; }
        .ledger-container { max-width: 900px; margin: auto; }
        
        @media (max-width: 768px) {
            body { padding: 10px; }
            .header-info { grid-template-columns: 1fr !important; }
        }
    </style>
</head>
<body>
    <div class="ledger-container">
        <div class="no-print" style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
            <div>
                <?php if (is_logged_in()): ?>
                    <a href="students.php" class="btn btn-secondary" style="background: white; border: 1px solid #e2e8f0;"><i class="fas fa-arrow-left"></i> Back</a>
                    <?php if (!$ledger_token): ?>
                        <a href="generate_token.php?id=<?php echo $student['id']; ?>" class="btn btn-primary"><i class="fas fa-share-nodes"></i> Enable Sharing</a>
                    <?php
    endif; ?>
                <?php
endif; ?>
            </div>
            <div style="display: flex; gap: 10px;">
                <?php if ($public_link): ?>
                    <button onclick="copyToClipboard('<?php echo $public_link; ?>')" class="btn btn-primary"><i class="fas fa-copy"></i> Copy Link</button>
                <?php
endif; ?>
                <button onclick="window.print()" class="btn btn-secondary"><i class="fas fa-print"></i> Print</button>
            </div>
        </div>

        <div class="glass-card fade-in" style="padding: 25px; margin-bottom: 20px;">
            <div style="border-bottom: 2px solid var(--primary); padding-bottom: 15px; margin-bottom: 25px; display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 20px;">
                <div>
                    <h1 style="margin: 0; color: var(--primary);"><?php echo $student['inst_name']; ?></h1>
                    <p style="color: var(--secondary); margin-top: 5px; font-size: 0.9rem;"><?php echo $student['inst_address']; ?></p>
                </div>
                <div style="text-align: right;">
                    <h3 style="margin: 0;">STUDENT LEDGER</h3>
                    <p style="color: var(--secondary); font-size: 0.8rem;"><?php echo date('d M, Y'); ?></p>
                </div>
            </div>

            <div class="grid-2" style="margin-bottom: 30px;">
                <div class="glass-card" style="padding: 20px; background: rgba(255,255,255,0.4);">
                    <h4 style="margin-bottom: 15px; color: var(--primary); font-size: 0.8rem; text-transform: uppercase;">Student Info</h4>
                    <p style="margin-bottom: 5px;"><strong><?php echo $student['name']; ?></strong></p>
                    <p style="font-size: 0.9rem; color: var(--secondary);">Roll: <?php echo $student['roll_no']; ?> | Class: <?php echo $student['class_name'] ?: 'N/A'; ?></p>
                    <p style="font-size: 0.9rem; color: var(--secondary);">Parent: <?php echo $student['parent_name']; ?></p>
                </div>
                
                <?php if ($student['inst_upi']):
    $upi_link = "upi://pay?pa=" . $student['inst_upi'] . "&pn=" . urlencode($student['inst_name']) . "&tn=" . urlencode("Fees for " . $student['name']);
    $pay_qr = "https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl=" . urlencode($upi_link);
?>
                <div class="glass-card" style="display: flex; align-items: center; gap: 15px; padding: 15px; border: 2px dashed var(--primary); background: #fdfdfd;">
                    <img src="<?php echo $pay_qr; ?>" width="80" alt="Pay QR">
                    <div>
                        <p style="font-weight: 800; color: var(--primary); margin: 0; font-size: 0.9rem;">SCAN TO PAY</p>
                        <p style="font-size: 0.7rem; color: var(--secondary); margin-top: 2px;"><?php echo $student['inst_upi']; ?></p>
                        <a href="<?php echo $upi_link; ?>" class="btn btn-primary" style="padding: 4px 10px; font-size: 0.7rem; margin-top: 5px;">Open UPI App</a>
                    </div>
                </div>
                <?php
else: ?>
                <div class="glass-card" style="background: var(--primary); color: white; display: flex; flex-direction: column; justify-content: center; align-items: center; padding: 20px;">
                    <span style="font-size: 0.8rem; opacity: 0.8;">TOTAL PAID</span>
                    <h2 style="margin: 5px 0; color: white;">₹<?php echo number_format($total_paid, 2); ?></h2>
                </div>
                <?php
endif; ?>
            </div>

            <h3 style="margin-bottom: 15px;">Payment History</h3>
            <div class="table-container">
                <table style="min-width: 600px;">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Receipt</th>
                            <th>Description</th>
                            <th style="text-align: right;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($fees):
    foreach ($fees as $fee): ?>
                            <tr>
                                <td><?php echo date('d M, Y', strtotime($fee['payment_date'])); ?></td>
                                <td><a href="receipt.php?id=<?php echo $fee['id']; ?>" style="color: var(--primary);">#<?php echo $fee['receipt_no']; ?></a></td>
                                <td><?php echo $fee['category_name'] ?: $fee['custom_fee_name']; ?></td>
                                <td style="text-align: right; font-weight: 700;">₹<?php echo number_format($fee['amount'], 2); ?></td>
                            </tr>
                        <?php
    endforeach;
else: ?>
                            <tr><td colspan="4" style="text-align: center; color: var(--secondary); padding: 40px;">No payments recorded yet.</td></tr>
                        <?php
endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($total_paid > 0): ?>
            <div style="margin-top: 20px; text-align: right; font-size: 1.25rem;">
                <strong>Cumulative Total: <span style="color: var(--primary);">₹<?php echo number_format($total_paid, 2); ?></span></strong>
            </div>
            <?php
endif; ?>
        </div>
        
        <div style="text-align: center; color: #94a3b8; font-size: 0.8rem; margin-top: 30px;">
            <p>© <?php echo date('Y'); ?> <?php echo $student['inst_name']; ?> | Managed by FeeFlow</p>
        </div>
    </div>

    <script>
    function copyToClipboard(text) {
        if (!navigator.clipboard) {
            const el = document.createElement('textarea');
            el.value = text;
            document.body.appendChild(el);
            el.select();
            document.execCommand('copy');
            document.body.removeChild(el);
            alert('Share link copied!');
            return;
        }
        navigator.clipboard.writeText(text).then(() => {
            alert('Ledger share link copied to clipboard!');
        });
    }
    </script>
</body>
</html>
