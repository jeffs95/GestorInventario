# PacaManager

Sistema de gestión de inventario y ventas para negocios de **pacas** (ropa y calzado de segunda mano importado) desarrollado en Guatemala.

---

## ¿Qué es PacaManager?

PacaManager digitaliza el flujo completo de un negocio de pacas: desde la compra de un lote hasta la venta de cada zapato individual, pasando por la preparación, fotografiado y asignación de precios. Está pensado para **dueños con una o varias sucursales** y sus **encargados de tienda**.

---

## Características principales

### Operaciones
| Módulo | Descripción |
|---|---|
| **Lotes de compra** | Registro de cada paca comprada con proveedor, fecha, costo y destino |
| **Aperturas** | Conteo físico del contenido del lote clasificado por tipo (Regular, Primera Lavado, Primera Lustre) |
| **Preparación** | Generación masiva de códigos de barras → impresión de hoja de etiquetas → asignación de precio por escaneo |
| **Fotos** | Captura de múltiples fotos por zapato (hasta 6) directamente desde la cámara del dispositivo |

### Inventario
| Módulo | Descripción |
|---|---|
| **Ver Inventario** | Vista acordeón agrupada por lote con filtros avanzados, valor por lote y exportación CSV |
| **Registrar venta** | Búsqueda por código (manual, lector físico o cámara) con confirmación SweetAlert2 |

### Gestión *(solo dueño)*
| Módulo | Descripción |
|---|---|
| **Sucursales** | Alta y administración de puntos de venta |
| **Proveedores** | Catálogo de proveedores de pacas |
| **Usuarios** | Creación de encargados con restricción de sucursal |
| **Categorías y Tipos** | Configuración de categorías (Zapatos, Botas…) y tipos (Casual, Deportivo…) |

---

## Stack tecnológico

- **Backend:** PHP 8.1 · Laravel 10
- **Frontend:** Blade · Bootstrap 5 · Soft UI Dashboard (Creative Tim)
- **Librerías JS:** JsBarcode · html5-qrcode · SweetAlert2 · Toastify
- **Base de datos:** MySQL
- **Almacenamiento de fotos:** FTP externo

---

## Instalación

```bash
# 1. Clonar el repositorio
git clone <url-del-repo>
cd soft-ui-dashboard-laravel

# 2. Instalar dependencias PHP
composer install

# 3. Copiar y configurar entorno
cp .env.example .env
php artisan key:generate

# 4. Configurar base de datos en .env
# DB_DATABASE=pacamanager
# DB_USERNAME=root
# DB_PASSWORD=secret

# 5. Correr migraciones
php artisan migrate

# 6. (Opcional) Seed de datos de ejemplo
php artisan db:seed

# 7. Instalar dependencias JS
npm install && npm run dev

# 8. Levantar servidor de desarrollo
php artisan serve
```

### Variables de entorno requeridas

```env
# App
APP_NAME=PacaManager
APP_URL=http://localhost:8000

# Base de datos
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pacamanager
DB_USERNAME=root
DB_PASSWORD=

# FTP para fotos de zapatos
FTP_HOST=
FTP_USERNAME=
FTP_PASSWORD=
FTP_PORT=21
FTP_ROOT=/
```

---

## Roles de usuario

| Rol | Acceso |
|---|---|
| `dueno` | Acceso completo a todos los módulos y sucursales |
| `encargado` | Solo ve el inventario y ventas de su sucursal asignada |

---

## Flujo de trabajo típico

```
Compra paca
    └─ Crear lote de compra
           └─ Crear apertura (contar zapatos por clasificación)
                  └─ Iniciar preparación
                         ├─ Generar códigos de barras → imprimir etiquetas
                         └─ Escanear cada zapato → asignar precio + fotos
                                └─ Zapato entra al inventario
                                       └─ Registrar venta (escanear + precio venta)
```

---

## Licencia

Proyecto privado — todos los derechos reservados.
