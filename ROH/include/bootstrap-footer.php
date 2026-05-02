<?php
/**
 * bootstrap-footer.php
 * Footer scripts - Bootstrap 5 + jQuery minimal
 */
?>

<!-- Bootstrap 5 JS Bundle (includes Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" 
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" 
        crossorigin="anonymous"></script>

<!-- Font Awesome (already in head, but ensuring) -->
<script src="https://kit.fontawesome.com/79aa4f4cb7.js" crossorigin="anonymous"></script>

<!-- Custom JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Enable all tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.forEach(function(tooltipTriggerEl) {
        new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Optional: Auto-dismiss alerts
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        setTimeout(() => {
            if (alert) alert.classList.add('fade');
        }, 5000);
    });
});
</script>

<!-- Optional: Google Analytics (consider privacy - move to consent-based loading) -->
<!-- 
<script async src="https://www.googletagmanager.com/gtag/js?id=G-CRBDFN0CZZ"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'G-CRBDFN0CZZ');
</script>
-->
