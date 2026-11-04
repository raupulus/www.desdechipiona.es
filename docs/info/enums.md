# Módulo: Enums

*Última actualización: 2026-11-04*

> Enumeraciones PHP backed que definen los valores constantes del sistema: roles de usuario, estados de contenido, estados de email, apartados enciclopédicos de Chipiona, épocas históricas, niveles de evidencia documental y estados de moderación.

---

## Resumen

La plataforma usa siete enums PHP 8.5 (`string`-backed) para tipado fuerte y consistencia. Cada enum incluye un método `label(): string` traducible o descriptivo y métodos auxiliares para badges y clasificación en Filament y el frontend.

---

## Role

**Archivo**: `app/Enums/Role.php`

Define los roles de usuario de la plataforma.

| Case | Value | Clave de traducción (`label()`) | ES / EN | `isAdmin()` |
|------|-------|---------------------------------|---------|:-----------:|
| `SuperAdmin` | `superadmin` | `filament.role_superadmin` | Super Administrador / Super Administrator | ✅ |
| `Admin` | `admin` | `filament.role_admin` | Administrador / Administrator | ✅ |
| `User` | `user` | `filament.role_user` | Usuario / User | ❌ |

**Métodos**:
- `label(): string` — Devuelve la etiqueta traducida según el locale activo
- `isAdmin(): bool` — `true` para `SuperAdmin` y `Admin`

**Cast en User**: `'role' => Role::class`

---

## ContentStatus

**Archivo**: `app/Enums/ContentStatus.php`

Define los estados posibles de posts y páginas.

| Case | Value | Clave de traducción (`label()`) | ES / EN | Color Filament |
|------|-------|---------------------------------|---------|----------------|
| `Draft` | `draft` | `filament.status_draft` | Borrador / Draft | `warning` (amarillo) |
| `Published` | `published` | `filament.status_published` | Publicado / Published | `success` (verde) |

**Métodos**:
- `label(): string` — Etiqueta traducida
- `color(): string` — Color para badges en Filament

**Cast en Post/Page**: `'status' => ContentStatus::class`

---

## EmailStatus

**Archivo**: `app/Enums/EmailStatus.php`

Define los estados de los emails enviados.

| Case | Value | Clave de traducción (`label()`) | ES / EN | Color Filament |
|------|-------|---------------------------------|---------|----------------|
| `Sent` | `sent` | `filament.email_status_sent` | Enviado / Sent | `success` (verde) |
| `Failed` | `failed` | `filament.email_status_failed` | Fallido / Failed | `danger` (rojo) |

**Métodos**:
- `label(): string` — Etiqueta traducida
- `color(): string` — Color para badges en Filament

**Cast en SentEmail**: `'status' => EmailStatus::class`

---

## ContentGroup

**Archivo**: `app/Enums/ContentGroup.php`

Clasifica las fichas enciclopédicas de Chipiona (`Page`) y las sugerencias ciudadanas (`ContentSuggestion`) en 8 grandes apartados temáticos:
- `Historia` (`historia`), `Monumentos` (`monumentos`), `Playas` (`playas`), `Naturaleza` (`naturaleza`), `Fauna` (`fauna`), `Flora` (`flora`), `Gastronomia` (`gastronomia`), `Fiestas` (`fiestas`), `Personajes` (`personajes`).

---

## HistoricalEra

**Archivo**: `app/Enums/HistoricalEra.php`

Define los grandes periodos cronológicos de la historia de Chipiona para filtrado y ordenación:
- `PrehistoryAntiquity` (`prehistoria-antiguedad`), `MiddleAges` (`edad-media`), `EarlyModern` (`edad-moderna`), `Contemporary` (`edad-contemporanea`), `PresentNature` (`actualidad-naturaleza`).

---

## EvidenceLevel

**Archivo**: `app/Enums/EvidenceLevel.php`

Distingue el rigor historiográfico de cada página o noticia:
- `Consolidated` (`consolidado`): Contrastado con fuentes primarias o académicas `[F-XXXX]`.
- `Documented` (`documentado`): Respaldado por bibliografía o documentación oficial.
- `Tradition` (`tradicion`): Tradición oral, memoria vecinal, mito o leyenda local explícita.
- `Disputed` (`discutido`): Hipótesis abierta o discrepancia entre fuentes históricas.

---

## SuggestionStatus

**Archivo**: `app/Enums/SuggestionStatus.php`

Define el estado de moderación en el panel admin para sugerencias (`ContentSuggestion`), correcciones (`CorrectionRequest`) y refranes (`Proverb`):
- `Pending` (`pending`), `Approved` (`approved`), `Rejected` (`rejected`).
