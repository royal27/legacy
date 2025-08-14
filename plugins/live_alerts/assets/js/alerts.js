document.addEventListener('DOMContentLoaded', function() {
    const overlay = document.getElementById('live-alert-overlay');
    const contentDiv = document.getElementById('live-alert-content');
    const closeButton = document.getElementById('live-alert-close');
    let currentAlertId = null;

    function checkForAlert() {
        fetch('/api/alerts/check')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.alert) {
                    showAlert(data.alert);
                }
            });
    }

    function showAlert(alert) {
        currentAlertId = alert.id;
        contentDiv.innerHTML = alert.content;
        overlay.style.display = 'flex';
    }

    function closeAlert() {
        if (!currentAlertId) return;

        fetch(`/api/alerts/mark_read/${currentAlertId}`, { method: 'POST' })
            .then(() => {
                overlay.style.display = 'none';
                currentAlertId = null;
            });
    }

    if (overlay) {
        closeButton.addEventListener('click', closeAlert);
        checkForAlert();
    }

    // Admin-side logic for sending an alert
    document.body.addEventListener('click', function(e) {
        if (e.target.classList.contains('send-alert')) {
            e.preventDefault();
            const userId = e.target.dataset.userId;
            const content = prompt('Enter the alert message:');

            if (content) {
                const formData = new FormData();
                formData.append('user_id', userId);
                formData.append('content', content);

                fetch('/admin/alerts/send', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Alert sent successfully!');
                    } else {
                        alert('Failed to send alert: ' + (data.message || 'Unknown error'));
                    }
                });
            }
        }
    });
});
