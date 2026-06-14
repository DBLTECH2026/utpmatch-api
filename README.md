# UTP+Match — API (Backend)

Backend del **copiloto de IA de empleabilidad UTP+Match** (Hackathon UTP+, Reto 01).
API REST construida con **Laravel 12 / PHP 8.2 / MySQL**, autenticación con **Sanctum**
y arquitectura por capas **MVC + DAO/Repository + Service + DTO**.

> 📐 Arquitectura → [`docs/ARCHITECTURE.md`](docs/ARCHITECTURE.md)
> 🔐 Ciberseguridad (OWASP) → [`docs/SECURITY.md`](docs/SECURITY.md)

---

## 🚀 Puesta en marcha

```bash
# 1. Dependencias
composer install

# 2. Entorno
cp .env.example .env
php artisan key:generate

# 3. Base de datos (crear BD 'utpmatch' en MySQL) y migrar
php artisan migrate

# 4. Servir
php artisan serve     # http://localhost:8000
```

Variables clave en `.env`:

```
DB_DATABASE=utpmatch
CORS_ALLOWED_ORIGINS=http://localhost:3000
SANCTUM_TOKEN_EXPIRATION=1440        # minutos
```

---

## 🗂️ Estructura del código (por capas)

```
app/
├── Http/
│   ├── Controllers/Api/V1/   # Presentación: traduce HTTP ↔ dominio
│   ├── Requests/             # Validación de entrada (FormRequest)
│   ├── Resources/            # Serialización de salida (lista blanca)
│   └── Middleware/           # Seguridad: headers, RBAC
├── Services/                 # Lógica de negocio (no conoce HTTP)
├── Repositories/
│   ├── Contracts/            # Interfaces DAO (abstracción)
│   └── Eloquent/             # Implementaciones (acceso a datos)
├── DataTransferObjects/      # DTO inmutables entre capas
├── Models/                   # Eloquent (ORM) + relaciones + casts
└── Providers/                # IoC: binding interfaz → implementación
```

---

## 🔌 Endpoints (v1)

| Método | Ruta | Auth | Descripción |
|--------|------|------|-------------|
| POST | `/api/v1/auth/register` | — | Crear cuenta (alumno) + perfil 360 |
| POST | `/api/v1/auth/login` | — | Iniciar sesión → token Bearer |
| POST | `/api/v1/auth/logout` | ✅ | Revocar token actual |
| GET  | `/api/v1/me` | ✅ | Usuario actual + perfil + conexiones |
| GET  | `/api/v1/profile` | ✅ | Perfil 360 del usuario |
| PUT  | `/api/v1/profile` | ✅ | Actualizar perfil (rol objetivo, headline, about) |

**Auth:** enviar `Authorization: Bearer <token>` en las rutas protegidas.

### Ejemplo rápido

```bash
# Registro
curl -X POST http://localhost:8000/api/v1/auth/register \
  -H "Content-Type: application/json" -H "Accept: application/json" \
  -d '{"name":"Maria Quispe","email":"maria@utp.edu.pe",
       "password":"Utp+Match2026!","password_confirmation":"Utp+Match2026!",
       "codigo_utp":"U20231234","carrera":"Ing. Sistemas","ciclo":8}'

# Usar el token devuelto:
curl http://localhost:8000/api/v1/me \
  -H "Authorization: Bearer <TOKEN>" -H "Accept: application/json"
```

Respuesta uniforme: `{ "data": ..., "message": ... }` (errores: `{ "message", "errors" }`).

---

## 🧱 Patrones aplicados

- **MVC** — separación Modelo / Controlador / (vista = JSON Resource).
- **Repository / DAO** — acceso a datos detrás de interfaces.
- **Service Layer** — lógica de negocio aislada y testeable.
- **DTO** — transferencia inmutable de datos validados.
- **Dependency Injection / IoC** — inversión de dependencias (SOLID-D).
- **Middleware (Chain of Responsibility)** — seguridad transversal.

---

## 🔐 Seguridad (resumen)

Sanctum Bearer · bcrypt · cifrado de tokens OAuth · RBAC · rate limiting ·
validación estricta · CORS allowlist · cabeceras de seguridad · logging de auth.
Detalle y mapa OWASP Top 10 en [`docs/SECURITY.md`](docs/SECURITY.md).

---

## 🧩 Próximos módulos

El módulo **Auth + Perfil 360** es la referencia arquitectónica. Se replica el
mismo patrón (Controller → Service → Repository) para: Ruta & Brechas, CV, Match,
Copiloto. Ver el plan de producto en la documentación del proyecto.
