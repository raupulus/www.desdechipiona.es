# Guía y Especificación de Diseño (DESIGN.md)

*Última actualización: 2026-11-03*

> Especificación técnica sobre arquitectura visual, maquetación, tipografía y esquema cromático para **Desde Chipiona** (portal de divulgación e investigación de historia moderna y patrimonio local), basado en Tailwind CSS v4.

---

## 1. Fundamentos y Filosofía Visual

El portal **Desde Chipiona** combina el rigor de un archivo histórico documental con la estética de una revista digital de historia moderna. La interfaz prioriza la legibilidad de textos largos, la trazabilidad de fuentes (`[F-XXXX]`), la jerarquía cromática por épocas o temáticas y la accesibilidad (**WCAG 2.1 AA**, manteniendo ratios de contraste mínimos de `4.5:1` en texto normal y `3:1` en textos grandes y bordes de componentes interactivos).

El diseño sigue una estrategia **mobile-first** estricta y emplea una paleta cromática rica inspirada en cuatro ejes identitarios de Chipiona:
1. **Naranja Terracota / Ámbar (`orange` / `amber`)**: Alfarería romana de El Olivar, piedra ostionera y atardeceres sobre el Atlántico.
2. **Rojo Carmesí / Lacre (`rose` / `red`)**: Sellos de lacre de las Cartas Pueblas (1477), legajos nobiliarios de la Casa de Arcos y tradición vitivinícola.
3. **Morado Imperial / Violeta (`violet` / `purple`)**: Tinta ferrogálica de archivo, patrimonio cultural, solemnidad académica y citas documentales.
4. **Azul Atlántico / Marino (`blue` / `sky` / `slate`)**: El océano Atlántico, el Faro (*Caepionis Turris*), los corrales de pesca y la estructura de navegación.

---

## 2. Stack de Estilos: Tailwind CSS v4

La aplicación utiliza **Tailwind CSS v4**, configurado mediante la directiva `@theme` en `resources/css/app.css`:

```css
@import "tailwindcss";
@plugin "@tailwindcss/forms";

@theme {
    --font-sans: 'Figtree', ui-sans-serif, system-ui, sans-serif;
    --font-serif: 'Merriweather', 'Georgia', ui-serif, serif;
    --font-mono: 'JetBrains Mono', ui-monospace, monospace;

    /* Eje 1: Naranja Terracota (Acción cálida, Arqueología y Antigüedad) */
    --color-brand-orange: #ea580c;       /* orange-600 */
    --color-brand-orange-hover: #c2410c; /* orange-700 */
    --color-brand-orange-soft: #fff7ed;  /* orange-50 */

    /* Eje 2: Rojo Lacre (Destacados, Edad Media/Moderna y Alertas) */
    --color-brand-red: #e11d48;          /* rose-600 */
    --color-brand-red-hover: #be123c;    /* rose-700 */
    --color-brand-red-soft: #fff1f2;     /* rose-50 */

    /* Eje 3: Morado Archivo (Fuentes F-XXXX, Citas, Historia Contemporánea) */
    --color-brand-purple: #7c3aed;       /* violet-600 */
    --color-brand-purple-hover: #6d28d9; /* violet-700 */
    --color-brand-purple-soft: #f5f3ff;  /* violet-50 */

    /* Eje 4: Azul Atlántico (Estructura, Navegación, Patrimonio Marítimo) */
    --color-brand-blue: #0284c7;         /* sky-600 */
    --color-brand-blue-dark: #1e3a8a;    /* blue-900 */
    --color-brand-blue-soft: #f0f9ff;    /* sky-50 */

    /* Tokens Semánticos Globales */
    --color-primary: #ea580c;
    --color-primary-dark: #c2410c;
    --color-secondary: #1e3a8a;
    --color-accent: #7c3aed;
    --color-crimson: #e11d48;
    --color-dark: #0f172a;               /* slate-900 */
    --color-surface: #ffffff;
    --color-surface-warm: #fafaf9;       /* stone-50 (papel editorial) */
    --color-muted: #475569;              /* slate-600 */
}
```

### Directrices de Tailwind v4 y CSS Moderno:
- Toda la configuración de tokens se declara mediante variables CSS y directivas `@theme` nativas.
- Uso de `aspect-ratio` (`aspect-video`, `aspect-4/3`) en contenedores multimedia para evitar desplazamientos de diseño (CLS).
- Uso de `overflow: clip` en tarjetas con bordes redondeados y `line-clamp` para extractos uniformes.
- Soporte para esquemas de color del sistema (`color-scheme: light dark`) y alto contraste (`forced-colors`).

---

## 3. Esquema de Colores y Combinación Cromática

La combinación de **naranjas, rojos, morados y azules** se articula sobre una base neutra cálida (`stone-50` / `slate-900`) para evitar saturación visual y lograr una estética editorial moderna:

### 3.1. Paleta de los 4 Tonos Principales

| Familia | Escala Base Tailwind | Hex Principal | Fondo Suave (Badge/Card) | Borde / Acento | Uso en el Portal de Historia |
|---------|----------------------|---------------|--------------------------|----------------|------------------------------|
| **Naranja (Terracota)** | `orange-600` / `amber-600` | `#ea580c` | `bg-orange-50 text-orange-800` | `border-orange-500` | **Antigüedad y Arqueología**, CTAs principales, buscador e hitos destacados |
| **Rojo (Carmesí / Lacre)** | `rose-600` / `red-600` | `#e11d48` | `bg-rose-50 text-rose-800` | `border-rose-500` | **Edad Media y Moderna**, discrepancias historiográficas (`[DISCUTIDO]`), sellos documentales |
| **Morado (Imperial)** | `violet-600` / `purple-700` | `#7c3aed` | `bg-violet-50 text-violet-800` | `border-violet-500` | **Siglos XIX–XXI**, referencias de archivo (`[F-XXXX]`), citas textuales y glosario |
| **Azul (Atlántico)** | `sky-600` / `blue-900` | `#0284c7` / `#1e3a8a` | `bg-sky-50 text-sky-800` | `border-sky-500` | **Patrimonio Marítimo y Naturaleza**, barra de navegación, enlaces y datos verificados |

### 3.2. Gradientes Editoriales Recomendados

Para cabeceras (Hero), bloques CTA y banners de época se emplean transiciones suaves entre las cuatro familias:
- **Atardecer en Regla (Naranja → Rojo → Morado)**:
  `bg-gradient-to-r from-orange-600 via-rose-600 to-violet-700`
- **Horizonte Atlántico (Azul Profundo → Morado → Rojo Lacre)**:
  `bg-gradient-to-br from-slate-900 via-blue-900 to-violet-900` con acentos en `orange-500` y `rose-500`.
- **Contraste Cálido-Frío (Azul → Naranja)**:
  Fondo `bg-slate-900` o `bg-blue-950` con botones primarios en `bg-orange-600 hover:bg-orange-500` y badges en `rose-500` / `violet-500`.

### 3.3. Estados de Evidencia Histórica (Badges Semánticos)

Siguiendo las reglas de trazabilidad documental del proyecto:
- **Consolidado (`consolidado`)**: `bg-sky-50 text-sky-800 border border-sky-300` (Azul Atlántico + icono de verificación).
- **Documentado (`documentado`)**: `bg-violet-50 text-violet-800 border border-violet-300` (Morado Archivo + código `F-XXXX`).
- **Indicios / Tradición (`indicios`)**: `bg-orange-50 text-orange-800 border border-orange-300` (Naranja Ámbar + icono de lupa).
- **Discutido / Discrepancia (`discutido`)**: `bg-rose-50 text-rose-800 border border-rose-300` (Rojo Lacre + icono de contraste).

> **Accesibilidad cromática**: Nunca se utiliza el color como único indicador de estado. Todo badge de evidencia o alerta incorpora una etiqueta textual explícita y un icono SVG de apoyo.

---

## 4. Tipografía y Escalas de Texto

- **Títulos Históricos y Editoriales (`font-serif` o `font-sans` en negrita)**:
  - **H1 (Portada / Artículo)**: `text-3xl md:text-5xl font-extrabold tracking-tight text-slate-900`
  - **H2 (Sección Histórica)**: `text-2xl md:text-3xl font-bold tracking-tight text-slate-900`
  - **H3 (Tarjeta / Hito)**: `text-lg md:text-xl font-bold text-slate-800`
- **Cuerpo de Lectura**: `text-base md:text-lg text-slate-700 leading-relaxed`
- **Citas Documentales (`[F-XXXX]`)**: `font-mono text-xs font-semibold px-2 py-0.5 rounded bg-violet-100 text-violet-800`

---

## 5. Estructura de Layout y Bloques Comunes

1. **Navbar (`h-16` / `sticky top-0 z-50`)**:
   - Fondo `bg-slate-900/95 backdrop-blur-md text-white` con borde inferior en degradado (`from-orange-500 via-rose-500 to-violet-600`) o versión clara en `bg-white/95 border-b border-stone-200`.
2. **Hero / Cabecera de Época**:
   - Fondo oscuro en azul noche y violeta (`from-slate-950 via-blue-950 to-violet-950`) con detalles luminosos en naranja/rojo y buscador rápido de archivos y épocas.
3. **Tarjetas de Artículos y Épocas (Subgrid / Flex)**:
   - Borde superior cromático (`border-t-4`) que identifica la categoría o periodo:
     - Naranja (`border-t-orange-500`)
     - Rojo (`border-t-rose-600`)
     - Morado (`border-t-violet-600`)
     - Azul (`border-t-sky-600`)
4. **Bloques Modulares del Content Builder**:
   - **Texto enriquecido + Cita histórica (`quote`)**: Borde lateral `border-s-4 border-violet-600 bg-violet-50/60`.
   - **Nota de Discrepancia / Archivo**: Caja `border-s-4 border-rose-600 bg-rose-50/70`.
   - **Cronología vertical / horizontal**: Nodos alternando `orange-600`, `rose-600`, `violet-600` y `sky-600`.
   - **Bloque CTA**: Fondo con gradiente `from-blue-950 via-violet-900 to-rose-900` y botón de alto contraste en `bg-orange-600 hover:bg-orange-500`.

---

## 6. Reglas de Componentes y Maquetación

- **Bordes redondeados**: `rounded-xl` (12px) para tarjetas documentales; `rounded-2xl` (16px) para bloques destacados; `rounded-full` para píldoras de época.
- **Sombras y bordes**: `shadow-sm hover:shadow-md border border-stone-200/80 transition-all duration-200`.
- **Reutilización en Blade**: Al pasar los diseños del directorio `template/` a las vistas definitivas de Laravel (`resources/views/`), emplear siempre los componentes catalogados en [COMPONENTS.md](COMPONENTS.md).
