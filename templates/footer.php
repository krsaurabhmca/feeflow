<?php // templates/footer.php ?>
            </div>
        </main>
    </div>
    <!-- Confirmation Modal -->
    <div id="confirmModal" class="modal-overlay">
        <div class="glass-card modal-content fade-in" style="padding: 2rem; text-align: center;">
            <div style="width: 60px; height: 60px; background: rgba(239, 68, 68, 0.1); color: var(--danger); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; font-size: 1.5rem;">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h3 id="confirmTitle">Are you sure?</h3>
            <p id="confirmMessage" style="color: var(--secondary); margin-bottom: 2rem;">This action cannot be undone.</p>
            <div style="display: flex; gap: 1rem; justify-content: center;">
                <button id="confirmCancel" class="btn btn-secondary" style="flex: 1;">Cancel</button>
                <button id="confirmProceed" class="btn btn-primary" style="flex: 1; background: var(--danger);">Confirm</button>
            </div>
        </div>
    </div>

    <script src="assets/js/main.js"></script>
</body>
</html>

