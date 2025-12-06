# Sistema de Librería - Proyecto Final Corte III

## 🚀 Instalación para el Equipo

1. **Base de Datos**:
   - Abre XAMPP (Apache y MySQL).
   - Ve a phpMyAdmin.
   - Importa el archivo `database/schema.sql` para crear la estructura.
   - Importa el archivo `database/seed.sql` para tener los usuarios de prueba.

2. **Configuración**:
   - Revisa el archivo `config/db.php` y asegúrate de que la contraseña de root sea la correcta (usualmente vacía en XAMPP).

## 🔑 Credenciales de Acceso (Local)

**Rol Administrador:**
- Usuario: `admin`
- Contraseña: `12345`

**Rol Cajero:**
- Usuario: `cajero`
- Contraseña: `12345`

## 📂 Estructura del Proyecto
- `/ajax`: Lógica del backend (sin vista).
- `/reportes`: Vistas para imprimir.
- `/database`: Archivos SQL.
- `ticket.php`: Vista exclusiva para impresora térmica.
- `img.php`: Endpoint para mostrar imágenes de la BD.