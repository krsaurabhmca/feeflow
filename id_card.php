<?php
require_once 'includes/config.php';
if (!is_logged_in())
    redirect('index.php');

$student_id = $_GET['id'] ?? null;
if (!$student_id)
    die("Invalid Request");

$stmt = $pdo->prepare("SELECT s.*, c.class_name, i.name as inst_name, i.logo as inst_logo, i.address as inst_address, i.receipt_color 
                      FROM students s 
                      JOIN institutes i ON s.institute_id = i.id
                      LEFT JOIN classes c ON s.class_id = c.id
                      WHERE s.id = ? AND s.institute_id = ?");
$stmt->execute([$student_id, get_institute_id()]);
$student = $stmt->fetch();

if (!$student)
    die("Student not found");

$qr_data = BASE_URL . "student_ledger.php?id=" . $student['id'];
$qr_url = "https://chart.googleapis.com/chart?chs=200x200&cht=qr&chl=" . urlencode($qr_data) . "&choe=UTF-8";
$theme_color = $student['receipt_color'] ?: '#dc2626';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ID Card - <?php echo $student['name']; ?></title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --p: <?php echo $theme_color; ?>; }
        body { background: #f1f5f9; display: flex; flex-direction: column; align-items: center; padding: 40px; font-family: 'Inter', sans-serif; margin: 0; }
        .no-print { margin-bottom: 30px; display: flex; gap: 10px; }
        .btn { padding: 10px 20px; border-radius: 8px; font-weight: 700; cursor: pointer; border: none; text-decoration: none; display: flex; align-items: center; gap: 8px; }
        .btn-primary { background: var(--p); color: white; }
        .btn-secondary { background: white; color: #333; border: 1px solid #ddd; }
        
        /* ID Card Professional Design */
        .id-card { 
            width: 320px; 
            height: 500px; 
            background: white; 
            border-radius: 20px; 
            overflow: hidden; 
            box-shadow: 0 15px 35px rgba(0,0,0,0.1); 
            position: relative; 
            border: 1px solid #eee;
        }
        .card-header { 
            height: 140px; 
            background: var(--p); 
            color: white; 
            padding: 20px; 
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .card-header img { max-height: 50px; margin-bottom: 8px; filter: brightness(0) invert(1); }
        .card-header h3 { margin: 0; font-size: 1.1rem; line-height: 1.2; font-weight: 800; }
        
        .photo-container { 
            width: 130px; 
            height: 130px; 
            margin: -65px auto 10px; 
            position: relative; 
            z-index: 2;
        }
        .photo-box { 
            width: 100%; 
            height: 100%; 
            background: white; 
            border-radius: 50%; 
            padding: 5px; 
            box-shadow: 0 5px 15px rgba(0,0,0,0.1); 
        }
        .photo-box img, .photo-box .placeholder { 
            width: 100%; 
            height: 100%; 
            border-radius: 50%; 
            object-fit: cover; 
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ccc;
            font-size: 3rem;
        }

        .student-details { text-align: center; padding: 0 20px; margin-top: 5px; }
        .student-details h2 { margin: 0; color: #0f172a; font-size: 1.4rem; font-weight: 800; }
        .student-details p.class { color: var(--p); font-weight: 700; font-size: 0.9rem; margin: 4px 0 15px; }
        
        .info-grid { 
            display: grid; 
            grid-template-columns: 1fr; 
            gap: 8px; 
            text-align: left; 
            padding: 0 30px;
            font-size: 0.85rem;
        }
        .info-row { display: flex; justify-content: space-between; border-bottom: 1px solid #f1f5f9; padding-bottom: 4px; }
        .info-row strong { color: #64748b; font-weight: 600; }
        .info-row span { color: #1e293b; font-weight: 700; }

        .qr-section { margin-top: 25px; text-align: center; }
        .qr-section img { width: 90px; border: 1px solid #eee; border-radius: 12px; padding: 5px; background: white; }
        
        .card-footer { 
            position: absolute; 
            bottom: 0; 
            width: 100%; 
            background: #0f172a; 
            color: white; 
            padding: 12px; 
            text-align: center; 
            font-size: 0.7rem; 
            font-weight: 600;
        }
        
        @media print { .no-print { display: none; } body { padding: 0; background: white; } }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="downloadID('png')" class="btn btn-secondary"><i class="fas fa-image"></i> Download PNG</button>
        <button onclick="window.print()" class="btn btn-primary"><i class="fas fa-print"></i> Print ID Card</button>
    </div>

    <div id="capture" class="id-card">
        <div class="card-header">
            <?php if ($student['inst_logo']): ?>
                <img src="assets/img/logos/<?php echo $student['inst_logo']; ?>">
            <?php
endif; ?>
            <h3><?php echo $student['inst_name']; ?></h3>
        </div>
        
        <div class="photo-container">
            <div class="photo-box">
                <?php if ($student['profile_image']): ?>
                    <img src="assets/img/students/<?php echo $student['profile_image']; ?>">
                <?php
else: ?>
                    <div class="placeholder"><i class="fas fa-user-graduate"></i></div>
                <?php
endif; ?>
            </div>
        </div>

        <div class="student-details">
            <h2><?php echo $student['name']; ?></h2>
            <p class="class"><?php echo $student['class_name'] ?: 'Student'; ?></p>
        </div>

        <div class="info-grid">
            <div class="info-row"><strong>Roll No</strong> <span><?php echo $student['roll_no'] ?: '-'; ?></span></div>
            <div class="info-row"><strong>Guardian</strong> <span><?php echo $student['parent_name'] ?: '-'; ?></span></div>
            <div class="info-row"><strong>Phone</strong> <span><?php echo $student['phone'] ?: '-'; ?></span></div>
            <div class="info-row"><strong>Validity</strong> <span>2025-2026</span></div>
        </div>

        <div class="qr-section">
            <img src="<?php echo $qr_url; ?>" alt="QR Code">
            <div style="font-size: 0.6rem; color: #94a3b8; margin-top: 4px; font-weight: 700;">SCAN TO VERIFY / PAY FEE</div>
        </div>

        <div class="card-footer">
            <i class="fas fa-shield-halved"></i> SECURE DIGITAL STUDENT ID
        </div>
    </div>

    <script>
    function downloadID(format) {
        const captureElement = document.querySelector("#capture");
        html2canvas(captureElement, {
            scale: 3, // High quality
            useCORS: true,
            allowTaint: true
        }).then(canvas => {
            const link = document.createElement("a");
            link.download = "ID_Card_<?php echo str_replace(' ', '_', $student['name']); ?>." + format;
            link.href = canvas.toDataURL("image/png");
            link.click();
        });
    }
    </script>
</body>
</html>
