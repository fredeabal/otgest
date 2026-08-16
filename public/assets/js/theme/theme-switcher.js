// =================================================================================
// Cambio de Tema Oscuro/Claro con Persistencia Mejorado
// =================================================================================

// Función para aplicar el tema
function applyTheme(theme) {
    let resolvedTheme = theme;
    if (theme === 'system') {
        resolvedTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    }
    
    document.documentElement.setAttribute('data-bs-theme', resolvedTheme);
    localStorage.setItem('theme', theme);

    // Sincronizar visualmente los botones activos (opcional)
    document.querySelectorAll('.dark-layout, .light-layout, .system-layout').forEach(btn => btn.classList.remove('active'));
    if (theme === 'dark') {
        document.querySelectorAll('.dark-layout').forEach(btn => btn.classList.add('active'));
    } else if (theme === 'light') {
        document.querySelectorAll('.light-layout').forEach(btn => btn.classList.add('active'));
    } else {
        document.querySelectorAll('.system-layout').forEach(btn => btn.classList.add('active'));
    }
}

// Función para obtener el tema preferido
function getPreferredTheme() {
    // 1. Prioridad: Tema del usuario de la sesión
    const userTheme = document.documentElement.getAttribute('data-user-theme');
    if (userTheme && userTheme !== '') {
        return userTheme;
    }
    // 2. Prioridad: Tema guardado localmente (para usuarios no logueados o antes de tener sesión)
    const localTheme = localStorage.getItem('theme');
    if (localTheme) {
        return localTheme;
    }
    // 3. Fallback al sistema
    return 'system';
}

// Aplicar tema al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    applyTheme(getPreferredTheme());

    // Asignar evento a TODOS los botones de cambio de tema
    document.querySelectorAll('.dark-layout').forEach(btn => {
        btn.addEventListener('click', () => applyTheme('dark'));
    });
    document.querySelectorAll('.light-layout').forEach(btn => {
        btn.addEventListener('click', () => applyTheme('light'));
    });
});

// Escuchar cambios en la preferencia del sistema
if (window.matchMedia) {
    try {
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
            // Solo aplicamos el cambio si el usuario no ha establecido una preferencia manual
            if (!localStorage.getItem('theme')) {
                applyTheme(e.matches ? 'dark' : 'light');
            }
        });
    } catch (err) {
        // Fallback para Safari antiguo
        window.matchMedia('(prefers-color-scheme: dark)').addListener(function(e) {
            if (!localStorage.getItem('theme')) {
                applyTheme(e.matches ? 'dark' : 'light');
            }
        });
    }
} 