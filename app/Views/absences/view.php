<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary-subtle text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-primary">Detalles de Solicitud de Ausencia</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">


                        <!-- Aquí iba el modal de rechazo, eliminado a favor de SweetAlert -->



                        <?php if (!empty($absence['admin_comments'])): ?>
                        <div class="list-group-item px-0">
                            <div class="row">
                                <div class="col-sm-4"><strong>Comentarios del Administrador:</strong></div>
                                <div class="col-sm-8">
                                    <?= nl2br(esc($absence['admin_comments'])) ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="list-group-item px-0">
                            <div class="row">
                                <div class="col-sm-4"><strong>Solicitante:</strong></div>
                                <div class="col-sm-8">
                                    <strong><?= esc($absence['user_name']) ?></strong><br>
                                    <small class="text-muted">DNI: <?= esc($absence['user_identification']) ?></small>
                                </div>
                            </div>
                        </div>

                        <div class="list-group-item px-0">
                            <div class="row">
                                <div class="col-sm-4"><strong>Fecha de Solicitud:</strong></div>
                                <div class="col-sm-8">
                                    <?= esc(date('d/m/Y H:i', strtotime($absence['created_at']))) ?>
                                </div>
                            </div>
                        </div>

                        <div class="list-group-item px-0">
                            <div class="row">
                                <div class="col-sm-4"><strong>Tipo de Ausencia:</strong></div>
                                <div class="col-sm-8">
                                    <?= $absenceTypes[$absence['type']] ?? $absence['type'] ?>
                                </div>
                            </div>
                        </div>

                        <div class="list-group-item px-0">
                            <div class="row">
                                <div class="col-sm-4"><strong>Fecha de Inicio:</strong></div>
                                <div class="col-sm-8">
                                    <?= esc(date('d/m/Y', strtotime($absence['start_date']))) ?>
                                    <?php if ($absence['start_time']): ?>
                                    a las <?= esc(date('H:i', strtotime($absence['start_time']))) ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="list-group-item px-0">
                            <div class="row">
                                <div class="col-sm-4"><strong>Fecha de Fin:</strong></div>
                                <div class="col-sm-8">
                                    <?= esc(date('d/m/Y', strtotime($absence['end_date']))) ?>
                                    <?php if ($absence['end_time']): ?>
                                    a las <?= esc(date('H:i', strtotime($absence['end_time']))) ?>
                                    <?php endif; ?>
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
                                        'rejected' => 'danger',
                                        'cancelled' => 'secondary'
                                    ];
                                    ?>
                                    <span
                                        class="badge bg-<?= $statusClass[$absence['status']] ?? 'secondary' ?>-subtle text-<?= $statusClass[$absence['status']] ?? 'secondary' ?> fw-semibold border border-<?= $statusClass[$absence['status']] ?? 'secondary' ?> fs-2"
                                        style="min-width: 80px; display: inline-block;">
                                        <?= $statusLabels[$absence['status']] ?? $absence['status'] ?>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <?php if (!empty($absence['comments'])): ?>
                        <div class="list-group-item px-0">
                            <div class="row">
                                <div class="col-sm-4"><strong>Comentarios:</strong></div>
                                <div class="col-sm-8">
                                    <?= nl2br(esc($absence['comments'])) ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if ($absence['processed_by']): ?>
                        <div class="list-group-item px-0">
                            <div class="row">
                                <div class="col-sm-4"><strong>Procesado por:</strong></div>
                                <div class="col-sm-8">
                                    <?= esc($absence['processed_by_name'] ?? 'Usuario desconocido') ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if ($absence['updated_at'] && $absence['updated_at'] != $absence['created_at']): ?>
                        <div class="list-group-item px-0">
                            <div class="row">
                                <div class="col-sm-4"><strong>Última Actualización:</strong></div>
                                <div class="col-sm-8">
                                    <?= esc(date('d/m/Y H:i', strtotime($absence['updated_at']))) ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- =================================================================================
                            // Archivo Adjunto - Al final
                            // ================================================================================= -->
                        <div class="list-group-item px-0">
                            <div class="row">
                                <div class="col-sm-4"><strong>Archivo Adjunto:</strong></div>
                                <div class="col-sm-8">
                                    <?php if (!empty($absence['attachment'])): ?>
                                        <?php
                                        $fileExtension = strtolower(pathinfo($absence['attachment'], PATHINFO_EXTENSION));
                                        $isImage = in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                        ?>
                                        <?php if ($isImage): ?>
                                            <div class="text-start">
                                                <img src="<?= base_url('absences/download/' . $absence['id']) ?>" alt="Archivo adjunto"
                                                     class="img-fluid rounded border" style="max-height: 300px; cursor: pointer;"
                                                     onclick="window.open(this.src, '_blank')">
                                                <p class="mt-2">
                                                    <a class="btn btn-primary btn-sm" href="<?= base_url('absences/download/' . $absence['id']) ?>" target="_blank">
                                                        Descargar Archivo
                                                    </a>
                                                </p>
                                            </div>
                                        <?php else: ?>
                                            <div class="text-start">
                                                <a href="<?= base_url('absences/download/' . $absence['id']) ?>" class="btn btn-primary btn-sm" target="_blank">
                                                    Descargar Archivo
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <div class="text-start w-100">
                                            <p class="text-muted mb-0">No se ha adjuntado archivo</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- =================================================================================
                // Acciones (solo para administradores en ausencias pendientes)
                // ================================================================================= -->
                <?php if ($absence['status'] === 'pending' && (has_permission('absences.manage'))): ?>
                <div class="card-footer px-0">
                    <div class="row">
                        <div class="col-sm-12 text-center mt-3 mb-3">
                            <a href="<?= base_url('absences/approve/' . $absence['id']) ?>"
                                class="btn bg-success-subtle text-success me-2 approve-absence-swal"
                                data-url="<?= base_url('absences/approve/' . $absence['id']) ?>" data-title="aprobar">
                                Aprobar
                            </a>
                            <a href="javascript:void(0)" class="btn bg-danger-subtle text-danger me-2 reject-absence-swal" 
                               data-id="<?= $absence['id'] ?>">
                                Rechazar
                            </a>
                            <a href="javascript:void(0)" onclick="goBack()" class="btn btn-dark">
                                Atrás
                            </a>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <div class="card-footer px-0">
                    <div class="row">
                        <div class="col-sm-12 text-center py-3">
                            <a href="<?= base_url('absences/export-absence-pdf/' . $absence['id']) ?>" class="btn btn-primary me-2" target="_blank">
                                <i class="ti ti-file-description"></i> Exportar PDF
                            </a>
                            <a href="javascript:void(0)" onclick="goBack()" class="btn btn-dark ms-1">Atrás</a>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>