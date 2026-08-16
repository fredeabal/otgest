<?php
// =================================================================================
// Controlador: ExpenseController
// =================================================================================

namespace App\Controllers;

use App\Models\ExpenseModel;
use App\Models\UsersModel;
use App\Models\CompanyModel;
use CodeIgniter\Controller;
use CodeIgniter\I18n\Time;
use Dompdf\Dompdf;
use Dompdf\Options;

class ExpenseController extends BaseController
{
    protected $expenseModel;
    protected $usersModel;
    protected $companyModel;

    public function __construct()
    {
        // Instanciar modelos
        $this->expenseModel = new ExpenseModel();
        $this->usersModel = new UsersModel();
        $this->companyModel = new CompanyModel();
    }

    // =================================================================================
    // Mostrar formulario de solicitud de gasto
    // =================================================================================
    public function create()
    {
        $data['title'] = 'Solicitar justificación de gasto';
        echo view('template/header', $data);
        echo view('expenses/create', $data);
        echo view('template/footer');
    }

    // =================================================================================
    // Procesar solicitud de gasto (POST)
    // =================================================================================
    public function store()
    {
        // Reglas de validación
        $rules = [
            'reason' => [
                'label' => 'motivo',
                'rules' => 'required|min_length[5]|max_length[100]'
            ],
            'receipt_image' => [
                'label' => 'imagen del recibo',
                'rules' => 'uploaded[receipt_image]|max_size[receipt_image,2048]|mime_in[receipt_image,image/jpg,image/jpeg,image/png,image/webp,application/pdf]'
            ],
            'amount' => [
                'label' => 'importe',
                'rules' => 'required|decimal|greater_than[0]'
            ],
            'category' => [
                'label' => 'categoría',
                'rules' => 'required|max_length[100]'
            ],
            'expense_date' => [
                'label' => 'fecha del gasto',
                'rules' => 'required|valid_date'
            ]
        ];

        // Validar datos
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Procesar imagen del recibo
        $receiptImage = $this->request->getFile('receipt_image');
        $imageName = null;

        if ($receiptImage && $receiptImage->isValid()) {
            // Generar nombre único para la imagen
            $imageName = $receiptImage->getRandomName();
            $userId = session()->get('user_id');
            $uploadPath = WRITEPATH . 'uploads/receipts/' . $userId . '/';

            // Crear directorio si no existe
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            // Mover archivo
            $receiptImage->move($uploadPath, $imageName);
        }

        // Guardar gasto
        $this->expenseModel->save([
            'user_id' => session()->get('user_id'),
            'reason' => $this->request->getPost('reason'),
            'receipt_image' => $imageName,
            'amount' => $this->request->getPost('amount') ?: null,
            'category' => $this->request->getPost('category') ?: null,
            'expense_date' => $this->request->getPost('expense_date'),
            'status' => 'pending',
            'created_at' => Time::now('Europe/Madrid', 'es_ES'),
        ]);

        // Redirigir con mensaje de éxito
        return redirect()->to('/expenses/my-expenses')->with('success', 'Justificación de gasto solicitada correctamente.');
    }

    // =================================================================================
    // Listado de gastos del usuario actual
    // =================================================================================
    public function my()
    {
        $userId = session()->get('user_id');
        $query = $this->expenseModel->where('user_id', $userId);

        // Filtros de fecha
        if ($this->request->getGet('date_from')) {
            $query->where('expense_date >=', $this->request->getGet('date_from'));
        }

        if ($this->request->getGet('date_to')) {
            $query->where('expense_date <=', $this->request->getGet('date_to'));
        }

        // Filtro de estado (default: pending)
        $status = $this->request->getGet('status') ?: 'pending';
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $data['expenses'] = $query->orderBy('created_at', 'DESC')->paginate(10);
        $data['pager'] = $this->expenseModel->pager;
        $data['title'] = 'Mis justificaciones de gastos';
        $data['current_status'] = $status;

        echo view('template/header', $data);
        echo view('expenses/my_expenses', $data);
        echo view('template/footer');
    }

    // =================================================================================
    // Gestión de gastos (para admin/aprobadores) - con filtros
    // =================================================================================
    public function manage()
    {
        // Filtros
        $userId = $this->request->getGet('user_id');
        $dateFrom = $this->request->getGet('date_from');
        $dateTo = $this->request->getGet('date_to');
        $status = $this->request->getGet('status') ?: 'pending'; // Default: pending

        $query = $this->expenseModel->select('expenses.*, users.name as user_name, users.identification as user_identification')
            ->join('users', 'users.id = expenses.user_id', 'left');

        // Aplicar filtro de estado
        if ($status !== 'all') {
            $query->where('expenses.status', $status);
        }

        // Aplicar otros filtros
        if (!empty($userId)) {
            $query->where('expenses.user_id', $userId);
        }
        if (!empty($dateFrom)) {
            $query->where('expenses.expense_date >=', $dateFrom);
        }
        if (!empty($dateTo)) {
            $query->where('expenses.expense_date <=', $dateTo);
        }

        $data['expenses'] = $query->orderBy('expenses.created_at', 'ASC')
            ->paginate(10);
        $data['pager'] = $this->expenseModel->pager;
        $data['title'] = 'Gestión de justificaciones de gastos';
        $data['current_status'] = $status;
        $data['users'] = $this->usersModel->findAll();

        echo view('template/header', $data);
        echo view('expenses/manage', $data);
        echo view('template/footer');
    }

    // =================================================================================
    // Aprobar gasto
    // =================================================================================
    public function approve($id)
    {
        $expense = $this->expenseModel->find($id);
        if (!$expense) {
            return redirect()->to('/expenses/manage')->with('errors', ['Gasto no encontrado.']);
        }

        if ($expense['status'] !== 'pending') {
            return redirect()->to('/expenses/manage')->with('errors', ['Este gasto ya ha sido procesado.']);
        }

        // Actualizar gasto
        $this->expenseModel->update($id, [
            'status' => 'approved',
            'approved_by' => session()->get('user_id'),
            'approved_at' => Time::now('Europe/Madrid', 'es_ES'),
            'updated_at' => Time::now('Europe/Madrid', 'es_ES'),
        ]);

        return redirect()->to('/expenses/manage')->with('success', 'Gasto aprobado correctamente.');
    }

    // =================================================================================
    // Rechazar gasto
    // =================================================================================
    public function reject($id)
    {
        $expense = $this->expenseModel->find($id);
        if (!$expense) {
            return redirect()->to('/expenses/manage')->with('errors', ['Gasto no encontrado.']);
        }

        if ($expense['status'] !== 'pending') {
            return redirect()->to('/expenses/manage')->with('errors', ['Este gasto ya ha sido procesado.']);
        }

        // Actualizar gasto
        $this->expenseModel->update($id, [
            'status' => 'rejected',
            'approved_by' => session()->get('user_id'),
            'approved_at' => Time::now('Europe/Madrid', 'es_ES'),
            'updated_at' => Time::now('Europe/Madrid', 'es_ES'),
        ]);

        return redirect()->to('/expenses/manage')->with('success', 'Gasto rechazado correctamente.');
    }

    // =================================================================================
    // Ver detalle de gasto
    // =================================================================================
    public function view($id)
    {
        $expense = $this->expenseModel->select('expenses.*, users.name as user_name, users.identification as user_identification, approver.name as approver_name')
            ->join('users', 'users.id = expenses.user_id', 'left')
            ->join('users as approver', 'approver.id = expenses.approved_by', 'left')
            ->find($id);

        if (!$expense) {
            return redirect()->to('/expenses/my-expenses')->with('errors', ['Gasto no encontrado.']);
        }

        // Verificar permisos (solo el propietario o admin puede ver)
        $userId = session()->get('user_id');
        if ($expense['user_id'] != $userId && !has_permission('expenses.manage')) {
            return redirect()->to('/expenses/my-expenses')->with('errors', ['No tienes permisos para ver este gasto.']);
        }

        $data['expense'] = $expense;
        $data['title'] = 'Detalle de justificación de gasto';

        echo view('template/header', $data);
        echo view('expenses/view', $data);
        echo view('template/footer');
    }
    // =================================================================================
    // Editar gasto
    // =================================================================================
    public function edit($id)
    {
        $expense = $this->expenseModel->find($id);
        
        if (!$expense) {
            return redirect()->to('/expenses/my-expenses')->with('errors', ['Gasto no encontrado.']);
        }

        // Verificar permisos y estado
        $userId = session()->get('user_id');
        if ($expense['user_id'] != $userId) {
            return redirect()->to('/expenses/my-expenses')->with('errors', ['No tienes permisos para editar este gasto.']);
        }
        if ($expense['status'] !== 'pending') {
            return redirect()->to('/expenses/my-expenses')->with('errors', ['Solo se pueden editar justificaciones pendientes.']);
        }

        $data['expense'] = $expense;
        $data['title'] = 'Editar justificación de gasto';

        echo view('template/header', $data);
        echo view('expenses/edit', $data);
        echo view('template/footer');
    }

    // =================================================================================
    // Actualizar gasto (POST)
    // =================================================================================
    public function update($id)
    {
        $expense = $this->expenseModel->find($id);
        if (!$expense) {
            return redirect()->to('/expenses/my-expenses')->with('errors', ['Gasto no encontrado.']);
        }

        // Verificar permisos y estado
        $userId = session()->get('user_id');
        if ($expense['user_id'] != $userId) {
            return redirect()->to('/expenses/my-expenses')->with('errors', ['No tienes permisos para editar este gasto.']);
        }
        if ($expense['status'] !== 'pending') {
            return redirect()->to('/expenses/my-expenses')->with('errors', ['Solo se pueden editar justificaciones pendientes.']);
        }

        // Reglas de validación
        $rules = [
            'reason' => [
                'label' => 'motivo',
                'rules' => 'required|min_length[5]|max_length[100]'
            ],
            'amount' => [
                'label' => 'importe',
                'rules' => 'required|decimal|greater_than[0]'
            ],
            'category' => [
                'label' => 'categoría',
                'rules' => 'required|max_length[100]'
            ],
            'expense_date' => [
                'label' => 'fecha del gasto',
                'rules' => 'required|valid_date'
            ]
        ];

        // Validar imagen solo si se sube una nueva
        $receiptImage = $this->request->getFile('receipt_image');
        if ($receiptImage && $receiptImage->isValid()) {
            $rules['receipt_image'] = [
                'label' => 'imagen del recibo',
                'rules' => 'uploaded[receipt_image]|max_size[receipt_image,2048]|mime_in[receipt_image,image/jpg,image/jpeg,image/png,image/webp,application/pdf]'
            ];
        }

        // Validar datos
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $updateData = [
            'reason' => $this->request->getPost('reason'),
            'amount' => $this->request->getPost('amount') ?: null,
            'category' => $this->request->getPost('category') ?: null,
            'expense_date' => $this->request->getPost('expense_date'),
            'updated_at' => Time::now('Europe/Madrid', 'es_ES'),
        ];

        // Procesar nueva imagen si existe
        if ($receiptImage && $receiptImage->isValid()) {
            $imageName = $receiptImage->getRandomName();
            $uploadPath = WRITEPATH . 'uploads/receipts/' . $userId . '/';

            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }
            $receiptImage->move($uploadPath, $imageName);

            // Borrar imagen anterior si existía
            if ($expense['receipt_image'] && file_exists($uploadPath . $expense['receipt_image'])) {
                unlink($uploadPath . $expense['receipt_image']);
            }
            $updateData['receipt_image'] = $imageName;
        }

        $this->expenseModel->update($id, $updateData);

        return redirect()->to('/expenses/my-expenses')->with('success', 'Justificación de gasto actualizada correctamente.');
    }

    // =================================================================================
    // Eliminar gasto
    // =================================================================================
    public function delete($id)
    {
        $expense = $this->expenseModel->find($id);
        if (!$expense) {
            return redirect()->to('/expenses/my-expenses')->with('errors', ['Gasto no encontrado.']);
        }

        // Verificar permisos y estado
        $userId = session()->get('user_id');
        if ($expense['user_id'] != $userId) {
            return redirect()->to('/expenses/my-expenses')->with('errors', ['No tienes permisos para eliminar este gasto.']);
        }
        if ($expense['status'] !== 'pending') {
            return redirect()->to('/expenses/my-expenses')->with('errors', ['Solo se pueden eliminar justificaciones pendientes.']);
        }

        // Borrar imagen
        if ($expense['receipt_image']) {
            $imagePath = WRITEPATH . 'uploads/receipts/' . $userId . '/' . $expense['receipt_image'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        $this->expenseModel->delete($id);

        return redirect()->to('/expenses/my-expenses')->with('success', 'Justificación de gasto eliminada correctamente.');
    }

    // =================================================================================
    // Exportar gastos a PDF
    // =================================================================================
    public function exportPendingPdf()
    {
        // Aplicar los mismos filtros que en manage
        $userId = $this->request->getGet('user_id');
        $dateFrom = $this->request->getGet('date_from');
        $dateTo = $this->request->getGet('date_to');
        $status = $this->request->getGet('status') ?: 'pending';

        $query = $this->expenseModel->select('expenses.*, users.name as user_name, users.identification as user_identification, processor.name as processed_by_name')
            ->join('users', 'users.id = expenses.user_id', 'left')
            ->join('users as processor', 'processor.id = expenses.approved_by', 'left');

        // Aplicar filtro de estado
        if ($status !== 'all') {
            $query->where('expenses.status', $status);
        }

        // Aplicar otros filtros
        if (!empty($userId)) {
            $query->where('expenses.user_id', $userId);
        }
        if (!empty($dateFrom)) {
            $query->where('expenses.expense_date >=', $dateFrom);
        }
        if (!empty($dateTo)) {
            $query->where('expenses.expense_date <=', $dateTo);
        }

        $expenses = $query->orderBy('expenses.created_at', 'ASC')->findAll();

        // Configurar DomPDF
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);

        // Generar HTML del PDF
        $html = $this->generateExpensesPdfHtml($expenses, $status);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        // Descargar el PDF
        $dompdf->stream('gestion_de_gastos_' . date('Y-m-d') . '.pdf', ['Attachment' => true]);
    }

    // =================================================================================
    // Generar HTML para el PDF de gastos
    // =================================================================================
    private function generateExpensesPdfHtml($expenses, $status = 'pending')
    {
        // Obtener información de la empresa
        $company = $this->companyModel->getCompany();
        $companyName = $company ? $company['name'] : 'OtGest';

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
            'approved' => 'Aprobados',
            'rejected' => 'Rechazados',
            'all' => 'Todos'
        ];
        $html .= '
        <div class="header-info">
            <strong>' . esc($companyName) . '</strong><br>
            <strong>Reporte de Gastos ' . ($statusText[$status] ?? 'Filtrados') . '</strong><br>
            <strong>Generado por:</strong> ' . esc(session()->get('user_name')) . '<br>
            <strong>Fecha:</strong> ' . date('d/m/Y H:i:s') . '<br>
            <strong>Registros:</strong> ' . count($expenses) . '<br>
        </div>';

        // Tabla principal
        $html .= '<table>
            <thead>
                <tr>
                    <th>Solicitante</th>
                    <th>DNI</th>
                    <th>Fecha Solicitud</th>
                    <th>Fecha del Gasto</th>
                    <th>Categoría</th>
                    <th>Estado</th>
                    <th>Revisado por</th>
                    <th>Fecha Revisión</th>
                    <th>Importe</th>
                </tr>
            </thead>
            <tbody>';

        // Variables para totales
        $totalAmount = 0;

        // Recorrer gastos
        foreach ($expenses as $expense) {
            // Determinar texto del estado
            $statusText = '';
            switch ($expense['status']) {
                case 'pending':
                    $statusText = 'Pendiente';
                    break;
                case 'approved':
                    $statusText = 'Aprobado';
                    break;
                case 'rejected':
                    $statusText = 'Rechazado';
                    break;
                default:
                    $statusText = ucfirst($expense['status']);
            }

            $html .= '<tr>
                <td>' . esc($expense['user_name']) . '</td>
                <td>' . esc($expense['user_identification']) . '</td>
                <td>' . date('d/m/Y H:i', strtotime($expense['created_at'])) . '</td>
                <td>' . date('d/m/Y', strtotime($expense['expense_date'])) . '</td>
                <td>' . esc($expense['category'] ?: '-') . '</td>
                <td>' . $statusText . '</td>
                <td>' . esc($expense['processed_by_name'] ?? '-') . '</td>
                <td>' . ($expense['status'] != 'pending' ? date('d/m/Y H:i', strtotime($expense['updated_at'] ?? $expense['created_at'])) : '-') . '</td>
                <td>' . ($expense['amount'] ? number_format($expense['amount'], 2, ',', '.') . ' €' : '-') . '</td>
            </tr>';

            // Acumuladores
            $totalAmount += $expense['amount'] ?? 0;
        }

        // Fila de totales
        $html .= '
            <tr class="total-row">
                <td colspan="8"><strong>TOTAL</strong></td>
                <td><strong>' . number_format($totalAmount, 2, ',', '.') . ' €</strong></td>
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
    // Exportar mis gastos a PDF
    // =================================================================================
    public function exportMyPdf()
    {
        $userId = session()->get('user_id');

        // Aplicar los mismos filtros que en my()
        $dateFrom = $this->request->getGet('date_from');
        $dateTo = $this->request->getGet('date_to');
        $status = $this->request->getGet('status') ?: 'pending';

        $query = $this->expenseModel->select('expenses.*, processor.name as processed_by_name')
            ->join('users as processor', 'processor.id = expenses.approved_by', 'left')
            ->where('expenses.user_id', $userId);

        // Aplicar filtros
        if (!empty($dateFrom)) {
            $query->where('expense_date >=', $dateFrom);
        }
        if (!empty($dateTo)) {
            $query->where('expense_date <=', $dateTo);
        }
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $expenses = $query->orderBy('created_at', 'DESC')->findAll();

        // Configurar DomPDF
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);

        // Generar HTML del PDF
        $html = $this->generateMyExpensesPdfHtml($expenses, $status);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        // Descargar el PDF
        $dompdf->stream('mis_gastos_' . date('Y-m-d') . '.pdf', ['Attachment' => true]);
    }

    // =================================================================================
    // Generar HTML para el PDF de mis gastos
    // =================================================================================
    private function generateMyExpensesPdfHtml($expenses, $status = 'pending')
    {
        // Obtener información de la empresa
        $company = $this->companyModel->getCompany();
        $companyName = $company ? $company['name'] : 'OtGest';

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
            'pending' => 'Pendientes',
            'approved' => 'Aprobados',
            'rejected' => 'Rechazados',
            'all' => 'Todos'
        ];
        $html .= '
        <div class="header-info">
            <strong>' . esc($companyName) . '</strong><br>
            <strong>Reporte de Mis Gastos ' . ($statusText[$status] ?? 'Filtrados') . '</strong><br>
            <strong>Generado por:</strong> ' . esc(session()->get('user_name')) . '<br>
            <strong>Fecha:</strong> ' . date('d/m/Y H:i:s') . '<br>
            <strong>Registros:</strong> ' . count($expenses) . '<br>
        </div>';

        // Tabla principal
        $html .= '<table>
            <thead>
                <tr>
                    <th>Fecha Solicitud</th>
                    <th>Fecha del Gasto</th>
                    <th>Categoría</th>
                    <th>Revisado por</th>
                    <th>Fecha Revisión</th>
                    <th>Estado</th>
                    <th>Importe</th>
                </tr>
            </thead>
            <tbody>';

        // Variables para totales
        $totalAmount = 0;

        // Recorrer gastos
        foreach ($expenses as $expense) {
            // Determinar texto del estado
            $statusText = '';
            switch ($expense['status']) {
                case 'pending':
                    $statusText = 'Pendiente';
                    break;
                case 'approved':
                    $statusText = 'Aprobado';
                    break;
                case 'rejected':
                    $statusText = 'Rechazado';
                    break;
                default:
                    $statusText = ucfirst($expense['status']);
            }

            $html .= '<tr>
                <td>' . date('d/m/Y H:i', strtotime($expense['created_at'])) . '</td>
                <td>' . date('d/m/Y', strtotime($expense['expense_date'])) . '</td>
                <td>' . esc($expense['category'] ?: '-') . '</td>
                <td>' . esc($expense['processed_by_name'] ?? '-') . '</td>
                <td>' . ($expense['status'] != 'pending' ? date('d/m/Y H:i', strtotime($expense['updated_at'] ?? $expense['created_at'])) : '-') . '</td>
                <td>' . $statusText . '</td>
                <td>' . ($expense['amount'] ? number_format($expense['amount'], 2, ',', '.') . ' €' : '-') . '</td>
            </tr>';

            // Acumuladores
            $totalAmount += $expense['amount'] ?? 0;
        }

        // Fila de totales
        $html .= '
            <tr class="total-row">
                <td colspan="6"><strong>TOTAL</strong></td>
                <td><strong>' . number_format($totalAmount, 2, ',', '.') . ' €</strong></td>
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
    // Servir imagen del recibo desde writable
    // =================================================================================
    public function receipt($userId, $filename)
    {
        $path = WRITEPATH . 'uploads/receipts/' . $userId . '/' . $filename;
        if (!is_file($path)) {
            // Retornar imagen por defecto o error
            return $this->response->setStatusCode(404)->setBody('Imagen no encontrada');
        }
        $mime = mime_content_type($path);
        return $this->response
            ->setContentType($mime)
            ->setHeader('Content-Length', filesize($path))
            ->setBody(file_get_contents($path));
    }
}
