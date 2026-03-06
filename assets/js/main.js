// Main JS for UI interactions
document.addEventListener('DOMContentLoaded', () => {
    // Add fade-in effect to main units
    const content = document.querySelector('.content-body');
    if (content) {
        content.classList.add('fade-in');
    }

    // Auto-dismiss alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transition = 'opacity 0.5s ease';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });

    // Global Confirmation Modal Logic
    const confirmModal = document.getElementById('confirmModal');
    const confirmTitle = document.getElementById('confirmTitle');
    const confirmMessage = document.getElementById('confirmMessage');
    const confirmCancel = document.getElementById('confirmCancel');
    const confirmProceed = document.getElementById('confirmProceed');
    let pendingAction = null;

    window.showConfirm = (title, message, callback) => {
        confirmTitle.innerText = title || 'Are you sure?';
        confirmMessage.innerText = message || 'This action cannot be undone.';
        confirmModal.style.display = 'flex';
        pendingAction = callback;
    };

    const closeConfirm = () => {
        confirmModal.style.display = 'none';
        pendingAction = null;
    };

    confirmCancel.addEventListener('click', closeConfirm);

    confirmProceed.addEventListener('click', () => {
        if (pendingAction) pendingAction();
        closeConfirm();
    });

    // Intercept clicks on elements with data-confirm
    document.addEventListener('click', (e) => {
        const target = e.target.closest('[data-confirm]');
        if (target) {
            e.preventDefault();
            const message = target.getAttribute('data-confirm');
            const title = target.getAttribute('data-title') || 'Confirm Action';
            const url = target.getAttribute('href');

            showConfirm(title, message, () => {
                if (url) {
                    window.location.href = url;
                } else if (target.tagName === 'BUTTON' && target.type === 'submit') {
                    target.closest('form').submit();
                }
            });
        }
    });
});

