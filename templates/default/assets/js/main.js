document.addEventListener('DOMContentLoaded', () => {
    // --- Fade In on Load ---
    document.body.classList.add('fade-in');

    // --- Toastr Configuration ---
    if (typeof toastr !== 'undefined') {
        toastr.options = {
            "closeButton": true,
            "newestOnTop": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "5000",
        };
    }

    // --- Fade Out on Navigation ---
    const links = document.querySelectorAll('a');
    links.forEach(link => {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href && !href.startsWith('#') && !href.startsWith('mailto:') && this.target !== '_blank') {
                e.preventDefault();
                document.body.classList.remove('fade-in');

                setTimeout(() => {
                    window.location.href = href;
                }, 500); // Match CSS transition duration
            }
        });
    });

    // --- AJAX Test Handler (using Fetch API for no dependencies) ---
    const ajaxButton = document.getElementById('ajax-test-button');
    if (ajaxButton) {
        ajaxButton.addEventListener('click', function() {
            fetch('includes/ajax_handler.php?action=get_server_time')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        toastr.success('Server time: ' + data.data.server_time, 'AJAX Success!');
                    } else {
                        toastr.error(data.message || 'Unknown error', 'AJAX Error');
                    }
                })
                .catch(error => {
                    toastr.error('Request failed: ' + error, 'AJAX Error');
                });
        });
    }
});

window.addEventListener('pageshow', function(event) {
    if (event.persisted) {
        document.body.classList.add('fade-in');
    }
});
