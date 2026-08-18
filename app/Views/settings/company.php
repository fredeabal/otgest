<div class="container-fluid">


    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card border">
                <div class="card-header bg-primary-subtle text-white">
                    <h5 class="mb-0 text-primary">Configuración de empresa</h5>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('settings/company/update') ?>" method="post" autocomplete="off">
                        <?= csrf_field() ?>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="cif" class="form-label">CIF</label>
                                <input type="text" class="form-control" id="cif" name="cif"
                                    value="<?= old('cif', esc($company['cif'] ?? '')) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="name" class="form-label">Nombre de la Empresa</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    value="<?= old('name', esc($company['name'] ?? '')) ?>" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label for="address" class="form-label">Dirección</label>
                                <input type="text" class="form-control" id="address" name="address"
                                    value="<?= old('address', esc($company['address'] ?? '')) ?>" maxlength="255">
                            </div>
                            <div class="col-md-4">
                                <label for="postal_code" class="form-label">Código Postal</label>
                                <input type="text" class="form-control" id="postal_code" name="postal_code"
                                    value="<?= old('postal_code', esc($company['postal_code'] ?? '')) ?>"
                                    maxlength="10">
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Teléfono</label>
                                <input type="tel" class="form-control" id="phone" name="phone"
                                    value="<?= old('phone', esc($company['phone'] ?? '')) ?>" maxlength="20">
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    value="<?= old('email', esc($company['email'] ?? '')) ?>" maxlength="255">
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="website" class="form-label">Página Web</label>
                                <input type="url" class="form-control" id="website" name="website"
                                    value="<?= old('website', esc($company['website'] ?? '')) ?>" maxlength="255"
                                    placeholder="https://www.ejemplo.com">
                            </div>
                        </div>
                        
                        <div class="pt-4 text-center">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="ti ti-device-floppy"></i> <span class="d-none d-md-inline ms-1">Guardar Cambios</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


</div>
