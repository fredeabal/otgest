<?php
// =================================================================================
// Controlador: DocumentsController
// =================================================================================

namespace App\Controllers;

use App\Models\DocumentsModel;
use App\Models\UsersModel;
use App\Models\CompanyModel;
use CodeIgniter\Controller;
use CodeIgniter\I18n\Time;

class DocumentsController extends BaseController
{
    protected $documentsModel;
    protected $usersModel;
    protected $companyModel;

    public function __construct()
    {
        // Instanciar modelos
        $this->documentsModel = new DocumentsModel();
        $this->usersModel = new UsersModel();
        $this->companyModel = new CompanyModel();
    }

    // =================================================================================
    // Listado de documentos recibidos
    // =================================================================================
    public function list()
    {
        $userId = session()->get('user_id');
        $query = $this->documentsModel->getReceivedDocumentsQuery($userId);

        // Aplicar filtros
        if ($this->request->getGet('user_id')) {
            $query->where('documents.sender_id', $this->request->getGet('user_id'));
        }

        if ($this->request->getGet('date_from')) {
            $query->where('documents.created_at >=', $this->request->getGet('date_from') . ' 00:00:00');
        }

        if ($this->request->getGet('date_to')) {
            $query->where('documents.created_at <=', $this->request->getGet('date_to') . ' 23:59:59');
        }

        $data['documents'] = $query->paginate(100);
        $data['pager'] = $this->documentsModel->pager;
        $data['users'] = $this->usersModel->findAll();
        $data['title'] = 'Documentos Recibidos';

        echo view('template/header', $data);
        echo view('documents/list', $data);
        echo view('template/footer');
    }
    // =================================================================================
    // Mostrar formulario de envío de documento
    // =================================================================================
    public function send()
    {
        // verificamos si es un usuario con permiso 'manage_bulk' o un admin
        //si lo es mostramos todos los usuarios en el select
        $userId = session()->get('user_id');
        $user = $this->usersModel->find($userId);

        // si es un usuario con permiso 'documents.manage' o un admin mostramos todos los usuarios en el select
        if (has_permission('documents.manage')) {
            $data['users'] = $this->usersModel->where('is_active', 1)->findAll();
        } else {
            // si no tiene permiso solo mostramos usuarios con documents.manage
            $data['users'] = $this->usersModel->where('is_active', 1)->where('permissions LIKE', '%"documents.manage"%')->findAll();
        }
        //
        $data['title'] = 'Enviar Documento';

        echo view('template/header', $data);
        echo view('documents/send', $data);
        echo view('template/footer');
    }

    // =================================================================================
    // Listado de documentos enviados
    // =================================================================================
    public function sent()
    {
        $userId = session()->get('user_id');
        $query = $this->documentsModel->getSentDocumentsQuery($userId);

        // Aplicar filtros
        if ($this->request->getGet('user_id')) {
            $query->where('documents.receiver_id', $this->request->getGet('user_id'));
        }

        if ($this->request->getGet('date_from')) {
            $query->where('documents.created_at >=', $this->request->getGet('date_from') . ' 00:00:00');
        }

        if ($this->request->getGet('date_to')) {
            $query->where('documents.created_at <=', $this->request->getGet('date_to') . ' 23:59:59');
        }

        $data['documents'] = $query->paginate(100);
        $data['pager'] = $this->documentsModel->pager;
        $data['users'] = $this->usersModel->findAll();
        $data['title'] = 'Documentos Enviados';

        echo view('template/header', $data);
        echo view('documents/sent', $data);
        echo view('template/footer');
    }

    // =================================================================================
    // Envío masivo de documentos
    // =================================================================================
    public function bulkSend()
    {
        $data['title'] = 'Envío Masivo de Documentos';

        echo view('template/header', $data);
        echo view('documents/bulk_send', $data);
        echo view('template/footer');
    }

    // =================================================================================
    // Procesar envío masivo
    // =================================================================================
    public function bulkStore()
    {
        $files = $this->request->getFiles();
        $errors = [];
        $successCount = 0;

        if (empty($files['documents'])) {
            return redirect()->back()->with('errors', ['No se seleccionaron archivos.']);
        }

        // Límite máximo de archivos para evitar sobrecarga del servidor
        $maxFiles = 50;
        if (count($files['documents']) > $maxFiles) {
            return redirect()->back()->with('errors', ['Máximo ' . $maxFiles . ' archivos por lote.']);
        }

        foreach ($files['documents'] as $file) {
            if (!$file->isValid() || $file->hasMoved()) {
                $errors[] = "Archivo '{$file->getName()}': Archivo inválido.";
                continue;
            }
            
            // Validar tamaño de archivo (máximo 10MB)
            if ($file->getSize() > 10 * 1024 * 1024) {
                $errors[] = "Archivo '{$file->getName()}': Excede el tamaño máximo (10MB).";
                continue;
            }
            
            // Validar tipo de archivo
            $allowedExtensions = ['pdf', 'doc', 'docx', 'txt', 'jpg', 'jpeg', 'png'];
            $extension = strtolower($file->getExtension());
            if (!in_array($extension, $allowedExtensions)) {
                $errors[] = "Archivo '{$file->getName()}': Tipo de archivo no permitido.";
                continue;
            }
            
            // Validar tipo MIME
            $allowedMimes = [
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'text/plain',
                'image/jpeg',
                'image/png'
            ];
            if (!in_array($file->getMimeType(), $allowedMimes)) {
                $errors[] = "Archivo '{$file->getName()}': Tipo MIME no permitido.";
                continue;
            }

            $originalName = $file->getName();

            // Validar formato del nombre: identification-filename
            $parts = explode('-', $originalName, 2);
            if (count($parts) < 2) {
                $errors[] = "Archivo '{$originalName}': Formato incorrecto. Debe ser identificación-nombre.";
                continue;
            }

            $identification = $parts[0];
            $filename = $parts[1];

            // Buscar usuario por identification
            $recipient = $this->usersModel->where('identification', $identification)->first();

            if (!$recipient) {
                $errors[] = "Archivo '{$originalName}': Usuario con identificación '{$identification}' no encontrado.";
                continue;
            }

            // Generar nombre único para el archivo
            $fileName = $file->getRandomName();
            $uploadPath = WRITEPATH . 'uploads/documents/' . $recipient['id'] . '/';

            // Crear directorio si no existe
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            // Mover archivo
            if ($file->move($uploadPath, $fileName)) {
                // Guardar en BD
                $this->documentsModel->save([
                    'sender_id' => session()->get('user_id'),
                    'receiver_id' => $recipient['id'],
                    'title' => $originalName,
                    'file_path' => $fileName,
                    'description' => '',
                    'sent_at' => Time::now('Europe/Madrid', 'es_ES')
                ]);

                $documentId = $this->documentsModel->getInsertID();

                log_activity('Documentos', 'CREATE', "Envió un documento ('{$originalName}') a {$recipient['name']}");

                // Enviar notificación por correo
                $this->sendDocumentNotification($documentId);

                $successCount++;
            } else {
                $errors[] = "Archivo '{$originalName}': Error al subir el archivo.";
            }
        }

        // Inicializar redirección por defecto
        $redirect = redirect()->to('/documents/sent');

        if ($successCount > 0) {
            $successMessage = "Se procesaron {$successCount} archivos correctamente.";
            $redirect = $redirect->with('success', $successMessage);
        }

        if (!empty($errors)) {
            $errorMessage = "Errores encontrados:<br>" . implode('<br>', array_map(function($error) { return '- ' . $error; }, $errors));
            $redirect = $redirect->with('warning', $errorMessage);
        }

        return $redirect;
    }

    // =================================================================================
    // Procesar envío de documento (POST)
    // =================================================================================
    public function store()
    {
        // Reglas de validación
        $rules = [
            'receiver_id' => [
                'label' => 'destinatario',
                'rules' => 'required|integer|is_not_unique[users.id]'
            ],
            'title' => [
                'label' => 'título',
                'rules' => 'required|min_length[3]|max_length[255]'
            ],
            'document' => [
                'label' => 'documento',
                'rules' => 'uploaded[document]|max_size[document,10240]|mime_in[document,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,text/plain,image/jpeg,image/png]|ext_in[document,pdf,doc,docx,txt,jpg,jpeg,png]'
            ]
        ];

        // Validar datos
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Obtener archivo subido
        $file = $this->request->getFile('document');
        if (!$file->isValid()) {
            return redirect()->back()->with('errors', ['Error al subir el archivo.']);
        }

        // Generar nombre único para el archivo
        $fileName = $file->getRandomName();
        $recipientId = $this->request->getPost('receiver_id');
        $uploadPath = WRITEPATH . 'uploads/documents/' . $recipientId . '/';

        // Crear directorio si no existe
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        // Mover archivo
        if (!$file->move($uploadPath, $fileName)) {
            return redirect()->back()->with('errors', ['No se pudo guardar el archivo.']);
        }

        // Guardar documento en BD
        $this->documentsModel->save([
            'sender_id' => session()->get('user_id'),
            'receiver_id' => $recipientId,
            'title' => $this->request->getPost('title'),
            'file_path' => $fileName,
            'description' => '',
            'sent_at' => Time::now('Europe/Madrid', 'es_ES')
        ]);

        $documentId = $this->documentsModel->getInsertID();

        log_activity('Documentos', 'CREATE', "Envió el documento '{$this->request->getPost('title')}'");

        // Enviar notificación por correo
        $this->sendDocumentNotification($documentId);

        return redirect()->to('/documents/sent')->with('success', 'Documento enviado correctamente.');
    }

    // =================================================================================
    // Ver documento en navegador
    // =================================================================================
    public function view($id)
    {
        $userId = session()->get('user_id');
        $document = $this->documentsModel->where('id', $id)->where('(receiver_id = ' . $userId . ' OR sender_id = ' . $userId . ')')->first();

        if (!$document) {
            return redirect()->back()->with('errors', ['Documento no encontrado o no tienes permisos.']);
        }

        $filePath = WRITEPATH . 'uploads/documents/' . $document['receiver_id'] . '/' . $document['file_path'];

        if (!is_file($filePath)) {
            return redirect()->back()->with('errors', ['Archivo no encontrado.']);
        }

        // Marcar como leído solo si no lo está y el usuario es el destinatario
        if (empty($document['read_at']) && $document['receiver_id'] == $userId) {
            $this->documentsModel->markAsRead($id);
        }

        // Obtener el tipo MIME del archivo
        $mimeType = mime_content_type($filePath);

        // Para PDFs e imágenes, mostrar en navegador
        if ($mimeType === 'application/pdf' ||
            str_starts_with($mimeType, 'image/')) {
            return $this->response
                ->setHeader('Content-Type', $mimeType)
                ->setHeader('Content-Disposition', 'inline; filename="' . $document['title'] . '.' . pathinfo($document['file_path'], PATHINFO_EXTENSION) . '"')
                ->setBody(file_get_contents($filePath));
        }

        // Para otros archivos, forzar descarga
        return $this->response->download($filePath, null)->setFileName($document['title'] . '.' . pathinfo($document['file_path'], PATHINFO_EXTENSION));
    }

    // =================================================================================
    // Descargar documento
    // =================================================================================
    public function download($id)
    {
        $userId = session()->get('user_id');
        $document = $this->documentsModel->where('id', $id)->where('(receiver_id = ' . $userId . ' OR sender_id = ' . $userId . ')')->first();

        if (!$document) {
            return redirect()->back()->with('errors', ['Documento no encontrado o no tienes permisos.']);
        }

        $filePath = WRITEPATH . 'uploads/documents/' . $document['receiver_id'] . '/' . $document['file_path'];

        if (!is_file($filePath)) {
            return redirect()->back()->with('errors', ['Archivo no encontrado.']);
        }

        // Marcar como leído solo si no lo está y el usuario es el destinatario
        if (empty($document['read_at']) && $document['receiver_id'] == $userId) {
            $this->documentsModel->markAsRead($id);
        }

        // Descargar archivo
        return $this->response->download($filePath, null)->setFileName($document['title'] . '.' . pathinfo($document['file_path'], PATHINFO_EXTENSION));
    }

    // =================================================================================
    // Eliminar documento
    // =================================================================================
    public function delete($id)
    {
        $userId = session()->get('user_id');
        
        // Solo el remitente o alguien con permiso de administración puede borrar
        if (has_permission('documents.manage')) {
            $document = $this->documentsModel->where('id', $id)->first();
        } else {
            $document = $this->documentsModel->where('id', $id)->where('sender_id', $userId)->first();
        }

        if (!$document) {
            return redirect()->to('/documents/sent')->with('errors', ['Documento no encontrado o no tienes permisos para eliminarlo.']);
        }

        // Ruta del archivo físico
        $filePath = WRITEPATH . 'uploads/documents/' . $document['receiver_id'] . '/' . $document['file_path'];

        // Eliminar archivo físico si existe
        if (is_file($filePath)) {
            unlink($filePath);
        }

        // Eliminar registro de la base de datos
        $this->documentsModel->delete($id);

        log_activity('Documentos', 'DELETE', "Eliminó el documento ID: {$id}");

        return redirect()->to('/documents/sent')->with('success', 'Documento eliminado correctamente.');
    }


    // =================================================================================
    // Enviar notificación de documento recibido por correo
    // =================================================================================
    private function sendDocumentNotification($documentId)
    {
        // Obtener información completa del documento con datos del remitente y destinatario
        $document = $this->documentsModel->getDocumentWithUsers($documentId);
        if (!$document) {
            return; // Si no se encuentra el documento, salir
        }

        // Configurar el servicio de correo electrónico
        helper('email');
        $emailService = get_configured_email();

        $company = $this->companyModel->getCompany();
        $companyName = $company ? $company['name'] : 'OtGest';

        // get_configured_email() ya configura el 'from' si existe en la BD
        $emailService->setTo($document['receiver_email']); // Destinatario: correo del receptor
        $emailService->setSubject('Nuevo documento recibido'); // Asunto del correo

        // Contenido HTML del correo con diseño responsivo
        $emailService->setMessage(
            '<div style="font-family: Arial, Helvetica, sans-serif; background: #f7f7f7; padding: 32px 0;">
                <div style="max-width: 480px; margin: 0 auto; background: #fff; border-radius: 10px; box-shadow: 0 2px 12px rgba(93,135,255,0.08); padding: 32px 24px;">
                    <h2 style="color: #5d87ff; margin-bottom: 16px; text-align: center;">Nuevo documento recibido</h2>
                    <p style="color: #222; margin-bottom: 18px;">Hola, <b>' . esc($document['receiver_name']) . '</b>:</p>
                    <p style="color: #444; margin-bottom: 24px;">Has recibido un nuevo documento de <b>' . esc($document['sender_name']) . '</b>.</p>
                    <div style="background: #f8f9fa; padding: 16px; border-radius: 6px; margin-bottom: 24px;">
                        <p style="margin: 0; color: #333;"><strong>Título:</strong> ' . esc($document['title']) . '</p>
                        <p style="margin: 8px 0 0 0; color: #333;"><strong>Fecha de envío:</strong> ' . esc(date('d/m/Y H:i', strtotime($document['sent_at']))) . '</p>
                    </div>
                    <div style="text-align: center; margin-bottom: 32px;">
                        <a href="' . site_url('documents/list') . '" style="display: inline-block; background: #5d87ff; color: #fff; text-decoration: none; padding: 12px 32px; border-radius: 6px; font-weight: bold; font-size: 16px;">Ver documento</a>
                    </div>
                    <p style="color: #888; font-size: 13px; text-align: center;">Puedes acceder a tu cuenta para descargar el documento.</p>
                    <hr style="border: none; border-top: 1px solid #eee; margin: 32px 0 16px 0;">
                    <p style="color: #aaa; font-size: 12px; text-align: center;">' . esc($companyName) . '</p>
                </div>
            </div>'
        );

        // Intentar enviar el correo y registrar error si falla
        if (!$emailService->send()) {
            log_message('error', 'No se pudo enviar la notificación de documento a ' . $document['receiver_email']);
        }
    }
}