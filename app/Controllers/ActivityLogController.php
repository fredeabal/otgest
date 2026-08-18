<?php

namespace App\Controllers;

use App\Models\ActivityLogModel;
use App\Models\UsersModel;
use App\Models\CompanyModel;

class ActivityLogController extends BaseController
{
    protected $activityLogModel;
    protected $usersModel;
    protected $companyModel;

    public function __construct()
    {
        $this->activityLogModel = new ActivityLogModel();
        $this->usersModel = new UsersModel();
        $this->companyModel = new CompanyModel();
    }

    public function index()
    {
        if (!has_permission('admin.logs')) {
            return redirect()->to('/')->with('errors', ['No tienes permisos para acceder a esta sección.']);
        }

        $query = $this->activityLogModel->select('activity_logs.*, users.name as user_name')
            ->join('users', 'users.id = activity_logs.user_id', 'left');

        // Filtros
        if ($this->request->getGet('user_id')) {
            $query->where('activity_logs.user_id', $this->request->getGet('user_id'));
        }

        if ($this->request->getGet('module')) {
            $query->where('activity_logs.module', $this->request->getGet('module'));
        }

        if ($this->request->getGet('date_from')) {
            $query->where('DATE(activity_logs.created_at) >=', $this->request->getGet('date_from'));
        }

        if ($this->request->getGet('date_to')) {
            $query->where('DATE(activity_logs.created_at) <=', $this->request->getGet('date_to'));
        }

        $data['logs'] = $query->orderBy('activity_logs.created_at', 'DESC')->paginate(20);
        $data['pager'] = $this->activityLogModel->pager;
        
        $data['users'] = $this->usersModel->orderBy('name', 'ASC')->findAll();
        $data['modules'] = $this->activityLogModel->select('module')->distinct()->findAll();
        
        $data['title'] = 'Registro de Actividad';

        echo view('template/header', $data);
        echo view('logs/index', $data);
        echo view('template/footer');
    }

    public function exportPdf()
    {
        if (!has_permission('admin.logs')) {
            return redirect()->to('/')->with('errors', ['No tienes permisos para acceder a esta sección.']);
        }

        $query = $this->activityLogModel->select('activity_logs.*, users.name as user_name')
            ->join('users', 'users.id = activity_logs.user_id', 'left');

        // Filtros
        if ($this->request->getGet('user_id')) {
            $query->where('activity_logs.user_id', $this->request->getGet('user_id'));
        }

        if ($this->request->getGet('module')) {
            $query->where('activity_logs.module', $this->request->getGet('module'));
        }

        if ($this->request->getGet('date_from')) {
            $query->where('DATE(activity_logs.created_at) >=', $this->request->getGet('date_from'));
        }

        if ($this->request->getGet('date_to')) {
            $query->where('DATE(activity_logs.created_at) <=', $this->request->getGet('date_to'));
        }

        $logs = $query->orderBy('activity_logs.created_at', 'DESC')->findAll();

        // Configurar DomPDF
        $options = new \Dompdf\Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->setPaper('A4', 'landscape');

        // Generar HTML del PDF
        $html = $this->generatePdfHtml($logs);

        $dompdf->loadHtml($html);
        $dompdf->render();

        // Descargar el PDF
        $dompdf->stream('registro_actividad_' . date('Y-m-d') . '.pdf', ['Attachment' => true]);
    }

    private function generatePdfHtml($logs)
    {
        // Obtener información de la empresa
        $company = $this->companyModel->getCompany();
        $companyName = $company ? $company['name'] : 'OtGest';

        // Estilos CSS embebidos para el PDF (mismo diseño que otros reportes)
        $html = '<style>
            body { font-family: Arial, sans-serif; margin: 15px; font-size: 10px; }
            h1 { color: #333; text-align: center; margin-bottom: 20px; font-size: 14px; }
            table { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 9px; }
            th, td { border: 1px solid #ddd; padding: 4px; text-align: left; }
            th { background-color: #f5f5f5; font-weight: bold; font-size: 9px; }
            tr:nth-child(even) { background-color: #f9f9f9; }
            .header-info { margin-bottom: 15px; font-size: 9px; }
        </style>';

        $html .= '
        <div class="header-info">
            <strong>' . esc($companyName) . '</strong><br>
            <strong>Registro de Actividad</strong><br>
            <strong>Generado por:</strong> ' . esc(session()->get('user_name')) . '<br>
            <strong>Fecha:</strong> ' . date('d/m/Y H:i:s') . '<br>
            <strong>Registros:</strong> ' . count($logs) . '<br>
        </div>';

        $html .= '<table>
            <thead>
                <tr>
                    <th>Fecha y Hora</th>
                    <th>Usuario</th>
                    <th>Módulo</th>
                    <th>Acción</th>
                    <th>Descripción</th>
                    <th>IP</th>
                </tr>
            </thead>
            <tbody>';

        if (empty($logs)) {
            $html .= '<tr><td colspan="6" style="text-align: center;">No hay registros de actividad.</td></tr>';
        } else {
            foreach ($logs as $log) {
                $html .= '<tr>
                    <td>' . date('d/m/Y H:i:s', strtotime($log['created_at'])) . '</td>
                    <td>' . esc($log['user_name'] ?? 'Sistema / Anónimo') . '</td>
                    <td>' . esc($log['module']) . '</td>
                    <td>' . esc($log['action']) . '</td>
                    <td>' . esc($log['description']) . '</td>
                    <td>' . esc($log['ip_address']) . '</td>
                </tr>';
            }
        }

        $html .= '
            </tbody>
        </table>';

        // Pie de página final
        $html .= '
        <div style="margin-top: 20px; font-size: 8px; color: #666; text-align: center;">
            <p><em>Documento generado automáticamente por el sistema de gestión.</em></p>
        </div>';

        return $html;
    }
}
