<?php
require_once 'includes/config.php';
if (!is_logged_in())
    redirect('login.php');

$fee_id = $_GET['id'] ?? null;
$layout = $_GET['layout'] ?? 'a4_full'; // a4_full or a4_half

if (!$fee_id)
    die("Invalid Request");

$stmt = $pdo->prepare("SELECT f.*, s.name as student_name, s.roll_no, s.parent_name, s.session, c.class_name, 
                      i.name as inst_name, i.address as inst_address, i.phone as inst_phone, i.logo as inst_logo,
                      i.recognition_text, i.affiliation_text,
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

$primary_color = $fee['receipt_color'] ?: '#003366'; // Defaulting to a deep academic blue if not set
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt #<?php echo $fee['receipt_no']; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;900&display=swap" rel="stylesheet">
    <style>
        :root { --primary: <?php echo $primary_color; ?>; }
        * { box-sizing: border-box; -webkit-print-color-adjust: exact; }
        body { font-family: 'Roboto', sans-serif; margin: 0; padding: 0; background: #f0f2f5; color: #333; }
        
        .no-print { 
            max-width: 800px; 
            margin: 20px auto; 
            background: white; 
            padding: 15px; 
            border-radius: 8px; 
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .page {
            width: 210mm;
            min-height: 297mm;
            padding: 10mm;
            margin: 10mm auto;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            position: relative;
        }

        .receipt-card {
            border: 1px solid #eee;
            height: 100%;
            padding: 20px;
            display: flex;
            flex-direction: column;
        }

        /* Header */
        .header {
            display: flex;
            align-items: center;
            text-align: center;
            margin-bottom: 20px;
        }
        .header-logo { width: 100px; height: 100px; object-fit: contain; }
        .header-text { flex: 1; padding: 0 20px; }
        .inst-name { color: var(--primary); font-size: 24px; font-weight: 900; margin: 0; text-transform: uppercase; }
        .inst-details { font-size: 14px; margin: 5px 0; font-weight: 500; }
        .inst-sub { font-size: 12px; color: #555; }

        /* Receipt Bar */
        .receipt-bar {
            display: grid;
            grid-template-columns: 1fr 1fr;
            background: var(--primary);
            color: white;
            padding: 8px 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-weight: 700;
        }

        /* Info Grid */
        .student-info {
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 10px;
            margin-bottom: 25px;
            border: 1.5px solid #e0e6ed;
            padding: 15px;
            border-radius: 6px;
        }
        .info-label { font-weight: 500; color: #444; }
        .info-value { border-bottom: 1.5px dotted #ccc; font-weight: 700; color: #000; padding-left: 10px; }

        /* Particulars Table */
        .particulars-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .particulars-table th {
            background: #f0f4f8;
            color: var(--primary);
            padding: 10px;
            border: 1.5px solid #d1d9e6;
            text-transform: uppercase;
            font-size: 12px;
        }
        .particulars-table td {
            padding: 12px 10px;
            border: 1.5px solid #d1d9e6;
            font-weight: 600;
        }
        .total-row td { background: #f8f9fa; font-weight: 800; }

        /* Footer Info */
        .footer-info {
            font-size: 14px;
            line-height: 1.8;
            margin-bottom: 30px;
        }
        .footer-info .line { display: flex; gap: 10px; margin-bottom: 8px; }
        .footer-info strong { min-width: 140px; }

        .words-box {
            border-top: 1.5px solid #eee;
            border-bottom: 1.5px solid #eee;
            padding: 10px 0;
            margin: 15px 0;
            font-weight: 700;
        }

        /* Final Footer */
        .final-footer {
            margin-top: auto;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            padding-top: 20px;
        }
        .qr-box { text-align: center; font-size: 10px; color: #666; }
        .qr-box img { width: 90px; height: 90px; margin-bottom: 5px; border: 1px solid #eee; }
        .signature-box { text-align: center; min-width: 180px; }
        .sig-image { max-height: 50px; margin-bottom: 5px; }
        .sig-line { border-top: 1.5px solid #333; padding-top: 5px; font-weight: 700; font-size: 13px; }

        .generated-tag { text-align: center; font-size: 11px; color: #999; margin-top: 20px; border-bottom: 1.5px dashed #eee; padding-bottom: 5px; }

        @media print {
            body { background: white; }
            .no-print { display: none; }
            .page { margin: 0; box-shadow: none; width: 100%; height: 100%; }
        }
        
        .btn { padding: 8px 16px; border-radius: 6px; cursor: pointer; font-weight: 600; text-decoration: none; border: none; font-size: 13px; }
        .btn-primary { background: var(--primary); color: white; }
        .btn-secondary { background: #e2e8f0; color: #475569; }
    </style>
</head>
<body>

    <div class="no-print">
        <div style="display: flex; gap: 10px; align-items: center;">
            <button onclick="window.print()" class="btn btn-primary">Download / Print Receipt</button>
            <a href="collect_fee.php" class="btn btn-secondary">New Collection</a>
            <a href="reports.php" class="btn btn-secondary">All Receipts</a>
        </div>
        <div style="font-size: 12px; color: #666;">
            <b>Tip:</b> Set layout to Portrait and remove headers/footers in print settings.
        </div>
    </div>

    <div class="page">
        <div class="receipt-card">
            <!-- Header Section -->
            <div class="header">
                <?php if ($fee['inst_logo']): ?>
                    <img src="assets/img/logos/<?php echo $fee['inst_logo']; ?>" class="header-logo">
                <?php
else: ?>
                    <div style="width: 100px; height: 100px; border: 1px solid #eee; display: grid; place-items: center; font-size: 10px;">LOGO</div>
                <?php
endif; ?>
                
                <div class="header-text">
                    <h1 class="inst-name"><?php echo $fee['inst_name']; ?></h1>
                    <p class="inst-details"><?php echo $fee['inst_address']; ?></p>
                    <p class="inst-sub">
                        <?php if ($fee['recognition_text'])
    echo $fee['recognition_text'] . "<br>"; ?>
                        <?php if ($fee['affiliation_text'])
    echo "<b>" . $fee['affiliation_text'] . "</b>"; ?>
                    </p>
                </div>
            </div>

            <hr style="border: 0; border-top: 2px solid var(--primary); margin: 0 0 15px 0;">

            <!-- Receipt Metadata Bar -->
            <div class="receipt-bar">
                <span>Receipt No : <?php echo $fee['receipt_no']; ?></span>
                <span style="text-align: right;">Date: <?php echo date('d-m-Y', strtotime($fee['payment_date'])); ?></span>
            </div>

            <!-- Student Detail Grid -->
            <div class="student-info">
                <span class="info-label">Student Name</span>
                <span class="info-value">: <?php echo strtoupper($fee['student_name']); ?></span>
                
                <span class="info-label">Father's Name</span>
                <span class="info-value">: <?php echo strtoupper($fee['parent_name']); ?></span>
                
                <span class="info-label">Course</span>
                <span class="info-value">: <?php echo strtoupper($fee['class_name']); ?></span>
                
                <span class="info-label">Session</span>
                <span class="info-value">: <?php echo strtoupper($fee['session'] ?: 'N/A'); ?></span>
                
                <span class="info-label">Admission No.</span>
                <span class="info-value">: <?php echo strtoupper($fee['roll_no'] ?: 'N/A'); ?></span>
            </div>

            <!-- Particulars Table -->
            <table class="particulars-table">
                <thead>
                    <tr>
                        <th style="width: 10%; text-align: center;">S.No</th>
                        <th style="text-align: left;">Particulars</th>
                        <th style="width: 25%; text-align: right;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="text-align: center;">1</td>
                        <td><?php echo $fee['category_name'] ?: $fee['custom_fee_name']; ?></td>
                        <td style="text-align: right;">₹<?php echo number_format($fee['amount'], 2); ?></td>
                    </tr>
                    <!-- Dynamic empty rows to fill space matching image feel -->
                    <tr style="height: 40px;"><td></td><td></td><td></td></tr>
                    <tr class="total-row">
                        <td colspan="2" style="text-align: right; text-transform: uppercase;">Total</td>
                        <td style="text-align: right;">₹<?php echo number_format($fee['amount'], 2); ?></td>
                    </tr>
                </tbody>
            </table>

            <!-- Payment Details Section -->
            <div class="footer-info">
                <div class="line">
                    <strong>Payment Mode</strong> : <?php echo $fee['payment_method']; ?>
                </div>
                <div class="line">
                    <strong>On Account Of</strong> : <?php echo $fee['category_name'] ?: $fee['custom_fee_name']; ?>
                </div>
                <div class="words-box">
                    Amount (in words) : <span style="text-transform: capitalize;"><?php echo amount_in_words($fee['amount']); ?> Only</span>
                </div>
            </div>

            <!-- Final Footer Section -->
            <div class="final-footer">
                <div class="qr-box">
                    <?php
$verify_data = BASE_URL . "student_ledger.php?id=" . $fee['student_id'];
$v_qr_url = "https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl=" . urlencode($verify_data);
?>
                    <img src="<?php echo $v_qr_url; ?>" alt="Verify QR">
                    <p>Scan to Verify Receipt</p>
                </div>

                <div class="signature-box">
                    <?php if ($fee['signature_data']): ?>
                        <img src="<?php echo $fee['signature_data']; ?>" class="sig-image">
                    <?php
else: ?>
                        <div style="height: 50px;"></div>
                    <?php
endif; ?>
                    <div class="sig-line">Authorized Signature</div>
                </div>
            </div>

            <div class="generated-tag">
                This is a Computer Generated Receipt
            </div>
        </div>
    </div>

</body>
</html>
