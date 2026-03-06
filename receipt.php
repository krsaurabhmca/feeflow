<?php
require_once 'includes/config.php';
if (!is_logged_in())
    redirect('index.php');

$fee_id = $_GET['id'] ?? null;
$layout = $_GET['layout'] ?? 'a4_full'; // a4_full or a4_half

if (!$fee_id)
    die("Invalid Request");

$stmt = $pdo->prepare("SELECT f.*, s.name as student_name, s.roll_no, c.class_name, 
                      i.name as inst_name, i.address as inst_address, i.phone as inst_phone, i.logo as inst_logo,
                      i.receipt_color, i.tnc, i.signature_data, fc.category_name, i.qr_payment_link as inst_upi 
                      FROM fees f 
                      JOIN students s ON f.student_id = s.id 
                      JOIN institutes i ON f.institute_id = i.id
                      LEFT JOIN classes c ON s.class_id = c.id
                      LEFT JOIN fee_categories fc ON f.fee_category_id = fc.id
                      WHERE f.id = ? AND f.institute_id = ?");
$stmt->execute([$fee_id, get_institute_id()]);
$fee = $stmt->fetch();

if (!$fee)
    die("Receipt not found");

$primary_color = $fee['receipt_color'] ?: '#6366f1';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt #<?php echo $fee['receipt_no']; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --p: <?php echo $primary_color; ?>; }
        body { font-family: 'Inter', sans-serif; padding: 20px; color: #1e293b; background: #f1f5f9; margin: 0; }
        .no-print { 
            max-width: 210mm; 
            margin: 0 auto 20px auto; 
            background: white; 
            padding: 15px; 
            border-radius: 12px; 
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        .receipt-container { 
            background: white; 
            margin: auto; 
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
            position: relative;
            overflow: hidden;
        }
        
        /* Layouts */
        .a4-full { width: 210mm; min-height: 297mm; padding: 60px; }
        .a4-half { width: 210mm; height: 144mm; padding: 30px 40px; margin-bottom: 5mm; border-bottom: 1px dashed #ccc; }
        
        .a4-half .header { margin-bottom: 20px; padding-bottom: 15px; }
        .a4-half .info-grid { margin-bottom: 20px; gap: 20px; }
        .a4-half .table { margin-bottom: 20px; }
        .a4-half .footer-grid { margin-top: 20px; gap: 30px; }
        
        .header { display: flex; justify-content: space-between; border-bottom: 3px solid var(--p); padding-bottom: 25px; margin-bottom: 30px; gap: 20px; }
        .logo-box { flex: 1; max-width: 70%; } /* Added flex to handle long names */
        .logo-box img { max-height: 80px; margin-bottom: 10px; }
        .inst-name { font-size: 1.5rem; font-weight: 800; color: var(--p); margin: 0; line-height: 1.2; }
        .receipt-title { font-size: 1.4rem; font-weight: 700; margin: 0; text-transform: uppercase; letter-spacing: 1px; color: #64748b; }
        
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-bottom: 40px; }
        .info-item { margin-bottom: 8px; font-size: 1rem; }
        .info-item strong { color: #64748b; font-size: 0.85rem; text-transform: uppercase; display: block; }
        
        .table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .table th { background: var(--p); color: white; padding: 15px; text-align: left; }
        .table td { padding: 15px; border-bottom: 1px solid #e2e8f0; }
        .total-box { background: #f8fafc; padding: 20px; text-align: right; border-radius: 8px; border-left: 5px solid var(--p); }
        .total-box h2 { margin: 0; color: var(--p); }

        .footer-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 50px; margin-top: 50px; }
        .tnc { font-size: 0.8rem; color: #64748b; }
        .signature-box { text-align: center; }
        .signature-img { max-width: 150px; border-bottom: 1px solid #333; margin-bottom: 5px; }
        
        @media print {
            body { background: white; padding: 0; }
            .no-print { display: none; }
            .receipt-container { box-shadow: none; }
            .a4-full { padding: 20px; width: 210mm; } 
            .a4-half { width: 210mm; height: 148.5mm; }
        }
        
        @media (max-width: 768px) {
            body { padding: 10px; }
            .no-print { width: 100%; padding: 10px; gap: 10px; }
            .receipt-container { width: 100% !important; height: auto !important; min-height: 0 !important; padding: 20px !important; }
            .info-grid { grid-template-columns: 1fr; gap: 20px; }
            .header { flex-direction: column; text-align: left; }
            .header div:nth-child(2) { text-align: left !important; margin-top: 15px; }
            .logo-box { max-width: 100%; }
            .footer-grid { grid-template-columns: 1fr; gap: 30px; }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <div style="display: flex; gap: 10px; align-items: center;">
            <span style="font-weight: 700; color: var(--secondary); font-size: 0.8rem; text-transform: uppercase;">Print Layout:</span>
            <div style="background: #f1f5f9; padding: 4px; border-radius: 8px;">
                <a href="receipt.php?id=<?php echo $fee_id; ?>&layout=a4_full" class="btn <?php echo $layout == 'a4_full' ? 'btn-primary' : ''; ?>" style="padding: 6px 12px; font-size: 0.8rem; border-radius: 6px;">Full A4</a>
                <a href="receipt.php?id=<?php echo $fee_id; ?>&layout=a4_half" class="btn <?php echo $layout == 'a4_half' ? 'btn-primary' : ''; ?>" style="padding: 6px 12px; font-size: 0.8rem; border-radius: 6px;">2-in-1 A4</a>
            </div>
        </div>
        <div style="display: flex; gap: 10px;">
            <button onclick="window.print()" class="btn btn-primary"><i class="fas fa-print"></i> Print Now</button>
            <a href="reports.php" class="btn btn-secondary"><i class="fas fa-list-check"></i> All Receipts</a>
            <a href="collect_fee.php" class="btn btn-secondary"><i class="fas fa-plus"></i> New Fee</a>
        </div>
    </div>

    <?php
// If half layout, we show it twice
$iterations = ($layout == 'a4_half') ? 2 : 1;
for ($i = 0; $i < $iterations; $i++):
?>
    <div class="receipt-container <?php echo($layout == 'a4_half') ? 'a4-half' : 'a4-full'; ?>">
        <div class="header">
            <div class="logo-box">
                <?php if ($fee['inst_logo']): ?>
                    <img src="assets/img/logos/<?php echo $fee['inst_logo']; ?>">
                <?php
    endif; ?>
                <h1 class="inst-name"><?php echo $fee['inst_name']; ?></h1>
                <p style="margin: 5px 0; font-size: 0.85rem; color: #64748b;"><?php echo $fee['inst_address']; ?><br>Contact: <?php echo $fee['inst_phone']; ?></p>
            </div>
            <div style="text-align: right;">
                <h2 class="receipt-title">Payment Receipt</h2>
                <div style="margin-top: 15px; font-size: 0.9rem;">
                    <strong>Receipt No:</strong> #<?php echo $fee['receipt_no']; ?><br>
                    <strong>Date:</strong> <?php echo date('d M, Y', strtotime($fee['payment_date'])); ?>
                </div>
            </div>
        </div>

        <div class="info-grid">
            <div class="glass-card" style="padding: 20px; background: #fbfbfb;">
                <div class="info-item"><strong>Student Name</strong> <?php echo $fee['student_name']; ?></div>
                <div class="info-item"><strong>Roll Number</strong> <?php echo $fee['roll_no']; ?></div>
                <div class="info-item"><strong>Class/Course</strong> <?php echo $fee['class_name']; ?></div>
            </div>
            <div class="glass-card" style="padding: 20px; background: #fbfbfb;">
                <div class="info-item"><strong>Payment Mode</strong> <?php echo $fee['payment_method']; ?></div>
                <div class="info-item"><strong>Status</strong> <span style="color: #10b981; font-weight: 700;">● PAID</span></div>
                <?php if ($fee['remarks']): ?>
                    <div class="info-item"><strong>Remarks</strong> <?php echo $fee['remarks']; ?></div>
                <?php
    endif; ?>
            </div>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>Particulars / Fee Description</th>
                    <th style="text-align: right; width: 150px;">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="font-weight: 600; font-size: 1.1rem;"><?php echo $fee['category_name'] ?: $fee['custom_fee_name']; ?></td>
                    <td style="text-align: right; font-weight: 700; font-size: 1.1rem;">₹<?php echo number_format($fee['amount'], 2); ?></td>
                </tr>
            </tbody>
        </table>

        <div class="total-box">
            <p style="margin-bottom: 5px; font-weight: 600; color: #64748b;">Total Amount Paid</p>
            <h2>INR <?php echo number_format($fee['amount'], 2); ?></h2>
        </div>
        
        <div style="margin-top: 10px; font-size: 0.85rem; color: #1e293b; border: 1px dashed #e2e8f0; padding: 10px; border-radius: 8px;">
            <strong>Amount in Words:</strong> 
            <span style="text-transform: capitalize;"><?php echo amount_in_words($fee['amount']); ?> only.</span>
        </div>

        <div class="footer-grid">
            <div class="tnc">
                <strong>Terms & Conditions:</strong><br>
                <?php echo nl2br($fee['tnc'] ?: "1. This is a computer generated receipt.\n2. Fees once paid are non-refundable."); ?>
                
                <!-- Verification QR -->
                <?php
    $verify_data = BASE_URL . "student_ledger.php?id=" . $fee['student_id'];
    $v_qr_url = "https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl=" . urlencode($verify_data);
?>
                <div style="display: flex; align-items: center; gap: 10px; margin-top: 15px; padding: 10px; background: #f8fafc; border-radius: 8px; width: fit-content;">
                    <img src="<?php echo $v_qr_url; ?>" width="55" alt="Verify QR">
                    <div>
                        <strong style="font-size: 0.7rem; color: var(--dark);">VERIFY RECEIPT</strong><br>
                        <small style="font-size: 0.6rem; color: var(--secondary);">Scan to view digital ledger</small>
                    </div>
                </div>

                <!-- Payment QR (Optional) -->
                <?php if ($fee['inst_upi']):
        $upi_id = trim($fee['inst_upi']);
        $upi_link = "upi://pay?pa=" . $upi_id . "&pn=" . urlencode($fee['inst_name']) . "&tn=" . urlencode("Fees for " . $fee['student_name']);
        $p_qr_url = "https://chart.googleapis.com/chart?chs=200x200&cht=qr&chl=" . urlencode($upi_link);
?>
                <div style="display: flex; align-items: center; gap: 10px; margin-top: 10px; padding: 10px; border: 1px dashed var(--p); border-radius: 8px; width: fit-content;">
                    <img src="<?php echo $p_qr_url; ?>" width="55" alt="Payment QR">
                    <div>
                        <strong style="font-size: 0.7rem; color: var(--p);">PAYMENT SCANNER</strong><br>
                        <small style="font-size: 0.6rem; color: var(--secondary);"><?php echo $upi_id; ?></small>
                    </div>
                </div>
                <?php
    endif; ?>
            </div>
            <div class="signature-box">
                <?php if ($fee['signature_data']): ?>
                    <img src="<?php echo $fee['signature_data']; ?>" class="signature-img">
                <?php
    else: ?>
                    <div style="height: 60px;"></div>
                <?php
    endif; ?>
                <div style="font-weight: 700; font-size: 0.9rem;">AUTHORIZED SIGNATORY</div>
                <div style="font-size: 0.75rem; color: #64748b;">(Accountant/Cashier)</div>
            </div>
        </div>

        <?php if ($layout == 'a4_half' && $i == 0): ?>
            <!-- Cut Line -->
            <div style="position: absolute; bottom: -10px; left: 0; width: 100%; text-align: center; color: #ccc; font-size: 0.7rem;">✂ - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - ✂</div>
        <?php
    endif; ?>
    </div>
    <?php
endfor; ?>
</body>
</html>
