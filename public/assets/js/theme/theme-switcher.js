// =================================================================================
// Cambio de Tema Oscuro/Claro con Persistencia Mejorado
// =================================================================================

// Función para aplicar el tema
function applyTheme(theme) {
    document.documentElement.setAttribute('data-bs-theme', theme);
    localStorage.setItem('theme', theme);

    // Sincronizar visualmente los botones activos (opcional)
    document.querySelectorAll('.dark-layout, .light-layout').forEach(btn => btn.classList.remove('active'));
    if (theme === 'dark') {
        document.querySelectorAll('.dark-layout').forEach(btn => btn.classList.add('active'));
    } else {
        document.querySelectorAll('.light-layout').forEach(btn => btn.classList.add('active'));
    }
}

// Función para obtener el tema preferido
function getPreferredTheme() {
    // Usamos el tema del usuario de la sesión
    const userTheme = document.documentElement.getAttribute('data-user-theme');
    if (userTheme) {
        return userTheme;
    }
    // Fallback al sistema
    return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
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