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
});
