# Estructura del Proyecto Reorganizado

## Arquitectura por Capas

### 1. **apis/** - Endpoints API
- **Responsabilidad**: Manejo de requests/responses HTTP
- **Contenido**: 
  - Validaciones básicas de entrada
  - Control de sesiones
  - Llamadas a lógica de negocio
  - Respuestas JSON
- **Ejemplo**: `apis/anfitrion/calendario.php`

### 2. **datos/** - Acceso a Datos
- **Responsabilidad**: Consultas SQL y operaciones con base de datos
- **Contenido**:
  - Clases con métodos para cada query
  - Sin lógica de negocio
  - Organizado por módulo (auth, anfitrion, etc.)
- **Ejemplo**: `datos/anfitrion/queries_calendario.php`

### 3. **negocio/** - Lógica de Negocio
- **Responsabilidad**: Reglas del sistema, validaciones, procesos
- **Contenido**:
  - Validaciones complejas
  - Cálculos y transformaciones
  - Flujo de procesos
  - Uso de clases de datos/
- **Ejemplo**: `negocio/anfitrion/calendario.php`

### 4. **presentacion/** - Interfaz Visual
- **Responsabilidad**: Estructura HTML, vistas, componentes
- **Contenido**:
  - Archivos PHP con HTML
  - Inclusión de recursos (CSS, JS)
  - Sin lógica de negocio compleja
- **Ejemplo**: `presentacion/anfitrion/dashboard.php`

### 5. **recursos/** - Recursos del Sistema
- **Responsabilidad**: Estilos, scripts, imágenes, assets
- **Contenido**:
  - CSS organizado por módulo
  - JavaScript por funcionalidad
  - Imágenes y archivos multimedia
  - Componentes reutilizables
- **Ejemplo**: `recursos/css/anfitrion/main.css`

## Reglas de Organización

### Nomenclatura
- **Archivos PHP**: `nombre_modulo.php` o `queries_modulo.php`
- **Clases**: `ClaseModulo` (ej: `QueriesCalendario`)
- **Variables**: descriptivas en español o inglés consistente

### Dependencias
- `apis/` → `negocio/` → `datos/`
- `presentacion/` → `recursos/`
- Nunca: `datos/` → `negocio/` o `presentacion/` → `negocio/`

### Rutas
- Usar rutas relativas consistentes (`../../`)
- Para frontend: rutas desde raíz del proyecto
- Para backend: rutas absolutas desde sistema de archivos

## Estructura de Carpetas Final

```
/
├── apis/
│   ├── admin/
│   ├── anfitrion/
│   │   ├── calendario.php
│   │   ├── propiedades.php
│   │   └── registrar_propiedad.php
│   ├── auth/
│   │   ├── login.php
│   │   └── registro.php
│   └── huesped/
├── config/
│   └── rutas.php
├── datos/
│   ├── admin/
│   ├── anfitrion/
│   │   ├── queries_calendario.php
│   │   ├── queries_propiedades.php
│   │   └── queries_registro_propiedad.php
│   ├── auth/
│   │   └── queries_auth.php
│   ├── huesped/
│   ├── mocks/
│   └── conexion.php
├── negocio/
│   ├── admin/
│   ├── anfitrion/
│   │   ├── calendario.php
│   │   ├── propiedades.php
│   │   └── registrar_propiedad.php
│   ├── auth/
│   │   ├── login.php
│   │   └── registro.php
│   └── huesped/
├── presentacion/
│   ├── admin/
│   ├── anfitrion/
│   └── huesped/
├── recursos/
│   ├── css/
│   │   ├── admin/
│   │   ├── anfitrion/
│   │   ├── auth/
│   │   ├── components/
│   │   ├── huesped/
│   │   ├── layouts/
│   │   ├── base.css
│   │   ├── main.css
│   │   └── variables.css
│   ├── js/
│   │   ├── admin/
│   │   ├── anfitrion/
│   │   ├── auth/
│   │   └── huesped/
│   ├── img/
│   ├── icons/
│   ├── navbar.php
│   └── user_profile_modal.php
├── index.php
├── set_session.php
├── test_db.php
├── test_roles.php
└── README_ESTRUCTURA.md
```

## Cambios Realizados

### 1. Separación de Responsabilidades
- Movida lógica SQL de `negocio/` a `datos/`
- Simplificados archivos `apis/` para solo manejo HTTP
- Centralizada lógica de negocio en `negocio/`

### 2. Organización de Datos
- Creadas clases `Queries*` para cada módulo
- Eliminada duplicación de queries
- Mejorada mantenibilidad

### 3. Unificación de Recursos
- Movidos archivos de `host/` a `anfitrion/`
- Actualizadas referencias en archivos de presentación
- Mantenida compatibilidad

### 4. Configuración Centralizada
- Creado `config/rutas.php` para gestión de rutas
- Facilitado mantenimiento futuro

## Próximos Pasos Recomendados

1. **Implementar autoloading** para clases
2. **Crear sistema de plantillas** para presentación
3. **Agregar validaciones** centralizadas
4. **Implementar logging** de errores
5. **Crear tests** para cada capa

## Notas Importantes

- **No se eliminó funcionalidad existente**
- **No se alteró diseño visual**
- **Todas las rutas fueron actualizadas para mantener funcionamiento**
- **La estructura es escalable y mantenible**

Esta organización sigue principios SOLID y separación de preocupaciones, facilitando el desarrollo futuro y mantenimiento del proyecto.