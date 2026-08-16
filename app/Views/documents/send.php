<div class="container">

    <!-- =================================================================================
    // Formulario de Envío de Documento
    // ================================================================================= -->
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-primary-subtle text-white">
                    <h5 class="mb-0 text-primary">Enviar Documento</h5>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('documents/store') ?>" method="post" enctype="multipart/form-data" autocomplete="off">
                        <?= csrf_field() ?>
                        <!-- =================================================================================
                        // Fila: Destinatario
                        // ================================================================================= -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="receiver_id" class="form-label">Destinatario</label>
                                <select class="select2" id="receiver_id" name="receiver_id">
                                    <option value="">Selecciona un destinatario</option>
                                    <?php foreach ($users as $user): ?>
                                    <option value="<?= esc($user['id']) ?>"
                                        <?= old('receiver_id') == $user['id'] ? 'selected' : '' ?>>
                                        <?= esc($user['name']) ?> (<?= esc($user['email']) ?>)
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <!-- =================================================================================
                        // Fila: Título
                        // ================================================================================= -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="title" class="form-label">Título del documento</label>
                                <input type="text" class="form-control" id="title" name="title"
                                    value="<?= old('title') ?>">
                            </div>
                        </div>
                        <!-- =================================================================================
                        // Fila: Archivo
                        // ================================================================================= -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="document" class="form-label">Archivo del documento</label>
                                <input type="file" class="form-control" id="document" name="document"
                                    accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png">
                                <small class="form-text text-muted">Formatos permitidos: PDF, DOC, DOCX, TXT, JPG, JPEG, PNG. Tamaño máximo: 10MB.</small>
                            </div>
                        </div>

                        <!-- =================================================================================
                        // Botón de enviar
                        // ================================================================================= -->
                        <div class="d-grids pt-5 text-center">
                            <button type="submit" class="btn btn-primary">Enviar Documento</button>
                            <a href="<?= base_url('documents/list') ?>" class="btn btn-dark ms-1">Volver atrás</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>