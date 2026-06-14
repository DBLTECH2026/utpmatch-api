# Arquitectura — UTP+Match API

> Backend del copiloto de empleabilidad UTP+Match.
> **Stack:** Laravel 12 · PHP 8.2 · MySQL · Sanctum (Bearer tokens).
> **Estilo:** API REST por capas — **MVC + DAO/Repository + Service + DTO**.

---

## 1. Visión general

La API sigue una **arquitectura en capas** con separación estricta de responsabilidades.
Cada petición atraviesa las capas en un solo sentido; **ninguna capa salta a otra que no sea su vecina inmediata**.

```mermaid
flowchart TD
    C[Cliente / Next.js] -->|HTTP + Bearer token| R[Rutas API v1]
    R --> MW[Middleware<br/>SecurityHeaders · auth:sanctum · throttle · role]
    MW --> CTRL[Controller<br/>capa Presentación]
    CTRL --> FR[FormRequest<br/>Validación]
    CTRL --> DTO[DTO<br/>transferencia inmutable]
    CTRL --> SVC[Service<br/>Lógica de negocio]
    SVC --> REPOI[«interface»<br/>RepositoryInterface]
    REPOI -.binding IoC.-> REPO[EloquentRepository<br/>capa DAO]
    REPO --> M[Model Eloquent]
    M --> DB[(MySQL)]
    CTRL --> RES[API Resource<br/>Serialización salida]
    RES -->|JSON| C

    style CTRL fill:#FCEAE9,stroke:#E2231A
    style SVC fill:#E4F4EF,stroke:#13A07C
    style REPO fill:#E4F4EF,stroke:#13A07C
    style MW fill:#16161A,color:#fff
```

---

## 2. Responsabilidad de cada capa

| Capa | Carpeta | Responsabilidad | Regla de oro |
|------|---------|-----------------|--------------|
| **Rutas** | `routes/api.php` | Mapear URL → Controller, aplicar middleware | Versionadas `/api/v1` |
| **Middleware** | `app/Http/Middleware` | Seguridad transversal (headers, auth, rate limit, RBAC) | Falla cerrado |
| **Controller** | `app/Http/Controllers/Api/V1` | Traducir HTTP ↔ dominio. **Sin lógica de negocio** | Delgado (SRP) |
| **FormRequest** | `app/Http/Requests` | Validar y normalizar la entrada | Lista blanca de campos |
| **DTO** | `app/DataTransferObjects` | Transportar datos validados entre capas | Inmutable (`readonly`) |
| **Service** | `app/Services` | **Lógica de negocio** y orquestación | No conoce HTTP |
| **Repository (DAO)** | `app/Repositories` | Único punto de acceso a datos | Detrás de una interfaz |
| **Model** | `app/Models` | Mapeo objeto-relacional (Eloquent) | Relaciones + casts |
| **Resource** | `app/Http/Resources` | Serializar la salida (lista blanca) | Nunca expone secretos |

---

## 3. Flujo de un request autenticado (secuencia)

Ejemplo: `PUT /api/v1/profile` (actualizar Perfil 360).

```mermaid
sequenceDiagram
    participant Cli as Cliente
    participant MW as Middleware
    participant Ctrl as ProfileController
    participant Req as UpdateProfileRequest
    participant Svc as ProfileService
    participant Repo as ProfileRepository (DAO)
    participant DB as MySQL

    Cli->>MW: PUT /profile + Bearer token
    MW->>MW: auth:sanctum (valida token)
    MW->>MW: throttle:api (rate limit)
    MW->>Ctrl: request autenticado
    Ctrl->>Req: validación automática
    Req-->>Ctrl: datos validados (o 422)
    Ctrl->>Svc: update(user, datos)
    Svc->>Repo: update(profile, datos)
    Repo->>DB: UPDATE (binding parametrizado)
    DB-->>Repo: filas afectadas
    Repo-->>Svc: Profile
    Svc-->>Ctrl: Profile
    Ctrl->>Cli: 200 + ProfileResource (JSON)
```

---

## 4. Patrón Repository / DAO + Inversión de Dependencias

El núcleo de la testabilidad y el desacople:

```mermaid
classDiagram
    class UserRepositoryInterface {
        <<interface>>
        +findById(int) User
        +findByEmail(string) User
        +create(array) User
        +update(User, array) User
    }
    class EloquentUserRepository {
        +findById(int) User
        +findByEmail(string) User
        +create(array) User
        +update(User, array) User
    }
    class AuthService {
        -UserRepositoryInterface users
        +register(DTO) array
        +login(email, pass) array
    }

    UserRepositoryInterface <|.. EloquentUserRepository : implementa
    AuthService --> UserRepositoryInterface : depende de la abstracción
```

- `AuthService` **depende de la interfaz**, no de Eloquent (principio **DIP** de SOLID).
- El binding interfaz→implementación vive en `RepositoryServiceProvider`.
- Para pruebas, se puede cambiar el binding por un *fake* sin tocar la lógica (**OCP**).

---

## 5. Modelo de datos (ER) — módulo Auth + Perfil 360

```mermaid
erDiagram
    USERS ||--o| PROFILES : "tiene (1:1)"
    USERS ||--o{ CONNECTIONS : "vincula (1:N)"
    PROFILES }o--o{ SKILLS : "profile_skills (N:M)"

    USERS {
        bigint id PK
        string name
        string email UK
        string password "hashed"
        string codigo_utp UK
        string carrera
        tinyint ciclo
        enum rol "alumno|asesor|admin"
    }
    PROFILES {
        bigint id PK
        bigint user_id FK
        string rol_objetivo
        string headline
        text about
        tinyint empleabilidad_score
    }
    CONNECTIONS {
        bigint id PK
        bigint user_id FK
        enum provider
        text access_token_enc "CIFRADO"
        enum status
    }
    SKILLS {
        bigint id PK
        string nombre UK
        enum categoria
    }
```

---

## 6. Principios SOLID aplicados

| Principio | Dónde se ve |
|-----------|-------------|
| **S** — Responsabilidad única | Controller traduce HTTP, Service razona, Repository accede a datos |
| **O** — Abierto/cerrado | Cambiar de almacenamiento = cambiar el binding, no la lógica |
| **L** — Sustitución de Liskov | Cualquier `*RepositoryInterface` es intercambiable |
| **I** — Segregación de interfaces | Interfaces pequeñas y específicas por entidad |
| **D** — Inversión de dependencias | Services dependen de interfaces, no de implementaciones |

---

## 7. Convenciones

- **Versionado:** todo bajo `/api/v1`. Cambios incompatibles → `/api/v2`.
- **Respuesta uniforme:** `{ "data": ..., "message": ... }` y `{ "message", "errors" }` en error.
- **Códigos HTTP correctos:** 200, 201, 401, 403, 422, 429.
- **Nombres en español** en el dominio (campos, mensajes), inglés en el framework.

Ver también: [`SECURITY.md`](./SECURITY.md) · [`../README.md`](../README.md)
