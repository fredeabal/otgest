<div class="container">

    <!-- =================================================================================
    // Detalle de Justificación de Gasto
    // ================================================================================= -->
    <div class="card">
        <div class="card-header bg-primary-subtle text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-primary">Detalle de Justificación de Gasto</h5>
        </div>
        <div class="card-body">

            <div class="card-body">
                <div class="list-group list-group-flush">
                    <div class="list-group-item px-0">
                        <div class="row">
                            <div class="col-sm-4"><strong>Solicitante:</strong></div>
                            <div class="col-sm-8">
                                <strong><?= esc($expense['user_name']) ?></strong><br>
                                <small class="text-muted">DNI: <?= esc($expense['user_identification']) ?></small>
                            </div>
                        </div>
                    </div>

                    <div class="list-group-item px-0">
                        <div class="row">
                            <div class="col-sm-4"><strong>Fecha de solicitud:</strong></div>
                            <div class="col-sm-8">
                                <?= esc(date('d/m/Y H:i', strtotime($expense['created_at']))) ?>
                            </div>
                        </div>
                    </div>

                    <div class="list-group-item px-0">
                        <div class="row">
                            <div class="col-sm-4"><strong>Fecha del gasto:</strong></div>
                            <div class="col-sm-8">
                                <?= esc(date('d/m/Y', strtotime($expense['expense_date']))) ?>
                            </div>
                        </div>
                    </div>

                    <div class="list-group-item px-0">
                        <div class="row">
                            <div class="col-sm-4"><strong>Importe:</strong></div>
                            <div class="col-sm-8">
                                <?php if ($expense['amount']): ?>
                                <strong class="text-muted h6"><?= number_format($expense['amount'], 2, ',', '.') ?>
                                    €</strong>
                                <?php else: ?>
                                <span class="text-muted">No especificado</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="list-group-item px-0">
                        <div class="row">
                            <div class="col-sm-4"><strong>Categoría:</strong></div>
                            <div class="col-sm-8">
                                <?= esc($expense['category'] ?: 'No especificada') ?>
                            </div>
                        </div>
                    </div>

                    <div class="list-group-item px-0">
                        <div class="row">
                            <div class="col-sm-4"><strong>Estado:</strong></div>
                            <div class="col-sm-8">
                                <?php
                                    $statusClass = [
                                        'pending' => 'warning',
                                        'approved' => 'success',
                                        'rejected' => 'danger'
                                    ];
                                    $statusText = [
                                        'pending' => 'Pendiente',
                                        'approved' => 'Aprobado',
                                        'rejected' => 'Rechazado'
                                    ];
                                    ?>
                                <span
                                    class="badge bg-<?= $statusClass[$expense['status']] ?? 'secondary' ?>-subtle text-<?= $statusClass[$expense['status']] ?? 'secondary' ?> fw-semibold border border-<?= $statusClass[$expense['status']] ?? 'secondary' ?> fs-2"
                                    style="min-width: 80px; display: inline-block;">
                                    <?= $statusText[$expense['status']] ?? $expense['status'] ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="list-group-item px-0">
                        <div class="row">
                            <div class="col-sm-4"><strong>Motivo:</strong></div>
                            <div class="col-sm-8">
                                <?= nl2br(esc($expense['reason'])) ?>
                            </div>
                        </div>
                    </div>

                    <?php if ($expense['approved_by']): ?>
                    <div class="list-group-item px-0">
                        <div class="row">
                            <div class="col-sm-4"><strong>Aprobado por:</strong></div>
                            <div class="col-sm-8">
                                <strong><?= esc($expense['approver_name']) ?></strong>
                            </div>
                        </div>
                    </div>

                    <div class="list-group-item px-0">
                        <div class="row">
                            <div class="col-sm-4"><strong>Fecha de aprobación:</strong></div>
                            <div class="col-sm-8">
                                <?= esc(date('d/m/Y H:i', strtotime($expense['approved_at']))) ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- =================================================================================
                       // Archivo Adjunto
                       // ================================================================================= -->
                    <div class="list-group-item px-0">
                        <div class="row">
                            <div class="col-sm-4"><strong>Recibo/Ticket:</strong></div>
                            <div class="col-sm-8">
                                <?php if (!empty($expense['receipt_image'])): ?>
                                <?php
                                   $fileExtension = strtolower(pathinfo($expense['receipt_image'], PATHINFO_EXTENSION));
                                   $isImage = in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                   ?>
                                <?php if ($isImage): ?>
                                <div class="text-start">
                                    <img src="<?= base_url('expenses/receipt/' . $expense['user_id'] . '/' . $expense['receipt_image']) ?>"
                                        alt="Recibo" class="img-fluid rounded border"
                                        style="max-height: 300px; cursor: pointer;"
                                        onclick="window.open(this.src, '_blank')">
                                    <p class="mt-2">
                                        <a href="<?= base_url('expenses/receipt/' . $expense['user_id'] . '/' . $expense['receipt_image']) ?>"
                                            target="_blank" class="btn btn-primary btn-sm">
                                            Descargar Archivo
                                        </a>
                                    </p>
                                </div>
                                <?php else: ?>
                                <div class="text-start">
                                    <a href="<?= base_url('expenses/receipt/' . $expense['user_id'] . '/' . $expense['receipt_image']) ?>"
                                        class="btn btn-primary btn-sm" target="_blank">
                                        Descargar Archivo
                                    </a>
                                </div>
                                <?php endif; ?>
                                <?php else: ?>
                                <div class="text-center w-100">
                                    <i class="ti ti-file-description text-muted mb-2" style="font-size: 2rem;"></i>
                                    <p class="text-muted mb-0">No se ha adjuntado archivo del recibo</p>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>

                <!-- =================================================================================
                // Motivo de Rechazo (Opcional)
                // ================================================================================= -->
                <?php if ($expense['status'] === 'rejected' && !empty($expense['rejection_reason'])): ?>
                <div class="alert alert-danger mt-4 border-0 mb-0 d-flex align-items-center gap-3">
                    <div class="bg-danger-subtle rounded-circle p-2 d-inline-flex">
                        <i class="ti ti-info-circle text-danger fs-5"></i>
                    </div>
                    <div>
                        <h6 class="mb-1 fw-bold text-danger">Motivo de rechazo</h6>
                        <p class="mb-0 text-dark"><?= nl2br(esc($expense['rejection_reason'])) ?></p>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- =================================================================================
                // Acciones
                // ================================================================================= -->
            <div class="card-footer px-0 bg-transparent mt-4 border-top-0">
                <div class="row">
                    <div class="col-sm-12 text-center mb-3">
                        <?php if ($expense['status'] === 'pending' && (has_permission('expenses.manage'))): ?>
                        <a href="<?= base_url('expenses/approve/' . $expense['id']) ?>"
                            class="btn bg-success-subtle text-success me-2 mb-2 approve-expense-swal"
                            data-url="<?= base_url('expenses/approve/' . $expense['id']) ?>" data-title="aprobar">
                            Aprobar
                        </a>
                        <a href="<?= base_url('expenses/reject/' . $expense['id']) ?>"
                            class="btn bg-danger-subtle text-danger me-2 mb-2 reject-expense-swal"
                            data-url="<?= base_url('expenses/reject/' . $expense['id']) ?>" data-title="rechazar">
                            Rechazar
                        </a>
                        <?php endif; ?>
                        <a href="javascript:void(0)" onclick="goBack()" class="btn btn-dark mb-2">
                            Atrás
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// =================================================================================
// Confirmación de aprobación/rechazo con SweetAlert
// =================================================================================
document.querySelectorAll('.approve-expense-swal, .reject-expense-swal').forEach(function(link) {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        const url = link.dataset.url;
        const action = link.dataset.title;
        const isApprove = link.classList.contains('approve-expense-swal');

        let swalConfig = {
            title: `Confirmar ${action}`,
            text: `¿Estás seguro de ${action} este gasto?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: isApprove ? '#28a745' : '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: isApprove ? 'Sí, aprobar' : 'Sí, rechazar',
            cancelButtonText: 'Cancelar'
        };

        if (!isApprove) {
            swalConfig.input = 'textarea';
            swalConfig.inputPlaceholder = 'Escribe el motivo del rechazo (opcional pero recomendado)...';
            swalConfig.inputAttributes = {
                'aria-label': 'Motivo del rechazo'
            };
        }

        Swal.fire(swalConfig).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = url;
                const csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '<?= csrf_token() ?>';
                csrf.value = '<?= csrf_hash() ?>';
                form.appendChild(csrf);

                if (!isApprove && result.value) {
                    const reasonInput = document.createElement('input');
                    reasonInput.type = 'hidden';
                    reasonInput.name = 'rejection_reason';
                    reasonInput.value = result.value;
                    form.appendChild(reasonInput);
                }

                document.body.appendChild(form);
                form.submit();
            }
        });
    });
});

// Función para volver atrás
function goBack() {
    window.history.back();
}

</script>