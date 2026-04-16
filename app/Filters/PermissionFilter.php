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
        // Permitir acceso total a los admin (rol 1)
        if ($session->get('user_role') == 1) {
            return; // acceso permitido
        }
        // Convertir argumentos del filtro a array y limpiar espacios
        $requiredPermissions = is_string($arguments) ? array_map('trim', explode(',', $arguments)) : ($arguments ?? []);
        $userPermissions = $session->get('user_permissions') ?? [];
        // Si no tiene el permiso, redirigir al usuario a la página anterior con un mensaje de error
        if (empty($requiredPermissions) || empty(array_intersect($requiredPermissions, $userPermissions))) {
            return redirect()->back()->with('errors', ['No tienes permiso para acceder a esta sección.']);
        }
        // Si tiene el permiso, permite el acceso
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No se requiere lógica después de la petición
    }
} 