<!-- Main content of the admin page ends here -->
    </main>
</div> <!-- closes .admin-wrapper -->

<!-- Load JS files -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<!-- A potential admin-specific JS file could go here -->
<!-- <script src="assets/js/admin.js"></script> -->

<script>
    // Basic setup for Toastr in admin
    if (typeof toastr !== 'undefined') {
        toastr.options = {
            "positionClass": "toast-top-right",
            "progressBar": true,
        };
    }
</script>

</body>
</html>
