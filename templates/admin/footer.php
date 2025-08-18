<!-- Page-specific content ends here -->
                </div> <!-- .admin-page-content -->
            </main>
        </div> <!-- .main-panel -->
    </div> <!-- .admin-wrapper -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="/js/admin.js"></script>
    <script>
        // Configure Toastr for Admin Panel
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "4000"
        };

        // Fade-in effect
        window.addEventListener('load', () => {
            document.body.style.opacity = 1;
        });
    </script>
</body>
</html>
