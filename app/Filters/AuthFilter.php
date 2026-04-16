<?php
// =================================================================================
// Filtro: AuthFilter
// =================================================================================

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthFilter implements FilterInterface
{
    // Este filtro verifica que el usuario esté autenticado
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        if (! $session->get('isLoggedIn')) {
            // Redirigir a login si no está autenticado
            return redirect()->to('/login');
        }
        // Si está autenticado, permitir acceso
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No se requiere lógica después de la petición
    }
} 