<?php

if (!function_exists('get_configured_email')) {
    /**
     * Retorna una instancia del servicio de email configurada con los datos de la empresa.
     * Si no hay configuración en la DB, usa los valores por defecto de Config\Email.
     *
     * @return \CodeIgniter\Email\Email
     */
    function get_configured_email()
    {
        $companyModel = new \App\Models\CompanyModel();
        $company = $companyModel->getCompany();
        $email = \Config\Services::email();

        if ($company && !empty($company['smtp_host'])) {
            $config = config('Email');
            
            $config->SMTPHost = $company['smtp_host'];
            $config->SMTPPort = (int)$company['smtp_port'];
            $config->SMTPUser = $company['smtp_user'];
            
            // Desencriptar contraseña si existe
            if (!empty($company['smtp_pass'])) {
                try {
                    $encrypter = \Config\Services::encrypter();
                    $config->SMTPPass = $encrypter->decrypt(base64_decode($company['smtp_pass']));
                } catch (\Exception $e) {
                    // Si falla la desencriptación, dejamos la configurada por defecto o vacía
                    log_message('error', '[EmailHelper] Error desencriptando contraseña SMTP: ' . $e->getMessage());
                }
            }
            
            $config->SMTPCrypto = $company['smtp_crypto'] ?: 'tls';
            $config->fromEmail  = $company['smtp_from_email'] ?: $config->fromEmail;
            $config->fromName   = $company['smtp_from_name'] ?: $company['name'];
            
            $email->initialize((array)$config);
        }

        return $email;
    }
}
