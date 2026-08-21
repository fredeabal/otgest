<?php

namespace App\Controllers;

use App\Models\AbsenceModel;
use App\Models\UsersModel;
use App\Models\CompanyModel;
use CodeIgniter\I18n\Time;

class AbsenceController extends BaseController
{
    protected $absenceModel;
    protected $usersModel;
    protected $companyModel;

    public function __construct()
    {
        $this->absenceModel = new AbsenceModel();
        $this->usersModel = new UsersModel();
        $this->companyModel = new CompanyModel();
    }

    // =================================================================================
    // Mostrar formulario de solicitud de ausencia
    // =================================================================================
    public function request()
    {
        $data['title'] = 'Solicitar Ausencia';
        $data['absenceTypes'] = $this->absenceModel->getAbsenceTypes();

        echo view('template/header', $data);
        echo view('absences/request', $data);
        echo view('template/footer');
    }

    // =================================================================================
    // Procesar solicitud de ausencia
    // =================================================================================
    public function store()
    {
        $userId = session()->get('user_id');

        // Reglas de validación
        $rules = [
            'type' => [
                'label' => 'tipo de ausencia',
                'rules' => 'required|in_list[baja,accidente,enfermedad,maternidad,paternidad,fallecimiento,cuidado,vacaciones,permiso,festivo,formacion,viaje,asuntos,retraso,injustificada,suspension,huelga,otros]'
            ],
            'start_date' => [
                'label' => 'fecha de inicio',
                'rules' => 'required|valid_date'
            ],
            'end_date' => [
                'label' => 'fecha de fin',
                'rules' => 'required|valid_date'
            ],
            'start_time' => [
                'label' => 'hora de inicio',
                'rules' => 'permit_empty'
            ],
            'end_time' => [
                'label' => 'hora de fin',
                'rules' => 'permit_empty'
            ],
            'comments' => [
                'label' => 'comentarios',
                'rules' => 'permit_empty|max_length[1000]'
            ]
        ];

        // Validar datos
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $startDate = $this->request->getPost('start_date');
        $endDate = $this->request->getPost('end_date');

        // Validar que la fecha de inicio no sea posterior a la de fin
        if (strtotime($startDate) > strtotime($endDate)) {
            return redirect()->back()->withInput()->with('errors', ['La fecha de inicio no puede ser posterior a la fecha de fin.']);
        }

        // Validar que las fechas no sean en el pasado (excepto para admins)
        $today = date('Y-m-d');
        if (!has_permission('absences.manage') && strtotime($startDate) < strtotime($today)) {
            return redirect()->back()->withInput()->with('errors', ['No se pueden solicitar ausencias para fechas pasadas.']);
        }

        // Verificación del límite de días de vacaciones
        if ($this->request->getPost('type') == 'vacaciones') {
            $user = $this->usersModel->find($userId);
            $vacationDaysval = $user['vacation_days'] ?? null;
            $vacationDaysAllowed = ($vacationDaysval !== null && $vacationDaysval !== '') ? (int)$vacationDaysval : 22;
            
            if ($vacationDaysAllowed > 0) {
                // Calcular días para esta nueva solicitud
                $requestedDays = $this->calculateWorkingDays($startDate, $endDate, 'vacaciones');
                
                // Total de días disfrutados este año
                $currentYear = date('Y', strtotime($startDate));
                $absencesThisYear = $this->absenceModel
                    ->where('user_id', $userId)
                    ->where('type', 'vacaciones')
                    ->where('status !=', 'rejected')
                    ->where('status !=', 'cancelled')
                    ->findAll();
                
                $totalDaysTaken = 0;
                foreach ($absencesThisYear as $abs) {
                    if (date('Y', strtotime($abs['start_date'])) == $currentYear) {
                        $totalDaysTaken += $this->calculateWorkingDays($abs['start_date'], $abs['end_date'], 'vacaciones');
                    }
                }
                
                if (($totalDaysTaken + $requestedDays) > $vacationDaysAllowed) {
                    return redirect()->back()->withInput()->with('errors', ["No puedes exceder tu límite de {$vacationDaysAllowed} días de vacaciones anuales. Actualmente has solicitado/tomado {$totalDaysTaken} días."]);
                }
            } else if ($vacationDaysAllowed === '0' || $vacationDaysAllowed === 0) {
                return redirect()->back()->withInput()->with('errors', ["No tienes días de vacaciones asignados."]);
            }
        }

        // Verificar superposiciones con otras solicitudes
        if ($this->absenceModel->checkOverlap($userId, $startDate, $endDate)) {
            return redirect()->back()->withInput()->with('errors', ['Ya tienes una solicitud de ausencia que se superpone con estas fechas.']);
        }

        // Procesar archivo adjunto si existe
        $attachmentPath = null;
        $attachmentFile = $this->request->getFile('attachment');

        if ($attachmentFile && $attachmentFile->isValid()) {
            // Generar nombre único para el archivo
            $fileName = $attachmentFile->getRandomName();
            $uploadPath = WRITEPATH . 'uploads/absences/' . $userId . '/';

            // Crear directorio si no existe
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            // Mover archivo
            $attachmentFile->move($uploadPath, $fileName);
            $attachmentPath = 'uploads/absences/' . $userId . '/' . $fileName;
        }

        // Preparar datos para guardar
        $absenceData = [
            'user_id' => $userId,
            'type' => $this->request->getPost('type'),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'start_time' => $this->request->getPost('start_time') ?: null,
            'end_time' => $this->request->getPost('end_time') ?: null,
            'comments' => $this->request->getPost('comments'),
            'attachment' => $attachmentPath,
            'status' => 'pending',
        ];

        // Guardar solicitud
        $this->absenceModel->save($absenceData);
        
        // Log activity
        log_activity('Ausencias', 'CREATE', "Solicitó una ausencia de tipo: " . $this->request->getPost('type'));

        return redirect()->to('/absences/list')->with('success', 'Solicitud de ausencia enviada correctamente.');
    }

    // =================================================================================
    // Lista de solicitudes del usuario
    // =================================================================================
    public function list()
    {
        $userId = session()->get('user_id');

        $query = $this->absenceModel->where('user_id', $userId);

        // Filtros
        if ($this->request->getGet('type')) {
            $query->where('type', $this->request->getGet('type'));
        }

        // Filtro de estado (default: todos)
        $status = $this->request->getGet('status') ?? '';
        if ($status !== '' && $status !== 'all') {
            $query->where('status', $status);
        }

        if ($this->request->getGet('date_from')) {
            $query->where('end_date >=', $this->request->getGet('date_from'));
        }

        if ($this->request->getGet('date_to')) {
            $query->where('start_date <=', $this->request->getGet('date_to'));
        }

        $data['absences'] = $query->orderBy('created_at', 'DESC')->paginate(10);
        $data['pager'] = $this->absenceModel->pager;
        $data['absenceTypes'] = $this->absenceModel->getAbsenceTypes();
        $data['statusLabels'] = $this->absenceModel->getStatusLabels();
        $data['current_status'] = $status;
        $data['title'] = 'Mis Solicitudes de Ausencia';

        echo view('template/header', $data);
        echo view('absences/list', $data);
        echo view('template/footer');
    }

    // =================================================================================
    // Exportar mis ausencias a PDF (Vista de usuario)
    // =================================================================================
    public function exportListPdf()
    {
        // Consulta base para obtener las ausencias del usuario actual
        $query = $this->absenceModel->select('absences.*, users.name as reviewer_name')
            ->join('users', 'users.id = absences.processed_by', 'left')
            ->where('absences.user_id', session()->get('user_id'));

        // Aplicar filtros
        if ($this->request->getGet('type')) {
            $query->where('absences.type', $this->request->getGet('type'));
        }

        $status = $this->request->getGet('status') ?? '';
        if ($status !== '' && $status !== 'all') {
            $query->where('absences.status', $status);
        }

        if ($this->request->getGet('date_from')) {
            $query->where('absences.end_date >=', $this->request->getGet('date_from'));
        }

        if ($this->request->getGet('date_to')) {
            $query->where('absences.start_date <=', $this->request->getGet('date_to'));
        }

        // Obtener resultados
        $absences = $query->orderBy('absences.created_at', 'DESC')->findAll();
        $absenceTypes = $this->absenceModel->getAbsenceTypes();
        $statusLabels = $this->absenceModel->getStatusLabels();

        // Configurar DomPDF
        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->setPaper('A4', 'landscape');

        // Generar HTML para el PDF
        $html = $this->generateAbsenceListPdfHtml($absences, $absenceTypes, $statusLabels);

        // Cargar HTML en DomPDF
        $dompdf->loadHtml($html);
        $dompdf->render();

        // Enviar PDF al navegador usando el response de CodeIgniter
        $pdfContent = $dompdf->output();
        return $this->response->download('mis_ausencias.pdf', $pdfContent);
    }

    /**
     * Genera el HTML para el PDF de ausencias del usuario
     */
    private function generateAbsenceListPdfHtml($absences, $absenceTypes, $statusLabels)
    {
        // Obtener información de la empresa
        $company = $this->companyModel->getCompany();
        $companyName = $company ? $company['name'] : 'OtGest';

        // Obtener datos del usuario
        $user = $this->usersModel->find(session()->get('user_id'));

        // Calcular días totales de ausencia
        $totalDays = 0;
        foreach ($absences as $absence) {
            $totalDays += $this->calculateWorkingDays($absence['start_date'], $absence['end_date'], $absence['type']);
        }

        // Estilos CSS embebidos para el PDF
        $html = '<style>
            body { font-family: Arial, sans-serif; margin: 15px; font-size: 10px; }
            h1 { color: #333; text-align: center; margin-bottom: 20px; font-size: 14px; }
            table { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 9px; }
            th, td { border: 1px solid #ddd; padding: 4px; text-align: left; }
            th { background-color: #f5f5f5; font-weight: bold; font-size: 9px; }
            tr:nth-child(even) { background-color: #f9f9f9; }
            .header-info { margin-bottom: 15px; font-size: 9px; }
            .total-row { background-color: #fff3cd; font-weight: bold; }
            .status-pending { background-color: #fff3cd; color: #856404; }
            .status-approved { background-color: #d4edda; color: #155724; }
            .status-rejected { background-color: #f8d7da; color: #721c24; }
            .status-cancelled { background-color: #e2e3e5; color: #383d41; }
        </style>';

        // Cabecera con información general
        $html .= '
        <div class="header-info">
            <strong>' . esc($companyName) . '</strong><br>
            <strong>Reporte de Mis Ausencias</strong><br>
            <strong>Usuario:</strong> ' . esc($user['name']) . '<br>
            <strong>DNI:</strong> ' . esc($user['identification']) . '<br>
            <strong>Fecha:</strong> ' . date('d/m/Y H:i:s') . '<br>
            <strong>Registros:</strong> ' . count($absences) . '<br>
        </div>';

        // Tabla principal
        $html .= '<table>
            <thead>
                <tr>
                    <th>Tipo</th>
                    <th>Fecha Solicitud</th>
                    <th>Fecha Inicio</th>
                    <th>Fecha Fin</th>
                    <th>Estado</th>
                    <th>Procesado por</th>
                    <th>Fecha procesado</th>
                </tr>
            </thead>
            <tbody>';

        foreach ($absences as $absence) {
            $html .= '<tr>
                <td>' . esc($absenceTypes[$absence['type']] ?? $absence['type']) . '</td>
                <td>' . date('d/m/Y H:i', strtotime($absence['created_at'])) . '</td>
                <td>' . date('d/m/Y', strtotime($absence['start_date'])) . '</td>
                <td>' . date('d/m/Y', strtotime($absence['end_date'])) . '</td>
                <td>' . $statusLabels[$absence['status']] . '</td>
                <td>' . esc($absence['reviewer_name'] ?? '-') . '</td>
                <td>' . ($absence['status'] !== 'pending' && $absence['updated_at'] && $absence['updated_at'] != $absence['created_at'] ? date('d/m/Y H:i', strtotime($absence['updated_at'])) : '-') . '</td>
            </tr>';
        }

        // Fila de totales
        $html .= '
            <tr class="total-row">
                <td colspan="4"><strong>TOTAL</strong></td>
                <td colspan="3"><strong>' . $totalDays . ' días</strong></td>
            </tr>
        </tbody>
        </table>';

        // Pie de página final
        $html .= '
        <div style="margin-top: 20px; font-size: 8px; color: #666; text-align: center;">
            <p><em>Documento generado automáticamente por el sistema de gestión.</em></p>
        </div>';

        return $html;
    }

    // =================================================================================
    // Panel de administración de solicitudes
    // =================================================================================
    public function manage()
    {
        $query = $this->absenceModel->select('absences.*, users.name as user_name, users.identification as user_identification, processor.name as processed_by_name')
            ->join('users', 'users.id = absences.user_id', 'left')
            ->join('users as processor', 'processor.id = absences.processed_by', 'left');

        // Filtros
        if ($this->request->getGet('user_id')) {
            $query->where('absences.user_id', $this->request->getGet('user_id'));
        }

        if ($this->request->getGet('type')) {
            $query->where('absences.type', $this->request->getGet('type'));
        }

        // Filtro de estado con default 'pending'
        $status = $this->request->getGet('status') ?: 'pending';
        if ($status !== 'all') {
            $query->where('absences.status', $status);
        }

        if ($this->request->getGet('date_from')) {
            $query->where('absences.end_date >=', $this->request->getGet('date_from'));
        }

        if ($this->request->getGet('date_to')) {
            $query->where('absences.start_date <=', $this->request->getGet('date_to'));
        }

        $data['absences'] = $query->orderBy('absences.created_at', 'DESC')->paginate(100);
        $data['pager'] = $this->absenceModel->pager;
        $data['users'] = $this->usersModel->findAll();
        $data['absenceTypes'] = $this->absenceModel->getAbsenceTypes();
        $data['statusLabels'] = $this->absenceModel->getStatusLabels();
        $data['current_status'] = $status;
        $data['title'] = 'Gestión de Solicitudes de Ausencia';

        echo view('template/header', $data);
        echo view('absences/manage', $data);
        echo view('template/footer');
    }

    // =================================================================================
    // Aprobar solicitud
    // =================================================================================
    public function approve($id)
    {
        $absence = $this->absenceModel->find($id);
        if (!$absence) {
            return redirect()->to('/absences/manage')->with('errors', ['Solicitud no encontrada.']);
        }

        $this->absenceModel->update($id, [
            'status' => 'approved',
            'processed_by' => session()->get('user_id'),
            'updated_at' => Time::now('Europe/Madrid', 'es_ES'),
        ]);

        log_activity('Ausencias', 'APPROVE', "Aprobó la solicitud de ausencia ID: {$id}");

        // Enviar correo de aprobación al usuario
        $user = $this->usersModel->find($absence['user_id']);
        if ($user) {
            $this->sendAbsenceResponseEmail($user, $absence, 'approved');
        }

        return redirect()->to('/absences/manage')->with('success', 'Solicitud aprobada correctamente.');
    }

    // =================================================================================
    // Rechazar solicitud
    // =================================================================================
    public function reject($id)
    {
        $absence = $this->absenceModel->find($id);
        if (!$absence) {
            return redirect()->to('/absences/manage')->with('errors', ['Solicitud no encontrada.']);
        }

        $adminComments = $this->request->getPost('admin_comments');

        $this->absenceModel->update($id, [
            'status' => 'rejected',
            'processed_by' => session()->get('user_id'),
            'admin_comments' => $adminComments,
            'updated_at' => Time::now('Europe/Madrid', 'es_ES'),
        ]);

        log_activity('Ausencias', 'REJECT', "Rechazó la solicitud de ausencia ID: {$id}");

        // Enviar correo de rechazo al usuario
        $user = $this->usersModel->find($absence['user_id']);
        if ($user) {
            $this->sendAbsenceResponseEmail($user, $absence, 'rejected', $adminComments);
        }

        return redirect()->to('/absences/manage')->with('success', 'Solicitud rechazada correctamente.');
    }

    // =================================================================================
    // Cancelar solicitud
    // =================================================================================
    public function cancel($id)
    {
        $userId = session()->get('user_id');
        $absence = $this->absenceModel->find($id);

        if (!$absence || $absence['user_id'] != $userId) {
            return redirect()->to('/absences/list')->with('errors', ['Solicitud no encontrada.']);
        }

        if ($absence['status'] != 'pending') {
            return redirect()->to('/absences/list')->with('errors', ['Solo se pueden cancelar solicitudes pendientes.']);
        }

        $this->absenceModel->update($id, [
            'status' => 'cancelled',
            'updated_at' => Time::now('Europe/Madrid', 'es_ES'),
        ]);

        log_activity('Ausencias', 'CANCEL', "Canceló su solicitud de ausencia ID: {$id}");

        return redirect()->to('/absences/list')->with('success', 'Solicitud cancelada correctamente.');
    }

    // =================================================================================
    // Mostrar formulario de edición de solicitud
    // =================================================================================
    public function edit($id)
    {
        $absence = $this->absenceModel->find($id);
        if (!$absence) {
            return redirect()->to('/absences/list')->with('errors', ['Solicitud no encontrada.']);
        }

        // Solo el propietario puede editar
        if ($absence['user_id'] != session()->get('user_id')) {
            return redirect()->to('/absences/list')->with('errors', ['No tienes permiso para editar esta solicitud.']);
        }

        // Solo solicitudes pendientes pueden editarse
        if ($absence['status'] != 'pending') {
            return redirect()->to('/absences/list')->with('errors', ['Solo se pueden editar solicitudes pendientes.']);
        }

        $data['absence'] = $absence;
        $data['absenceTypes'] = $this->absenceModel->getAbsenceTypes();
        $data['title'] = 'Editar Solicitud de Ausencia';

        echo view('template/header', $data);
        echo view('absences/edit', $data);
        echo view('template/footer');
    }

    // =================================================================================
    // Actualizar solicitud de ausencia
    // =================================================================================
    public function update($id)
    {
        $absence = $this->absenceModel->find($id);
        if (!$absence) {
            return redirect()->to('/absences/list')->with('errors', ['Solicitud no encontrada.']);
        }

        // Solo el propietario puede actualizar
        if ($absence['user_id'] != session()->get('user_id')) {
            return redirect()->to('/absences/list')->with('errors', ['No tienes permiso para editar esta solicitud.']);
        }

        // Solo solicitudes pendientes pueden actualizarse
        if ($absence['status'] != 'pending') {
            return redirect()->to('/absences/list')->with('errors', ['Solo se pueden editar solicitudes pendientes.']);
        }

        // Reglas de validación
        $rules = [
            'type' => [
                'label' => 'tipo de ausencia',
                'rules' => 'required|in_list[baja,accidente,enfermedad,maternidad,paternidad,fallecimiento,cuidado,vacaciones,permiso,festivo,formacion,viaje,asuntos,retraso,injustificada,suspension,huelga,otros]'
            ],
            'start_date' => [
                'label' => 'fecha de inicio',
                'rules' => 'required|valid_date'
            ],
            'end_date' => [
                'label' => 'fecha de fin',
                'rules' => 'required|valid_date'
            ],
            'start_time' => [
                'label' => 'hora de inicio',
                'rules' => 'permit_empty'
            ],
            'end_time' => [
                'label' => 'hora de fin',
                'rules' => 'permit_empty'
            ],
            'comments' => [
                'label' => 'comentarios',
                'rules' => 'permit_empty|max_length[1000]'
            ],
            'attachment' => [
                'label' => 'archivo adjunto',
                'rules' => 'permit_empty|max_size[attachment,5120]|mime_in[attachment,image/jpg,image/jpeg,image/png,image/gif,application/pdf]'
            ]
        ];

        // Validar datos
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $startDate = $this->request->getPost('start_date');
        $endDate = $this->request->getPost('end_date');

        // Validar que la fecha de inicio no sea posterior a la de fin
        if (strtotime($startDate) > strtotime($endDate)) {
            return redirect()->back()->withInput()->with('errors', ['La fecha de inicio no puede ser posterior a la fecha de fin.']);
        }

        // Validar que las fechas no sean en el pasado (excepto para admins)
        $today = date('Y-m-d');
        if (!has_permission('absences.manage') && strtotime($startDate) < strtotime($today)) {
            return redirect()->back()->withInput()->with('errors', ['No se pueden solicitar ausencias para fechas pasadas.']);
        }

        // Verificación del límite de días de vacaciones
        if ($this->request->getPost('type') == 'vacaciones') {
            $user = $this->usersModel->find(session()->get('user_id'));
            $vacationDaysval = $user['vacation_days'] ?? null;
            $vacationDaysAllowed = ($vacationDaysval !== null && $vacationDaysval !== '') ? (int)$vacationDaysval : 22;
            
            if ($vacationDaysAllowed > 0) {
                // Calcular días para esta nueva solicitud
                $requestedDays = $this->calculateWorkingDays($startDate, $endDate, 'vacaciones');
                
                // Total de días disfrutados este año
                $currentYear = date('Y', strtotime($startDate));
                $absencesThisYear = $this->absenceModel
                    ->where('user_id', session()->get('user_id'))
                    ->where('id !=', $id)
                    ->where('type', 'vacaciones')
                    ->where('status !=', 'rejected')
                    ->where('status !=', 'cancelled')
                    ->findAll();
                
                $totalDaysTaken = 0;
                foreach ($absencesThisYear as $abs) {
                    if (date('Y', strtotime($abs['start_date'])) == $currentYear) {
                        $totalDaysTaken += $this->calculateWorkingDays($abs['start_date'], $abs['end_date'], 'vacaciones');
                    }
                }
                
                if (($totalDaysTaken + $requestedDays) > $vacationDaysAllowed) {
                    return redirect()->back()->withInput()->with('errors', ["No puedes exceder tu límite de {$vacationDaysAllowed} días de vacaciones anuales. Actualmente has solicitado/tomado {$totalDaysTaken} días."]);
                }
            } else if ($vacationDaysAllowed === '0' || $vacationDaysAllowed === 0) {
                return redirect()->back()->withInput()->with('errors', ["No tienes días de vacaciones asignados."]);
            }
        }

        // Verificar superposiciones con otras solicitudes (excluyendo la actual)
        if ($this->absenceModel->checkOverlap(session()->get('user_id'), $startDate, $endDate, $id)) {
            return redirect()->back()->withInput()->with('errors', ['Ya tienes una solicitud de ausencia que se superpone con estas fechas.']);
        }

        // Procesar archivo adjunto si existe
        $attachmentPath = $absence['attachment']; // Mantener el archivo actual por defecto
        $attachmentFile = $this->request->getFile('attachment');

        if ($attachmentFile && $attachmentFile->isValid()) {
            // Eliminar archivo anterior si existe
            if (!empty($absence['attachment'])) {
                $oldFilePath = WRITEPATH . $absence['attachment'];
                if (is_file($oldFilePath)) {
                    unlink($oldFilePath);
                }
            }

            // Generar nombre único para el nuevo archivo
            $fileName = $attachmentFile->getRandomName();
            $uploadPath = WRITEPATH . 'uploads/absences/' . session()->get('user_id') . '/';

            // Crear directorio si no existe
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            // Mover archivo
            $attachmentFile->move($uploadPath, $fileName);
            $attachmentPath = 'uploads/absences/' . session()->get('user_id') . '/' . $fileName;
        }

        // Preparar datos para actualizar
        $updateData = [
            'type' => $this->request->getPost('type'),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'start_time' => $this->request->getPost('start_time') ?: null,
            'end_time' => $this->request->getPost('end_time') ?: null,
            'comments' => $this->request->getPost('comments'),
            'attachment' => $attachmentPath,
            'updated_at' => Time::now('Europe/Madrid', 'es_ES'),
        ];

        // Actualizar solicitud
        $this->absenceModel->update($id, $updateData);

        return redirect()->to('/absences/list')->with('success', 'Solicitud de ausencia actualizada correctamente.');
    }

    // =================================================================================
    // Ver detalles de solicitud
    // =================================================================================
    public function view($id)
    {
        $absence = $this->absenceModel->find($id);
        if (!$absence) {
            return redirect()->to('/absences/list')->with('errors', ['Solicitud no encontrada.']);
        }

        // Verificar permisos: solo el propietario o admin pueden ver
        if (!has_permission('absences.manage') && $absence['user_id'] != session()->get('user_id')) {
            return redirect()->to('/absences/list')->with('errors', ['No tienes permiso para ver esta solicitud.']);
        }

        $data['absence'] = $absence;
        $data['absenceTypes'] = $this->absenceModel->getAbsenceTypes();
        $data['statusLabels'] = $this->absenceModel->getStatusLabels();

        // Obtener el nombre del usuario solicitante
        $user = $this->usersModel->find($absence['user_id']);
        $data['absence']['user_name'] = $user ? $user['name'] : 'Usuario desconocido';
        $data['absence']['user_identification'] = $user ? $user['identification'] : 'Sin DNI';

        // Obtener el nombre del usuario que procesó la solicitud si existe
        if ($absence['processed_by']) {
            $processor = $this->usersModel->find($absence['processed_by']);
            $data['absence']['processed_by_name'] = $processor ? $processor['name'] : 'Usuario desconocido';
        }

        $data['title'] = 'Detalles de Solicitud de Ausencia';

        echo view('template/header', $data);
        echo view('absences/view', $data);
        echo view('template/footer');
    }

    // =================================================================================
    // Exportar ausencias a PDF (Vista de gestión)
    // =================================================================================
    public function exportPdf()
    {
        $query = $this->absenceModel->select('absences.*, users.name as user_name, users.identification as user_identification, processor.name as processed_by_name')
            ->join('users', 'users.id = absences.user_id', 'left')
            ->join('users as processor', 'processor.id = absences.processed_by', 'left');

        // Aplicar filtros exactamente igual que en manage()
        $userId = $this->request->getGet('user_id');
        if ($userId) {
            $query->where('absences.user_id', $userId);
        }

        $type = $this->request->getGet('type');
        if ($type) {
            $query->where('absences.type', $type);
        }

        // Filtro de estado con default 'pending'
        $status = $this->request->getGet('status') ?: 'pending';
        if ($status !== 'all') {
            $query->where('absences.status', $status);
        }

        $dateFrom = $this->request->getGet('date_from');
        if ($dateFrom) {
            $query->where('absences.end_date >=', $dateFrom);
        }

        $dateTo = $this->request->getGet('date_to');
        if ($dateTo) {
            $query->where('absences.start_date <=', $dateTo);
        }

        $absences = $query->orderBy('absences.created_at', 'DESC')->findAll();

        // Configurar DomPDF
        $options = new \Dompdf\Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->setPaper('A4', 'landscape');

        // Generar HTML del PDF
        $html = $this->generateAbsencesPdfHtml($absences, $status);

        $dompdf->loadHtml($html);
        $dompdf->render();

        // Descargar el PDF
        $dompdf->stream('ausencias_' . date('Y-m-d') . '.pdf', ['Attachment' => true]);
    }

    // =================================================================================
    // Generar HTML para el PDF de ausencias
    // =================================================================================
    private function generateAbsencesPdfHtml($absences, $status = 'pending')
    {
        // Obtener información de la empresa
        $company = $this->companyModel->getCompany();
        $companyName = $company ? $company['name'] : 'OtGest';

        // Obtener tipos de ausencia
        $absenceTypes = $this->absenceModel->getAbsenceTypes();

        // Estilos CSS embebidos para el PDF
        $html = '<style>
            body { font-family: Arial, sans-serif; margin: 15px; font-size: 10px; }
            h1 { color: #333; text-align: center; margin-bottom: 20px; font-size: 14px; }
            table { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 9px; }
            th, td { border: 1px solid #ddd; padding: 4px; text-align: left; }
            th { background-color: #f5f5f5; font-weight: bold; font-size: 9px; }
            tr:nth-child(even) { background-color: #f9f9f9; }
            .header-info { margin-bottom: 15px; font-size: 9px; }
            .total-row { background-color: #fff3cd; font-weight: bold; }
            .status-pending { background-color: #fff3cd; color: #856404; }
            .status-approved { background-color: #d4edda; color: #155724; }
            .status-rejected { background-color: #f8d7da; color: #721c24; }
        </style>';

        // Cabecera con información general
        $statusText = [
            'pending' => 'Pendientes de Aprobación',
            'approved' => 'Aprobadas',
            'rejected' => 'Rechazadas',
            'cancelled' => 'Canceladas',
            'all' => 'Todas'
        ];
        $html .= '
        <div class="header-info">
            <strong>' . esc($companyName) . '</strong><br>
            <strong>Reporte de Ausencias ' . ($statusText[$status] ?? 'Filtradas') . '</strong><br>
            <strong>Generado por:</strong> ' . esc(session()->get('user_name')) . '<br>
            <strong>Fecha:</strong> ' . date('d/m/Y H:i:s') . '<br>
            <strong>Registros:</strong> ' . count($absences) . '<br>
        </div>';

        // Tabla principal
        $html .= '<table>
            <thead>
                <tr>
                    <th>Solicitante</th>
                    <th>DNI</th>
                    <th>Tipo</th>
                    <th>Fecha Solicitud</th>
                    <th>Fecha Inicio</th>
                    <th>Fecha Fin</th>
                    <th>Estado</th>
                    <th>Procesado por</th>
                    <th>Fecha procesado</th>
                </tr>
            </thead>
            <tbody>';

        // Variables para totales
        $totalDays = 0;

        // Recorrer ausencias
        foreach ($absences as $absence) {
            // Calcular días
            $days = $this->calculateWorkingDays($absence['start_date'], $absence['end_date'], $absence['type']);
            
            $html .= '<tr>
                <td>' . esc($absence['user_name']) . '</td>
                <td>' . esc($absence['user_identification']) . '</td>
                <td>' . esc($absenceTypes[$absence['type']] ?? $absence['type']) . '</td>
                <td>' . date('d/m/Y H:i', strtotime($absence['created_at'])) . '</td>
                <td>' . date('d/m/Y', strtotime($absence['start_date'])) . '</td>
                <td>' . date('d/m/Y', strtotime($absence['end_date'])) . '</td>
                <td>' . $this->absenceModel->getStatusLabels()[$absence['status']] . '</td>
                <td>' . esc($absence['processed_by_name'] ?? '-') . '</td>
                <td>' . ($absence['status'] !== 'pending' && $absence['updated_at'] && $absence['updated_at'] != $absence['created_at'] ? date('d/m/Y H:i', strtotime($absence['updated_at'])) : '-') . '</td>
            </tr>';

            // Acumuladores
            $totalDays += $days;
        }

        // Fila de totales
        $html .= '
            <tr class="total-row">
                <td colspan="5"><strong>TOTAL</strong></td>
                <td colspan="4"><strong>' . $totalDays . ' días</strong></td>
            </tr>
        </tbody>
        </table>';

        // Pie de página final
        $html .= '
        <div style="margin-top: 20px; font-size: 8px; color: #666; text-align: center;">
            <p><em>Documento generado automáticamente por el sistema de gestión.</em></p>
        </div>';

        return $html;
    }

    // =================================================================================
    // Enviar correo de respuesta de solicitud de ausencia
    // =================================================================================
    private function sendAbsenceResponseEmail($user, $absence, $status, $adminComments = null)
    {
        helper('email');
        $emailService = get_configured_email();
        $link = site_url('absences/view/' . $absence['id']);

        $statusText = $status === 'approved' ? 'aprobada' : 'rechazada';
        $subject = 'Tu solicitud de ausencia ha sido ' . $statusText;

        $company = $this->companyModel->getCompany();
        $companyName = $company ? $company['name'] : 'OtGest';

        // get_configured_email() ya configura el 'from' si existe en la BD
        $emailService->setTo($user['email']);
        $emailService->setSubject($subject);
        $intro = 'Hola, <b>' . esc($user['name']) . '</b>:<br>Tu solicitud de ausencia ha sido <strong>' . $statusText . '</strong>. Puedes revisar los detalles en la aplicación:';
        
        $content = '';
        if ($adminComments) {
            $content = '<p style="margin: 0 0 10px 0; font-size: 16px; color: #5a6a85; -webkit-text-fill-color: #5a6a85;"><strong>Comentarios del administrador:</strong><br>' . nl2br(esc($adminComments)) . '</p>';
        }

        $emailBody = view('emails/template', [
            'title' => 'Solicitud ' . ucfirst($statusText),
            'intro' => $intro,
            'content' => $content,
            'buttonText' => 'Ver Detalles',
            'buttonUrl' => $link,
            'companyName' => $companyName
        ]);

        $emailService->setMessage($emailBody);

        if (!$emailService->send()) {
            log_message('error', 'No se pudo enviar el correo de respuesta de ausencia a ' . $user['email']);
        }
    }

    // =================================================================================
    // Exportar ausencia individual a PDF
    // =================================================================================
    public function exportAbsencePdf($id)
    {
        $absence = $this->absenceModel->find($id);
        if (!$absence) {
            return redirect()->to('/absences/list')->with('errors', ['Solicitud no encontrada.']);
        }

        // Verificar permisos: solo el propietario o admin pueden exportar
        if (!has_permission('absences.manage') && $absence['user_id'] != session()->get('user_id')) {
            return redirect()->to('/absences/list')->with('errors', ['No tienes permiso para exportar esta solicitud.']);
        }

        // Configurar DomPDF
        $dompdf = new \Dompdf\Dompdf();
        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $dompdf->setOptions($options);

        // Generar HTML del PDF
        $html = $this->generateAbsencePdfHtml($absence);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Descargar el PDF
        $dompdf->stream('ausencia_' . $id . '_' . date('Y-m-d') . '.pdf', ['Attachment' => true]);
    }

    // =================================================================================
    // Generar HTML para el PDF de una ausencia individual
    // =================================================================================
    private function generateAbsencePdfHtml($absence)
    {
        // Obtener información de la empresa
        $company = $this->companyModel->getCompany();
        $companyName = $company ? $company['name'] : 'OtGest';

        // Obtener información del usuario
        $user = $this->usersModel->find($absence['user_id']);
        $absenceTypes = $this->absenceModel->getAbsenceTypes();
        $statusLabels = $this->absenceModel->getStatusLabels();

        // Calcular días de ausencia
        $days = $this->calculateWorkingDays($absence['start_date'], $absence['end_date'], $absence['type']);

        // Estilos CSS embebidos para el PDF
        $html = '<style>
            body { font-family: Arial, sans-serif; margin: 20px; font-size: 12px; line-height: 1.4; }
            .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #5d87ff; padding-bottom: 20px; }
            .header h1 { color: #5d87ff; margin: 0; font-size: 24px; }
            .header p { margin: 5px 0; color: #666; }
            .content { margin-bottom: 30px; }
            .section { margin-bottom: 25px; }
            .section h2 { color: #333; font-size: 16px; margin-bottom: 10px; border-bottom: 1px solid #ddd; padding-bottom: 5px; }
            .info-grid { display: table; width: 100%; margin-bottom: 15px; }
            .info-row { display: table-row; }
            .info-label { display: table-cell; width: 200px; font-weight: bold; padding: 8px 0; vertical-align: top; }
            .info-value { display: table-cell; padding: 8px 0; vertical-align: top; }
            .status-approved { color: #28a745; font-weight: bold; }
            .status-rejected { color: #dc3545; font-weight: bold; }
            .status-pending { color: #ffc107; font-weight: bold; }
            .status-cancelled { color: #6c757d; font-weight: bold; }
            .footer { margin-top: 40px; text-align: center; font-size: 10px; color: #666; border-top: 1px solid #ddd; padding-top: 20px; }
        </style>';

        // Cabecera
        $html .= '
        <div class="header">
            <h1>' . esc($companyName) . '</h1>
            <p>Reporte de Solicitud de Ausencia</p>
            <p>Generado el ' . date('d/m/Y H:i') . '</p>
        </div>';

        // Información de la solicitud
        $html .= '<div class="content">';

        // Sección de información general
        $html .= '<div class="section">
            <h2>Información General</h2>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">Solicitante:</div>
                    <div class="info-value">' . esc($user['name']) . '</div>
                </div>
                <div class="info-row">
                    <div class="info-label">DNI:</div>
                    <div class="info-value">' . esc($user['identification']) . '</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Tipo:</div>
                    <div class="info-value">' . esc($absenceTypes[$absence['type']] ?? $absence['type']) . '</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Fecha Solicitud:</div>
                    <div class="info-value">' . date('d/m/Y H:i', strtotime($absence['created_at'])) . '</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Fecha Inicio:</div>
                    <div class="info-value">' . date('d/m/Y', strtotime($absence['start_date'])) . (!empty($absence['start_time']) ? ' ' . date('H:i', strtotime($absence['start_time'])) : '') . '</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Fecha Fin:</div>
                    <div class="info-value">' . date('d/m/Y', strtotime($absence['end_date'])) . (!empty($absence['end_time']) ? ' ' . date('H:i', strtotime($absence['end_time'])) : '') . '</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Estado:</div>
                    <div class="info-value status-' . $absence['status'] . '">' . esc($statusLabels[$absence['status']]) . '</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Procesado por:</div>
                    <div class="info-value">' . esc($absence['processed_by_name'] ?? '-') . '</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Fecha procesado:</div>
                    <div class="info-value">' . ($absence['updated_at'] && $absence['updated_at'] != $absence['created_at'] ? date('d/m/Y H:i', strtotime($absence['updated_at'])) : '-') . '</div>
                </div>
            </div>
        </div>';

        // Sección de fechas
        $html .= '<div class="section">
            <h2>Periodo de Ausencia</h2>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">Fecha de Inicio:</div>
                    <div class="info-value">' . date('d/m/Y', strtotime($absence['start_date'])) . (!empty($absence['start_time']) ? ' ' . date('H:i', strtotime($absence['start_time'])) : '') . '</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Fecha de Fin:</div>
                    <div class="info-value">' . date('d/m/Y', strtotime($absence['end_date'])) . (!empty($absence['end_time']) ? ' ' . date('H:i', strtotime($absence['end_time'])) : '') . '</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Días Totales:</div>
                    <div class="info-value">' . $days . ' día(s)</div>
                </div>
            </div>
        </div>';

        // Sección de comentarios
        if (!empty($absence['comments'])) {
            $html .= '<div class="section">
                <h2>Comentarios del Solicitante</h2>
                <p style="margin: 0; padding: 15px; background: #f8f9fa; border-left: 4px solid #5d87ff; font-style: italic;">' . nl2br(esc($absence['comments'])) . '</p>
            </div>';
        }

        // Sección de comentarios del administrador (si existe)
        if (!empty($absence['admin_comments'])) {
            $html .= '<div class="section">
                <h2>Comentarios del Administrador</h2>
                <p style="margin: 0; padding: 15px; background: #fff3cd; border-left: 4px solid #ffc107; font-style: italic;">' . nl2br(esc($absence['admin_comments'])) . '</p>
            </div>';
        }

        // Sección de información de procesamiento
        if ($absence['processed_by']) {
            $processor = $this->usersModel->find($absence['processed_by']);
            $html .= '<div class="section">
                <h2>Información de Procesamiento</h2>
                <div class="info-grid">
                    <div class="info-row">
                        <div class="info-label">Procesado por:</div>
                        <div class="info-value">' . esc($processor ? $processor['name'] : 'Usuario desconocido') . '</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Fecha de Procesamiento:</div>
                        <div class="info-value">' . date('d/m/Y H:i', strtotime($absence['updated_at'])) . '</div>
                    </div>
                </div>
            </div>';
        }

        $html .= '</div>';

        // Pie de página
        $html .= '<div class="footer">
            <p>Documento generado automáticamente por el sistema de gestión de ausencias.</p>
            <p>' . esc($companyName) . ' - ' . date('Y') . '</p>
        </div>';

        return $html;
    }

    // =================================================================================
    // Descargar archivo adjunto
    // =================================================================================
    public function download($absenceId)
    {
        $userId = session()->get('user_id');
        $absence = $this->absenceModel->find($absenceId);

        if (!$absence) {
            return redirect()->to('/absences/list')->with('errors', ['Solicitud no encontrada.']);
        }

        // Verificar permisos: solo el propietario o admin pueden descargar el archivo
        if (!has_permission('absences.manage') && $absence['user_id'] != session()->get('user_id')) {
            return redirect()->to('/absences/list')->with('errors', ['No tienes permiso para descargar este archivo.']);
        }

        if (empty($absence['attachment'])) {
            return redirect()->to('/absences/list')->with('errors', ['No hay archivo adjunto en esta solicitud.']);
        }

        $filePath = WRITEPATH . $absence['attachment'];

        if (!is_file($filePath)) {
            return redirect()->to('/absences/list')->with('errors', ['Archivo no encontrado.']);
        }

        // Descargar archivo
        return $this->response->download($filePath, null)->setFileName('ausencia_' . $absenceId . '_' . basename($absence['attachment']));
    }

    // =================================================================================
    // Calcular días descontando fines de semana (solo laborales para vacaciones)
    // =================================================================================
    private function calculateWorkingDays($startDate, $endDate, $type = 'vacaciones')
    {
        $start = new \DateTime($startDate);
        $end = new \DateTime($endDate);
        
        // Si no es vacaciones, contamos días naturales
        if ($type !== 'vacaciones') {
            return $start->diff($end)->days + 1;
        }

        $days = 0;
        $current = clone $start;

        while ($current <= $end) {
            // N = 1 (Lunes) ... 7 (Domingo)
            if ($current->format('N') < 6) {
                $days++;
            }
            $current->modify('+1 day');
        }
        
        return $days;
    }
}
