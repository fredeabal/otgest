<div class="container">

    <!-- =================================================================================
    // Formulario de Edición de Justificación de Gasto
    // ================================================================================= -->
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-primary-subtle text-white">
                    <h5 class="mb-0 text-primary">Editar justificación de gasto</h5>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('expenses/update/' . $expense['id']) ?>" method="post" enctype="multipart/form-data" autocomplete="off">
                        <?= csrf_field() ?>

                        <!-- =================================================================================
                        // Fila: Motivo del gasto
                        // ================================================================================= -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="reason" class="form-label">Motivo del gasto</label>
                                <textarea class="form-control" id="reason" name="reason" rows="3"
                                    placeholder="Describe el motivo del gasto..." maxlength="100"><?= old('reason', $expense['reason']) ?></textarea>
                                <small class="form-text text-muted">Máximo 100 caracteres.</small>
                            </div>
                        </div>

                        <!-- =================================================================================
                        // Fila: Categoría
                        // ================================================================================= -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="category" class="form-label">Categoría</label>
                                <select class="form-select" id="category" name="category">
                                    <option value="">Selecciona una categoría</option>
                                    <?php $current_cat = old('category', $expense['category']); ?>
                                    <option value="Transporte" <?= $current_cat == 'Transporte' ? 'selected' : '' ?>>Transporte</option>
                                    <option value="Alimentación" <?= $current_cat == 'Alimentación' ? 'selected' : '' ?>>Alimentación</option>
                                    <option value="Materiales" <?= $current_cat == 'Materiales' ? 'selected' : '' ?>>Materiales</option>
                                    <option value="Tecnología" <?= $current_cat == 'Tecnología' ? 'selected' : '' ?>>Tecnología</option>
                                    <option value="Servicios" <?= $current_cat == 'Servicios' ? 'selected' : '' ?>>Servicios</option>
                                    <option value="Otros" <?= $current_cat == 'Otros' ? 'selected' : '' ?>>Otros</option>
                                </select>
                            </div>
                        </div>

                        <!-- =================================================================================
                        // Fila: Fecha del gasto y Monto
                        // ================================================================================= -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="expense_date" class="form-label">Fecha del gasto</label>
                                <div class="input-group">
                                    <?php $current_date = old('expense_date', $expense['expense_date']); ?>
                                    <input type="text" class="form-control" id="expense_date_display"
                                        value="<?= $current_date ? date('d/m/Y', strtotime($current_date)) : date('d/m/Y') ?>" placeholder="dd/mm/yyyy" autocomplete="off" readonly>
                                    <span class="input-group-text bg-transparent"><i class="ti ti-calendar fs-5"></i></span>
                                </div>
                                <input type="hidden" name="expense_date" id="expense_date" value="<?= $current_date ?: date('Y-m-d') ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="amount" class="form-label">Importe (€)</label>
                                <input type="number" class="form-control" id="amount" name="amount" step="0.01" min="0"
                                    value="<?= old('amount', $expense['amount']) ?>" placeholder="0.00">
                            </div>
                        </div>

                        <!-- =================================================================================
                        // Fila: Archivo
                        // ================================================================================= -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="receipt_image" class="form-label">Imagen del recibo/ticket (Opcional si ya existe)</label>
                                <input type="file" class="form-control" id="receipt_image" name="receipt_image"
                                    accept="image/*,.pdf">
                                <small class="form-text text-muted">Formatos permitidos: JPG, PNG, WebP, PDF. Tamaño máximo: 2MB. Si no subes uno nuevo, se mantendrá el actual.</small>
                                
                                <?php if ($expense['receipt_image']): ?>
                                    <div class="mt-2">
                                        <a href="<?= base_url('expenses/receipt/' . $expense['user_id'] . '/' . $expense['receipt_image']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">Ver recibo actual</a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- =================================================================================
                        // Botón de enviar
                        // ================================================================================= -->
                        <div class="d-grids pt-5 text-center">
                            <button type="submit" class="btn btn-primary">Actualizar solicitud</button>
                            <a href="<?= base_url('expenses/my-expenses') ?>" class="btn btn-dark ms-1">Volver atrás</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
