<?php
$page_title = 'Help & Documentation';
require_once 'templates/header.php';
?>

<div class="glass-card fade-in" style="padding: 2rem; max-width: 900px; margin: 0 auto;">
    <div style="text-align: center; margin-bottom: 3rem;">
        <i class="fas fa-circle-question" style="font-size: 3rem; color: var(--primary); margin-bottom: 1rem;"></i>
        <h2 style="margin-bottom: 0.5rem;">How to use FeeFlow</h2>
        <p style="color: var(--secondary);">Quick guide to master your institute's fee management system.</p>
    </div>

    <div class="grid-2" style="gap: 2rem;">
        <!-- Step 1 -->
        <div style="display: flex; gap: 1.5rem;">
            <div style="width: 40px; height: 40px; background: var(--primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-weight: 800;">1</div>
            <div>
                <h4 style="margin-bottom: 0.5rem;">Setup Classes</h4>
                <p style="font-size: 0.9rem; color: var(--secondary); line-height: 1.4;">Go to <strong>Classes</strong> and add your courses or levels (e.g., Class 10th, BCA, Web Development). This helps in organizing students properly.</p>
            </div>
        </div>

        <!-- Step 2 -->
        <div style="display: flex; gap: 1.5rem;">
            <div style="width: 40px; height: 40px; background: var(--primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-weight: 800;">2</div>
            <div>
                <h4 style="margin-bottom: 0.5rem;">Define Fee Types</h4>
                <p style="font-size: 0.9rem; color: var(--secondary); line-height: 1.4;">Use <strong>Fee Categories</strong> to create standard fee types with a default amount (e.g., Admission Fee: ₹5000). This saves time on every transaction.</p>
            </div>
        </div>

        <!-- Step 3 -->
        <div style="display: flex; gap: 1.5rem;">
            <div style="width: 40px; height: 40px; background: var(--primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-weight: 800;">3</div>
            <div>
                <h4 style="margin-bottom: 0.5rem;">Register Students</h4>
                <p style="font-size: 0.9rem; color: var(--secondary); line-height: 1.4;">In the <strong>Students</strong> tab, add your students. You can then generate <strong>Professional ID Cards</strong> with a scan-to-verify QR code.</p>
            </div>
        </div>

        <!-- Step 4 -->
        <div style="display: flex; gap: 1.5rem;">
            <div style="width: 40px; height: 40px; background: var(--primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-weight: 800;">4</div>
            <div>
                <h4 style="margin-bottom: 0.5rem;">Collect & Print</h4>
                <p style="font-size: 0.9rem; color: var(--secondary); line-height: 1.4;">Click <strong>Collect Fee</strong>, select a student, and generate a receipt. You can print in <strong>Full A4</strong> or <strong>2-in-1 Half A4</strong> layouts.</p>
            </div>
        </div>
    </div>

    <hr style="margin: 3rem 0; border: 0; border-top: 1px solid #e2e8f0;">

    <h3><i class="fas fa-star" style="color: #f59e0b;"></i> Key Features</h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem; margin-top: 1.5rem;">
        <div class="glass-card" style="padding: 1rem; background: var(--light);">
            <i class="fas fa-qrcode" style="color: var(--primary);"></i> <strong>Smart QR</strong>: Every receipt has a verification QR leading to a live digital student ledger.
        </div>
        <div class="glass-card" style="padding: 1rem; background: var(--light);">
            <i class="fas fa-signature" style="color: var(--primary);"></i> <strong>Digital Sign</strong>: Upload or draw your signature in Settings to manifest it on all receipts automatically.
        </div>
        <div class="glass-card" style="padding: 1rem; background: var(--light);">
            <i class="fas fa-palette" style="color: var(--primary);"></i> <strong>Branding</strong>: Change the app and receipt theme color in settings to match your institute's brand.
        </div>
        <div class="glass-card" style="padding: 1rem; background: var(--light);">
            <i class="fas fa-file-excel" style="color: var(--primary);"></i> <strong>Data Export</strong>: Export your entire transaction log to CSV for simplified accounting in Excel.
        </div>
    </div>

    <div style="margin-top: 3rem; background: var(--dark); color: white; padding: 1.5rem; border-radius: 1rem; text-align: center;">
        <h4 style="color: white; margin-bottom: 0.5rem;">Need Technical Support?</h4>
        <p style="font-size: 0.85rem; opacity: 0.8;">Contact your system administrator for further assistance regarding server setup or database queries.</p>
    </div>
</div>

<?php require_once 'templates/footer.php'; ?>
