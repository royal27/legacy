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
            // Check if it's a valid, internal link and not a special link
            if (href && !href.startsWith('#') && !href.startsWith('mailto:') && !this.target) {
                e.preventDefault();
                document.body.classList.remove('fade-in'); // Start fade-out

                setTimeout(() => {
                    window.location.href = href;
                }, 500); // Must match CSS transition duration
            }
        });
    });

    // --- AJAX Test Handler ---
    const ajaxButton = document.getElementById('ajax-test-button');
    if (ajaxButton) {
        ajaxButton.addEventListener('click', function() {
            // Using fetch API as jQuery might not be loaded
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

// Handle browser back/forward buttons
window.addEventListener('pageshow', function(event) {
    if (event.persisted) {
        document.body.classList.add('fade-in');
    }
});
