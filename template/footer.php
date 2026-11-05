
<!-- Footer -->
<footer class="app-footer">
    <div class="float-end d-none d-sm-inline">
        v1.0
    </div>

    <strong>
        Copyright &copy; <?= date('Y') ?>
        Proyek Admin.
    </strong>

    All rights reserved.
</footer>
<!-- End Footer -->

</div>
<!-- End App Wrapper -->

<!-- Required JavaScript -->

<!-- OverlayScrollbars -->
<script
    src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.16.0/browser/overlayscrollbars.browser.es6.min.js"
    crossorigin="anonymous"
></script>

<!-- Popper -->
<script
    src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
    crossorigin="anonymous"
></script>

<!-- Bootstrap -->
<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js"
    crossorigin="anonymous"
></script>

<!-- AdminLTE JavaScript -->
<script src="<?= BASE_URL ?>assets/js/adminlte.js"></script>

<!-- Initialize OverlayScrollbars -->
<script>
    const SELECTOR_SIDEBAR_WRAPPER = ".sidebar-wrapper";
    const sidebarWrapper = document.querySelector(SELECTOR_SIDEBAR_WRAPPER);

    if (
        sidebarWrapper &&
        typeof OverlayScrollbarsGlobal !== "undefined" &&
        OverlayScrollbarsGlobal.OverlayScrollbars
    ) {
        OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
            scrollbars: {
                theme: "os-theme-light",
                autoHide: "leave",
                clickScroll: true
            }
        });
    }
</script>

</body>
</html>
