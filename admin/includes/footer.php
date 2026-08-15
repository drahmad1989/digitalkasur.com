<?php
/**
 * Admin Footer - DigitalKasur.com
 */
?>
        </div><!-- /.page-content -->

        <!-- Footer -->
        <div class="admin-footer">
            &copy; <?php echo date('Y'); ?> DigitalKasur.com - All Rights Reserved. | Version <?php echo APP_VERSION; ?>
        </div>
    </div><!-- /.main-content -->

    <!-- Back to Top Button -->
    <button class="back-to-top" id="backToTop" title="Back to Top">
        <i class="fas fa-chevron-up"></i>
    </button>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel"><i class="fas fa-exclamation-triangle text-danger me-2"></i>Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this item? This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteForm" method="POST" style="display:inline;">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <button type="submit" class="btn btn-danger"><i class="fas fa-trash me-1"></i>Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sidebar Toggle
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('show');
                sidebarOverlay.classList.toggle('show');
            });
        }

        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', function() {
                sidebar.classList.remove('show');
                sidebarOverlay.classList.remove('show');
            });
        }

        // Back to Top
        const backToTop = document.getElementById('backToTop');
        window.addEventListener('scroll', function() {
            if (window.scrollY > 300) {
                backToTop.classList.add('show');
            } else {
                backToTop.classList.remove('show');
            }
        });

        if (backToTop) {
            backToTop.addEventListener('click', function() {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        }

        // Delete Modal Handler
        function confirmDelete(formAction) {
            const deleteForm = document.getElementById('deleteForm');
            deleteForm.action = formAction;
            const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            modal.show();
        }

        // Toggle Switch Handler
        document.querySelectorAll('.toggle-ajax').forEach(function(toggle) {
            toggle.addEventListener('change', function() {
                const url = this.dataset.url;
                const field = this.dataset.field;
                const id = this.dataset.id;
                const value = this.checked ? 1 : 0;

                fetch(url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'action=toggle&field=' + field + '&id=' + id + '&value=' + value + '&csrf_token=<?php echo $_SESSION['csrf_token']; ?>'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success feedback
                        const row = this.closest('tr');
                        if (row) {
                            row.style.transition = 'background 0.3s';
                            row.style.background = 'rgba(16, 185, 129, 0.1)';
                            setTimeout(() => { row.style.background = ''; }, 1000);
                        }
                    } else {
                        this.checked = !this.checked;
                        alert('Error updating status. Please try again.');
                    }
                })
                .catch(() => {
                    this.checked = !this.checked;
                    alert('Network error. Please try again.');
                });
            });
        });

        // Auto-dismiss alerts
        document.querySelectorAll('.alert').forEach(function(alert) {
            setTimeout(function() {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 5000);
        });

        // Search form - auto submit on filter change
        document.querySelectorAll('.filter-auto-submit').forEach(function(el) {
            el.addEventListener('change', function() {
                this.closest('form').submit();
            });
        });
    </script>
</body>
</html>
