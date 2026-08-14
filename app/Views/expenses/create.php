<div class="container">

    <!-- =================================================================================
    // Formulario de Solicitud de Justificación de Gasto
    // ================================================================================= -->
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-primary-subtle text-white">
                    <h5 class="mb-0 text-primary">Solicitar justificación de gasto</h5>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('expenses/store') ?>" method="post" enctype="multipart/form-data" autocomplete="off">
                        <?= csrf_field() ?>

                        <!-- =================================================================================
                        // Fila: Motivo del gasto
                        // ================================================================================= -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="reason" class="form-label">Motivo del gasto</label>
                                <textarea class="form-control" id="reason" name="reason" rows="3"
                                    placeholder="Describe el motivo del gasto..." maxlength="100"><?= old('reason') ?></textarea>
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
                                    <option value="Transporte" <?= old('category') == 'Transporte' ? 'selected' : '' ?>>Transporte</option>
                                    <option value="Alimentación" <?= old('category') == 'Alimentación' ? 'selected' : '' ?>>Alimentación</option>
                                    <option value="Materiales" <?= old('category') == 'Materiales' ? 'selected' : '' ?>>Materiales</option>
                                    <option value="Tecnología" <?= old('category') == 'Tecnología' ? 'selected' : '' ?>>Tecnología</option>
                                    <option value="Servicios" <?= old('category') == 'Servicios' ? 'selected' : '' ?>>Servicios</option>
                                    <option value="Otros" <?= old('category') == 'Otros' ? 'selected' : '' ?>>Otros</option>
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
                                    <input type="text" class="form-control" id="expense_date_display"
                                        value="<?= old('expense_date') ? date('d/m/Y', strtotime(old('expense_date'))) : date('d/m/Y') ?>" placeholder="dd/mm/yyyy" autocomplete="off" readonly>
                                    <span class="input-group-text bg-transparent"><i class="ti ti-calendar fs-5"></i></span>
                                </div>
                                <input type="hidden" name="expense_date" id="expense_date" value="<?= old('expense_date', date('Y-m-d')) ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="amount" class="form-label">Importe (€)</label>
                                <input type="number" class="form-control" id="amount" name="amount" step="0.01" min="0"
                                    value="<?= old('amount') ?>" placeholder="0.00">
                            </div>
                        </div>

                        <!-- =================================================================================
                        // Fila: Archivo
                        // ================================================================================= -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="receipt_image" class="form-label">Imagen del recibo/ticket</label>
                                <input type="file" class="form-control" id="receipt_image" name="receipt_image"
                                    accept="image/*,.pdf">
                                <small class="form-text text-muted">Formatos permitidos: JPG, PNG, WebP, PDF. Tamaño máximo: 2MB.</small>
                            </div>
                        </div>

                        <!-- =================================================================================
                        // Botón de enviar
                        // ================================================================================= -->
                        <div class="d-grids pt-5 text-center">
                            <button type="submit" class="btn btn-primary">Enviar solicitud</button>
                            <a href="<?= base_url('expenses/my-expenses') ?>" class="btn btn-dark ms-1">Volver atrás</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>