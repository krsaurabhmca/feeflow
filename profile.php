<?php
$page_title = 'Institute Settings';
require_once 'templates/header.php';

$institute_id = get_institute_id();
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $name = $_POST['name'];
    $inst_code = strtoupper(trim($_POST['inst_code']));
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $qr_link = $_POST['qr_payment_link'];
    $tnc = $_POST['tnc'];
    $receipt_color = $_POST['receipt_color'];
    $signature_data = $_POST['signature_data'];

    // Handle Logo Upload
    $logo_name = $_POST['old_logo'] ?? '';
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
        $target_dir = "assets/img/logos/";
        $file_ext = pathinfo($_FILES["logo"]["name"], PATHINFO_EXTENSION);
        $logo_name = "logo_" . $institute_id . "_" . time() . "." . $file_ext;
        if (!move_uploaded_file($_FILES["logo"]["tmp_name"], $target_dir . $logo_name)) {
            $error = "Failed to upload logo.";
        }
    }

    $stmt = $pdo->prepare("UPDATE institutes SET name = ?, inst_code = ?, phone = ?, address = ?, qr_payment_link = ?, logo = ?, tnc = ?, receipt_color = ?, signature_data = ? WHERE id = ?");
    if ($stmt->execute([$name, $inst_code, $phone, $address, $qr_link, $logo_name, $tnc, $receipt_color, $signature_data, $institute_id])) {
        $_SESSION['institute_name'] = $name;
        $message = "Settings updated successfully!";
    }
}

// Fetch Profile
$stmt = $pdo->prepare("SELECT * FROM institutes WHERE id = ?");
$stmt->execute([$institute_id]);
$profile = $stmt->fetch();
?>

<div class="fade-in">
    <?php if ($message): ?>
        <div class="alert alert-success"><?php echo $message; ?></div>
    <?php
endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php
endif; ?>

    <form action="profile.php" method="POST" enctype="multipart/form-data" id="profileForm">
        <div class="grid-2">
            <!-- Basic Info -->
            <div class="glass-card" style="padding: 2rem;">
                <h3><i class="fas fa-building"></i> General Info</h3>
                <div class="form-group">
                    <label>Institute Logo</label>
                    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                        <?php if ($profile['logo']): ?>
                            <img src="assets/img/logos/<?php echo $profile['logo']; ?>" style="width: 80px; height: 80px; object-fit: contain; border: 1px solid #e2e8f0; border-radius: 10px;">
                        <?php
else: ?>
                            <div style="width: 80px; height: 80px; background: #f1f5f9; display: grid; place-items: center; border-radius: 10px; border: 2px dashed #e2e8f0; color: #94a3b8;">No Logo</div>
                        <?php
endif; ?>
                        <input type="file" name="logo" class="form-control" accept="image/*">
                        <input type="hidden" name="old_logo" value="<?php echo $profile['logo']; ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label>Institute Name</label>
                    <input type="text" name="name" class="form-control" value="<?php echo $profile['name']; ?>" required>
                </div>
                <div class="form-group">
                    <label>Institute Code (Prefix for Receipts)</label>
                    <input type="text" name="inst_code" class="form-control" value="<?php echo $profile['inst_code']; ?>" placeholder="e.g. SKY">
                </div>
                <div class="form-group">
                    <label>Contact Phone</label>
                    <input type="text" name="phone" class="form-control" value="<?php echo $profile['phone']; ?>">
                </div>
                <div class="form-group">
                    <label>Physical Address</label>
                    <textarea name="address" class="form-control" rows="3"><?php echo $profile['address']; ?></textarea>
                </div>
            </div>

            <!-- Receipt & Signature -->
            <div class="glass-card" style="padding: 2rem;">
                <h3><i class="fas fa-file-invoice"></i> Receipt Settings</h3>
                <div class="form-group">
                    <label>Receipt Theme Color</label>
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <input type="color" name="receipt_color" class="form-control" style="width: 100px; height: 45px; padding: 2px;" value="<?php echo $profile['receipt_color'] ?: '#6366f1'; ?>">
                        <span style="font-size: 0.8rem; color: var(--secondary);">Color used in PDF & Prints</span>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Terms & Conditions (Shown on Receipt)</label>
                    <textarea name="tnc" class="form-control" rows="4" placeholder="1. Fees once paid are non-refundable..."><?php echo $profile['tnc']; ?></textarea>
                </div>

                <div class="form-group">
                    <label>Digital Signature (Accountant/Cashier)</label>
                    <canvas id="sig-canvas" class="signature-pad" width="400" height="150"></canvas>
                    <div style="display: flex; gap: 10px;">
                        <button type="button" class="btn btn-secondary" id="sig-clear-btn" style="padding: 0.5rem 1rem; font-size: 0.8rem;">Clear Signature</button>
                    </div>
                    <input type="hidden" name="signature_data" id="signature_data" value="<?php echo $profile['signature_data']; ?>">
                </div>
            </div>
            
            <!-- Payment Info -->
            <div class="glass-card" style="padding: 2rem; grid-column: 1 / -1;">
                <h3><i class="fas fa-money-check-dollar"></i> Payment Settings</h3>
                <div class="form-group">
                    <label>UPI ID (for Student QR Payments)</label>
                    <input type="text" name="qr_payment_link" class="form-control" value="<?php echo $profile['qr_payment_link']; ?>" placeholder="e.g. yourname@okaxis">
                </div>
            </div>
        </div>

        <div style="margin-top: 2rem;">
            <button type="submit" name="update_profile" class="btn btn-primary" style="width: auto; padding: 1rem 3rem;">
                <i class="fas fa-save"></i> Save All Settings
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const canvas = document.getElementById('sig-canvas');
    const ctx = canvas.getContext('2d');
    const clearBtn = document.getElementById('sig-clear-btn');
    const sigInput = document.getElementById('signature_data');
    let drawing = false;

    // Load existing signature
    if (sigInput.value) {
        const image = new Image();
        image.src = sigInput.value;
        image.onload = () => ctx.drawImage(image, 0, 0);
    }

    canvas.addEventListener('mousedown', startDrawing);
    canvas.addEventListener('mousemove', draw);
    canvas.addEventListener('mouseup', stopDrawing);
    canvas.addEventListener('mouseout', stopDrawing);

    // Touch support for mobile
    canvas.addEventListener('touchstart', (e) => {
        const touch = e.touches[0];
        const mouseEvent = new MouseEvent("mousedown", {
            clientX: touch.clientX,
            clientY: touch.clientY
        });
        canvas.dispatchEvent(mouseEvent);
        e.preventDefault();
    }, false);
    
    canvas.addEventListener('touchmove', (e) => {
        const touch = e.touches[0];
        const rect = canvas.getBoundingClientRect();
        const mouseEvent = new MouseEvent("mousemove", {
            clientX: touch.clientX,
            clientY: touch.clientY
        });
        canvas.dispatchEvent(mouseEvent);
        e.preventDefault();
    }, false);

    function getPos(e) {
        const rect = canvas.getBoundingClientRect();
        return {
            x: e.clientX - rect.left,
            y: e.clientY - rect.top
        };
    }

    function startDrawing(e) {
        drawing = true;
        const pos = getPos(e);
        ctx.beginPath();
        ctx.moveTo(pos.x, pos.y);
    }

    function draw(e) {
        if (!drawing) return;
        const pos = getPos(e);
        ctx.lineWidth = 2;
        ctx.lineCap = 'round';
        ctx.strokeStyle = '#000';
        ctx.lineTo(pos.x, pos.y);
        ctx.stroke();
    }

    function stopDrawing() {
        if (drawing) {
            drawing = false;
            sigInput.value = canvas.toDataURL();
        }
    }

    clearBtn.addEventListener('click', () => {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        sigInput.value = '';
    });
});
</script>

<?php require_once 'templates/footer.php'; ?>
