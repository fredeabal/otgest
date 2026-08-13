<?php
// =================================================================================
// Filtro: PermissionFilter
// =================================================================================

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class PermissionFilter implements FilterInterface
{
    // Verifica si el usuario tiene el permiso requerido
    public function before(RequestInterface $request, $arguments = null)
    {
        // Verificar si el usuario está logueado
        $session = session();
        if (! $session->get('isLoggedIn')) {
            return redirect()->to('/login');
        }
        
        helper('auth');
        
        // Convertir argumentos del filtro a array y limpiar espacios
        $requiredPermissions = is_string($arguments) ? array_map('trim', explode(',', $arguments)) : ($arguments ?? []);
        
        // Si no se pasaron argumentos, solo requerir estar logueado
        if (empty($requiredPermissions)) {
            return;
        }

        if (!has_permission($requiredPermissions)) {
            return redirect()->back()->with('errors', ['No tienes permiso para acceder a esta sección.']);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No se requiere lógica después de la petición
    }
} 