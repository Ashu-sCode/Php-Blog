<?php
function set_flash($message, $type = 'info') {
    $_SESSION['message'] = $message;
    $_SESSION['message_type'] = $type;
}

function show_flash_toast() {
    if (!isset($_SESSION['message'])) return;

    $message = $_SESSION['message'];
    $type = $_SESSION['message_type'] ?? 'info';

    // Bootstrap toast color classes map
    $bgClass = [
        'success' => 'bg-success text-white',
        'danger' => 'bg-danger text-white',
        'warning' => 'bg-warning text-dark',
        'info' => 'bg-info text-white',
        'primary' => 'bg-primary text-white'
    ][$type] ?? 'bg-secondary text-white';

    echo <<<HTML
    <div aria-live="polite" aria-atomic="true" class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999">
        <div id="toastMessage" class="toast align-items-center $bgClass border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body fw-semibold">
                    {$message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <script>
        // Auto-dismiss toast after 4 seconds
        setTimeout(() => {
            const toastEl = document.getElementById('toastMessage');
            if (toastEl) {
                toastEl.classList.remove('show');
                toastEl.classList.add('hide');
                setTimeout(() => toastEl.remove(), 1000);
            }
        }, 4000);
    </script>
    HTML;

    unset($_SESSION['message'], $_SESSION['message_type']);
}

function render_flash() {
    show_flash_toast();
}