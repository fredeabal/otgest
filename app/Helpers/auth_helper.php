<?php

if (!function_exists('has_permission')) {
    /**
     * Verifica si el usuario actual tiene el permiso requerido.
     * También retorna true si el usuario es administrador (rol 1).
     *
     * @param string|array $requiredPermissions Permiso o lista de permisos a comprobar.
     * @return bool
     */
    function has_permission($requiredPermissions): bool
    {
        $session = session();

        // Si no está logueado, obviamente no tiene permisos
        if (!$session->get('isLoggedIn')) {
            return false;
        }

        // El Administrador (rol 1) tiene acceso total
        if ($session->get('user_role') == 1) {
            return true;
        }

        // Obtener permisos del usuario
        $userPermissions = $session->get('user_permissions') ?? [];

        if (is_array($requiredPermissions)) {
            // Si pasamos un array, el usuario debe tener al menos uno de esos permisos
            return count(array_intersect($requiredPermissions, $userPermissions)) > 0;
        } else {
            // Si pasamos un string, verificar si existe en el array
            return in_array($requiredPermissions, $userPermissions);
        }
    }
}
