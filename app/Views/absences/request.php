<div class="container">
    <!-- =================================================================================
    // Formulario de Solicitud de Ausencia
    // ================================================================================= -->
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-primary-subtle text-white">
                    <h5 class="mb-0 text-primary">Solicitar Ausencia</h5>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('absences/store') ?>" method="post" enctype="multipart/form-data">
                        <?= csrf_field() ?>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label for="type" class="form-label">Tipo de Ausencia</label>
                                    <select name="type" id="type" class="form-control">
                                        <option value="">Seleccionar tipo</option>
                                        <?php foreach ($absenceTypes as $key => $label): ?>
                                            <option value="<?= $key ?>" <?= (old('type') == $key) ? 'selected' : '' ?>>
                                                <?= $label ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (isset($errors['type'])): ?>
                                        <div class="text-danger small"><?= $errors['type'] ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="start_date" class="form-label">Fecha de Inicio</label>
                                    <input type="date" name="start_date" id="start_date" class="form-control" value="<?= old('start_date') ?>">
                                    <?php if (isset($errors['start_date'])): ?>
                                        <div class="text-danger small"><?= $errors['start_date'] ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="end_date" class="form-label">Fecha de Fin</label>
                                    <input type="date" name="end_date" id="end_date" class="form-control" value="<?= old('end_date') ?>">
                                    <?php if (isset($errors['end_date'])): ?>
                                        <div class="text-danger small"><?= $errors['end_date'] ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="start_time" class="form-label">Hora de Inicio (opcional)</label>
                                    <input type="time" name="start_time" id="start_time" class="form-control" value="<?= old('start_time') ?>">
                                    <?php if (isset($errors['start_time'])): ?>
                                        <div class="text-danger small"><?= $errors['start_time'] ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="end_time" class="form-label">Hora de Fin (opcional)</label>
                                    <input type="time" name="end_time" id="end_time" class="form-control" value="<?= old('end_time') ?>">
                                    <?php if (isset($errors['end_time'])): ?>
                                        <div class="text-danger small"><?= $errors['end_time'] ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="comments" class="form-label">Comentarios</label>
                            <textarea name="comments" id="comments" class="form-control" rows="4"
                                      placeholder="Añade cualquier comentario adicional..."><?= old('comments') ?></textarea>
                            <?php if (isset($errors['comments'])): ?>
                                <div class="text-danger small"><?= $errors['comments'] ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group mb-3">
                            <label for="attachment" class="form-label">Archivo Adjunto (opcional)</label>
                            <input type="file" name="attachment" id="attachment" class="form-control"
                                   accept=".jpg,.jpeg,.png,.gif,.pdf">
                            <small class="form-text text-muted">
                                Puedes adjuntar imágenes o PDFs relacionados con tu ausencia (máx. 5MB)
                            </small>
                            <?php if (isset($errors['attachment'])): ?>
                                <div class="text-danger small"><?= $errors['attachment'] ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group text-center pt-4">
                            <button type="submit" class="btn btn-primary">
                                Enviar Solicitud
                            </button>
                            <a href="<?= base_url('absences/list') ?>" class="btn btn-dark ms-1">Volver atrás</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>