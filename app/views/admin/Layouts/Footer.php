        </section>
    </main>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const successAlerts = document.querySelectorAll('.admin-alert.success');

        successAlerts.forEach(function (alert) {
            setTimeout(function () {
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-8px)';
                alert.style.transition = '.35s ease';

                setTimeout(function () {
                    alert.remove();
                }, 400);
            }, 3500);
        });
    });
</script>
</body>
</html>