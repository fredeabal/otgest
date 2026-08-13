# Sistema de Gestión Empresarial - OtGest

## Descripción

OtGest es un sistema integral de gestión empresarial que permite a las empresas gestionar eficientemente sus recursos humanos, jornadas laborales, ausencias, gastos y documentos. El sistema está diseñado con una arquitectura moderna y segura, implementando las mejores prácticas de desarrollo web.

## Características Principales

### 🏢 Gestión de Empresa
- Configuración de datos de la empresa
- Personalización de información corporativa
- Gestión de configuraciones generales

### 👥 Gestión de Usuarios
- Sistema de autenticación seguro con hash de contraseñas
- Gestión de roles y permisos granulares
- Perfiles de usuario personalizables
- Control de acceso basado en roles (RBAC)

### ⏰ Control de Jornadas Laborales
- Registro de entrada y salida de empleados
- Control de descansos y pausas
- Cálculo automático de horas trabajadas
- Reportes de jornadas en PDF
- Exportación de datos personalizados

### 🏖️ Gestión de Ausencias
- Solicitud de ausencias con múltiples tipos:
  - Baja médica
  - Accidente laboral
  - Enfermedad
  - Maternidad/Paternidad
  - Fallecimiento familiar
  - Cuidado de familiares
  - Vacaciones
  - Permisos personales
  - Y más...
- Flujo de aprobación/rechazo
- Notificaciones por email
- Reportes detallados en PDF
- Cálculo automático de días de ausencia

### 💰 Gestión de Gastos
- Registro de gastos con justificantes
- Categorización de gastos
- Flujo de aprobación
- Subida de imágenes de recibos
- Reportes financieros en PDF
- Control de presupuestos

### 📄 Gestión de Documentos
- Sistema de envío y recepción de documentos
- Seguimiento de documentos
- Organización por categorías
- Control de versiones

## Tecnologías Utilizadas

- **Framework**: CodeIgniter 4.6.3
- **PHP**: 8.1+
- **Base de datos**: MySQL/MariaDB
- **Frontend**: Bootstrap 5, HTML5, CSS3, JavaScript
- **Generación de PDFs**: DomPDF 2.0
- **Autenticación**: Sistema personalizado con sesiones seguras
- **Iconografía**: [Solar Icons](https://icones.js.org/collection/solar) vía Iconify (Bold Duotone/Outline)
- **Testing**: PHPUnit 10.5+

## Requisitos del Sistema

- PHP 8.1 o superior
- MySQL 5.7+ o MariaDB 10.3+
- Extensiones PHP requeridas:
  - mbstring
  - intl
  - json
  - mysqlnd
  - xml
  - curl
  - gd (para procesamiento de imágenes)

## 🚀 Instalación en 1 Paso

En cualquier servidor **Debian 11/12** o **Ubuntu 20.04 / 22.04 / 24.04** limpio con acceso root (VPS o máquina virtual), ejecuta el siguiente comando en tu terminal:

```bash
bash <(curl -s https://raw.githubusercontent.com/fredeabal/otgest/main/install.sh)
```

El script se encargará automáticamente de todo el proceso de instalación y configuración del servidor web, dependencias y base de datos.

### 🔑 Credenciales por Defecto tras la Instalación
* **URL:** `http://TU_IP_O_DOMINIO`
* **Email:** `admin@demo.com`
* **Password:** `12345678`

### ⚙️ Configuración Post-Instalación Recomendada

Para el correcto funcionamiento de todas las características de OtGest, se recomienda configurar:

#### 1. 📧 Configuración de Correo (SMTP)
OtGest utiliza el servicio de correo para enviar notificaciones.
1. Ve a **Ajustes** (o en la sección de administración del sistema) en la barra lateral/menú.
2. Introduce los datos de tu servidor de correo (Host, Puerto, Usuario, Contraseña y tipo de encriptación TLS/SSL).

#### 🔧 Archivo de Configuración Global (.env)
El archivo `.env` se encuentra en la raíz de la instalación y almacena las variables principales del sistema (como la URL base del sitio `app.baseURL`, la ubicación de la base de datos SQLite y el entorno de ejecución). Puedes editarlo en cualquier momento desde la terminal mediante el comando:

```bash
nano /var/www/otgest/.env
```

---

## 🔄 Actualizar a la última versión

Para actualizar un servidor OtGest existente a la última versión disponible (sin perder tus datos, configuraciones o base de datos), simplemente ejecuta:

```bash
bash <(curl -s https://raw.githubusercontent.com/fredeabal/otgest/main/update.sh)
```

## Estructura del Proyecto

```
app/
├── Controllers/          # Controladores de la aplicación
│   ├── AbsenceController.php    # Gestión de ausencias
│   ├── AuthController.php       # Autenticación
│   ├── CompanyController.php    # Gestión de empresa
│   ├── DashboardController.php  # Panel principal
│   ├── DocumentsController.php  # Gestión de documentos
│   ├── ExpenseController.php    # Gestión de gastos
│   ├── RolesController.php      # Gestión de roles
│   ├── UsersController.php      # Gestión de usuarios
│   └── WorkDayController.php    # Control de jornadas
├── Models/              # Modelos de datos
│   ├── AbsenceModel.php
│   ├── CompanyModel.php
│   ├── DocumentsModel.php
│   ├── ExpenseModel.php
│   ├── RolesModel.php
│   ├── UsersModel.php
│   └── WorkDayModel.php
├── Views/               # Vistas de la aplicación
│   ├── absences/        # Vistas de ausencias
│   ├── auth/           # Vistas de autenticación
│   ├── company/        # Vistas de empresa
│   ├── dashboard/      # Panel principal
│   ├── documents/      # Vistas de documentos
│   ├── expenses/       # Vistas de gastos
│   ├── roles/          # Vistas de roles
│   ├── template/       # Plantillas base
│   ├── users/          # Vistas de usuarios
│   └── workdays/       # Vistas de jornadas
├── Config/             # Configuraciones
├── Database/           # Migraciones y seeders
├── Filters/            # Filtros de seguridad
└── Language/           # Archivos de idioma
```

## Funcionalidades por Rol

### Administrador
- Acceso completo a todas las funcionalidades
- Gestión de usuarios y roles
- Configuración de la empresa
- Aprobación de ausencias y gastos
- Generación de reportes globales

### Empleado
- Registro de jornadas laborales
- Solicitud de ausencias
- Registro de gastos
- Visualización de documentos personales
- Acceso a reportes propios

### Supervisor/Manager
- Aprobación de ausencias de su equipo
- Aprobación de gastos
- Visualización de reportes de equipo
- Gestión limitada de usuarios

## Seguridad

El sistema implementa múltiples capas de seguridad:

- **Protección CSRF**: Todos los formularios incluyen tokens CSRF
- **Validación de entrada**: Validación estricta de todos los datos
- **Escape de salida**: Prevención de XSS con `esc()`
- **Autenticación segura**: Hash de contraseñas con `password_hash()`
- **Control de sesiones**: Gestión segura de sesiones
- **Filtros de autorización**: Control de acceso basado en permisos granulares
- **Headers de seguridad**: Implementación de headers de seguridad HTTP

## Estructura de Permisos Granulares

El sistema utiliza una arquitectura de permisos modulares almacenados en formato JSON en la base de datos. Cada permiso sigue la nomenclatura `modulo.accion`.

### Módulos y Permisos Disponibles:

- **Jornadas**:
  - `workdays.clockin`: Permite al usuario fichar entrada/salida.
  - `workdays.records`: Permite ver el historial propio de fichajes.
  - `workdays.manage`: Permite gestionar y ver fichajes de otros usuarios (Supervisor/Admin).

- **Ausencias**:
  - `absences.request`: Permite solicitar nuevas ausencias.
  - `absences.list`: Permite ver el listado propio de ausencias.
  - `absences.manage`: Permite aprobar/rechazar y gestionar ausencias de terceros.

- **Gastos**:
  - `expenses.create`: Permite subir nuevos justificantes de gastos.
  - `expenses.my`: Permite ver el historial propio de gastos.
  - `expenses.manage`: Permite gestionar y aprobar gastos de otros usuarios.

- **Documentos**:
  - `documents.received`: Acceso a la bandeja de entrada de documentos.
  - `documents.sent`: Acceso a la lista de documentos enviados por el usuario.
  - `documents.send`: Permite realizar envíos individuales de documentos.
  - `documents.manage`: Permite realizar envíos masivos y gestionar la documentación global.

- **Administración**:
  - `admin.users`: Gestión completa de la base de usuarios.
  - `admin.roles`: Configuración de roles del sistema.
  - `admin.company`: Ajustes de configuración de la empresa.

### Implementación Técnica:

El control de acceso se realiza mediante el filtro `PermissionFilter.php`, que permite validar múltiples permisos en una sola ruta:
```php
$routes->get('path', 'Controller::method', ['filter' => 'permission:permiso1,permiso2']);
```
Independientemente de los permisos asignados, los usuarios con **ID de Rol 1 (Admin)** conservan acceso total a todas las secciones del sistema.

## Reportes y Exportación

El sistema genera reportes en PDF para:

- **Jornadas laborales**: Reportes individuales y globales
- **Ausencias**: Listados con filtros avanzados
- **Gastos**: Reportes financieros detallados
- **Documentos**: Seguimiento de documentos

Nombres de archivos PDF generados:
- Jornadas personales: `mis_jornadas_YYYY-MM-DD.pdf`
- Jornadas globales: `reporte_jornadas_YYYY-MM-DD.pdf`
- Ausencias personales: `mis_ausencias.pdf`
- Ausencias globales: `ausencias_YYYY-MM-DD.pdf`
- Gastos personales: `mis_gastos_YYYY-MM-DD.pdf`
- Gastos globales: `gestion_de_gastos_YYYY-MM-DD.pdf`

## API y Endpoints

El sistema utiliza rutas RESTful para todas las operaciones:

```
GET  /dashboard              # Panel principal
GET  /users                  # Lista de usuarios
POST /users/create           # Crear usuario
GET  /workdays               # Jornadas laborales
POST /workdays/start         # Iniciar jornada
POST /workdays/end           # Finalizar jornada
GET  /absences/request       # Solicitar ausencia
POST /absences/approve/{id}  # Aprobar ausencia
GET  /expenses               # Lista de gastos
POST /expenses/create        # Crear gasto
```

## Configuración de Email

Para habilitar las notificaciones por email:

1. Configurar SMTP en `app/Config/Email.php`
2. Establecer las credenciales del servidor de correo
3. Configurar las plantillas de email en las vistas correspondientes

## Desarrollo y Testing

### Ejecutar tests
```bash
composer test
```

### Modo de desarrollo
```bash
# Habilitar modo debug en .env
CI_ENVIRONMENT = development
```

### Logs
Los logs se almacenan en `writable/logs/` y pueden configurarse en `app/Config/Logger.php`

## Soporte

Para soporte técnico o consultas:
- Crear un issue en el repositorio
- Contactar al equipo de desarrollo

## Estándares de Desarrollo (Frontend)

Para mantener la base de código limpia, escalable y profesional, se deben seguir estrictamente las siguientes reglas:

### 🚫 Prohibición de Código Inline
- **NO utilizar etiquetas `<style>`** dentro de las vistas (`.php`). Todos los estilos personalizados deben añadirse a [`public/assets/css/custom.css`](file:///Users/esweb/Documents/OtgestApp/app/public/assets/css/custom.css).
- **NO utilizar etiquetas `<script>`** con lógica compleja dentro de las vistas. La lógica de JavaScript debe externalizarse a archivos `.js` en `public/assets/js/`.
- **NO utilizar estilos `inline`** (atributo `style="..."`) salvo para casos de posicionamiento dinámico crítico que no puedan resolverse con clases CSS.

### 📦 Gestión de Activos (JS/CSS)
- **CSS**: Usar variables CSS localizadas en `custom.css` para mantener la consistencia del tema oscuro.
- **JavaScript**:
  - Utilizar **Data Attributes** (`data-*`) en el HTML para pasar datos dinámicos de PHP a los archivos JS externos.
  - Las librerías globales (ApexCharts, FullCalendar, etc.) deben cargarse en el `footer.php` o `header.php`.
  - Los scripts específicos de un módulo deben residir en su propio archivo (ej. `absences.js`, `workdays.js`).
- **Iconografía**: Utilizar exclusivamente `iconify-icon` con el set de Solar Icons.

## Changelog

### Versión Actual
- ✅ Sistema de gestión de jornadas laborales
- ✅ Gestión completa de ausencias
- ✅ Sistema de gastos con aprobaciones
- ✅ Gestión de usuarios y roles
- ✅ **Migración Completa a Permisos Granulares y Modulares**
- ✅ Generación de reportes PDF
- ✅ Sistema de autenticación seguro con soporte multirrol (Supervisor/Admin/Usuario)
- ✅ Panel de administración completo
- ✅ Sistema de gestión de gastos
- ✅ Migración completa a Solar Icons (Bold Duotone)
- ✅ Estandarización de botones de acción con clase `.btn-icon`


### Próximas Funcionalidades
- 🔄 Notificaciones push
- 🔄 API REST completa
- 🔄 Aplicación móvil
- 🔄 Integración con sistemas externos
- 🔄 Dashboard analítico avanzado

### Interfaz y Diseño
- **Iconos**: Se utiliza el set **Solar Icons** en estilo *Bold Duotone* para la mayoría de acciones y *Outline* para la navegación.
- **Botones de Acción**: Se ha estandarizado el uso de la clase `.btn-icon` en toda la aplicación para garantizar que los botones con icono sean cuadrados (40x38px) y tengan el icono perfectamente centrado.
- **Calendario del Dashboard**: Mejoras visuales en las divisiones y cabeceras para una correcta visualización de transparencias tanto en modo claro como oscuro (theme Bootstrap 5).

**Desarrollado con ❤️ usando CodeIgniter 4**
