        </div> <!-- End content -->

        <!-- Footer -->
        <footer class="sticky-footer bg-white">
            <div class="container my-auto">
                <div class="copyright text-center my-auto">
                    <span>Copyright &copy; Berkat Usaha <?= date('Y') ?> — Developed by Maya Maulina</span>
                </div>
            </div>
        </footer>
        <!-- End Footer -->

        </div> <!-- End content-wrapper -->
        </div> <!-- End wrapper -->

        <!-- Scroll to Top Button-->
        <a class="scroll-to-top rounded" href="#page-top">
            <i class="fas fa-angle-up"></i>
        </a>

        <!-- JS -->
        <script src="assets/vendor/jquery/jquery.min.js"></script>
        <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="assets/vendor/datatables/jquery.dataTables.min.js"></script>
        <script src="assets/vendor/datatables/dataTables.bootstrap4.min.js"></script>
        <script src="assets/vendor/jquery-easing/jquery.easing.min.js"></script>
        <script src="assets/js/sb-admin-2.min.js"></script>

        <script>
            $(document).ready(function() {
                $('#dataTable').DataTable({
                    "pageLength": 10,
                    "lengthMenu": [5, 10, 25, 50, 100],
                    // "language": {
                    //     "search": "Cari:",
                    //     "lengthMenu": "Tampilkan _MENU_ data",
                    //     "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    //     "paginate": {
                    //         "previous": "Sebelumnya",
                    //         "next": "Berikutnya"
                    //     }
                    // }
                });
            });
        </script>

        </body>

        </html>