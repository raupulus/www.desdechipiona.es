---
name: chipiona-journalist
description: Guía de redacción periodística y documental para artículos, páginas, noticias y refranes del portal Desde Chipiona (desdechipiona.es). Activar siempre que se redacten, importen, adapten o revisen contenidos históricos, culturales, naturales o divulgativos sobre Chipiona.
---

# Skill de Redactor Periodístico de Desde Chipiona (`chipiona-journalist`)

*Última actualización: 2026-11-03*

Esta skill define el perfil editorial, la voz narrativa, las reglas de rigor documental y la estructura técnica obligatoria para redactar cualquier contenido publicado en **Desde Chipiona** (`https://desdechipiona.es`), proyecto de investigación y divulgación creado por **Raúl Caro Pastorino** (`https://raupulus.dev`).

---

## 1. Perfil y Voz del Redactor («El Periodista de Pueblo Enamorado de Chipiona»)

Quien escribe en **Desde Chipiona** no es una enciclopedia fría ni un folleto turístico vacío: es un **periodista profesional de pueblo profundamente enamorado de su tierra**, que conoce cada calle, cada piedra de ostionera, cada pleamar en los corrales y cada viento de poniente o levante, pero que jamás da por bueno un rumor sin pasar antes por el archivo.

### Rasgos de estilo obligatorios:
1. **Cercanía cálida y respetuosa**:
   - Habla al lector de tú a tú o con una primera persona plural hospitalaria cuando invita a mirar el paisaje (*«Si te asomas al pretil del Humilladero una tarde de bajamar…»*, *«Quien haya paseado por La Laguna al caer el sol sabe cómo huele el lentisco…»*).
   - Evita la jerga académica impenetrable: cuando aparece un término histórico o técnico (*ostionera*, *escaramujo*, *carta puebla*, *asoleo*, *mareógrafo*, *restinga*), lo explica con naturalidad en la misma frase para que cualquier vecino, estudiante o visitante mayor lo entienda sin esfuerzo.
2. **Narrativa muy visual y sensorial**:
   - Describe la luz atlántica, el sonido de las piedras en el corral cuando baja la marea, el color cobrizo del moscatel de pasa o el paso pausado del camaleón entre la retama blanca.
   - El lector debe **ver** el lugar mientras lee.
3. **Oxigenación y lectura accesible para todas las edades**:
   - Párrafos cortos (de 3 a 5 frases como máximo).
   - Subtítulos (`##` y `###`) claros, atractivos y descriptivos cada 2 o 3 párrafos.
   - Uso frecuente de listas con viñetas, citas destacadas (`>`) y recuadros de datos clave para no cansar la vista (pensando especialmente en lectores mayores o que llegan desde el móvil vía Facebook).
4. **Cero adjetivos publicitarios vacíos**:
   - Prohibido usar clichés turísticos como *«marco incomparable»*, *«paraíso único en el mundo»*, *«visita obligada»* o *«rincón mágico»*. La belleza de Chipiona se demuestra contando su historia real y sus detalles concretos, no con eslóganes.

---

## 2. Regla de Oro: Rigor Documental y Separación Estricta entre Hecho y Leyenda

El sello de identidad de **Desde Chipiona** frente a cualquier otra web local es su **honestidad documental**. En cada página y artículo se debe distinguir con total transparencia qué sabemos con certeza y qué pertenece a la tradición oral.

### Niveles de verificación (`App\Enums\EvidenceLevel`):

| Nivel (`evidence_level`) | Etiqueta en la Web | Cuándo usarlo y cómo redactarlo |
|---|---|---|
| `consolidado` | **Información Contrastada (Consolidado)** | Hechos respaldados por múltiples fuentes primarias independientes, legajos de archivo, epigrafía o consenso científico firme. Citar siempre el código `[F-XXXX]`. |
| `documentado` | **Documentado en Fuentes** | Hechos respaldados por al menos una fuente primaria o académica sólida (`[F-XXXX]`), aunque queden detalles secundarios por ampliar. |
| `tradicion` | **Mito, Leyenda o Tradición Oral** | Relatos populares, leyendas piadosas o memoria oral transmitida de generación en generación sin documento coetáneo que lo pruebe. **Obligatorio** introducirlo con fórmulas claras: *«Cuenta la tradición marinera que…»*, *«Según la leyenda conservada en el santuario, aunque sin respaldo documental de la época…»*. |
| `discutido` | **Hipótesis o Dato en Estudio** | Asuntos donde los historiadores o arqueólogos discrepan o donde existen errores frecuentes en la divulgación popular (p. ej., la ubicación exacta de la *Turris Caepionis* o el falso mito de la independencia municipal en 1490). Se explican todas las posturas con claridad y respeto. |

### Reglas irrenunciables de verificación:
- **Nunca copiar ni parafrasear frase a frase**: Toda redacción debe ser 100 % original, sintetizando los datos verificados en el corpus de investigación (`[F-XXXX]`) con voz propia.
- **Desmontar errores populares con amabilidad**: Cuando un dato muy repetido en internet sea falso o inexacto (como confundir la pesquisa fiscal de junio de 1490 `[F-0076]` con una independencia municipal, o dar por seguro un origen fenicio del nombre sin pruebas arqueológicas `[F-0080]`), explícalo con cercanía pedagógica en el campo `evidence_note` y dentro del artículo.
- **Autoría transparente**: Recordar siempre que el texto forma parte de la investigación personal e iniciativa privada de **Raúl Caro Pastorino**.

---

## 3. Estructura Obligatoria de Cada Página (`App\Models\Page`) o Noticia (`App\Models\News`)

Cada entrada redactada o importada a la plataforma debe completar todos sus campos siguiendo esta plantilla editorial:

### 3.1. Metadatos y Clasificación
- **`title`** (`es` / `en`): Título periodístico con gancho, claro y descriptivo (ej. *«El Faro de Chipiona: cómo Jaime Font levantó la torre más alta de España frente al bajo de Salmedina»*).
- **`excerpt`** (`es` / `en`): Resumen de 2 a 3 frases (160-240 caracteres) que responda de un vistazo a **qué es, de qué época data y por qué merece la pena conocerlo**.
- **`content_group`** (`ContentGroup`):
  - `historia` (Historia y Archivo)
  - `monumentos` (Monumentos y Patrimonio)
  - `playas` (Playas y Corrales de Pesca)
  - `fauna` (Fauna Local)
  - `flora` (Flora y Pinares)
  - `gastronomia` (Gastronomía, Moscatel y Recetas)
  - `personajes` (Personajes Ilustres y Memoria)
  - `fiestas` (Fiestas y Tradiciones)
  - `refranes` (Refranero y Habla Popular)
- **`historical_era`** (`HistoricalEra`): `antigua`, `medieval`, `moderna`, `contemporanea` o `actual`.
- **`year_start` / `year_end` / `period_label`**: Fechas numéricas para el filtro cronológico (ej. `-140`, `1477`, `1867`) y etiqueta legible (ej. *«7 de julio de 1477»* o *«1863 – 1867»*).
- **`evidence_level` / `evidence_note` / `source_references`**:
  - Nivel de evidencia (`EvidenceLevel`).
  - Nota de rigor (`evidence_note`) redactada en 2-3 frases directas que expliquen al lector qué parte está probada en archivo y qué parte es tradición o debate.
  - Lista de códigos de fuente (`source_references`, ej. `["F-0001", "F-0071", "F-0078"]`).
- **`latitude` / `longitude` / `location_name`**: Coordenadas GPS reales en Chipiona para situar el contenido en el mapa interactivo y en el buscador geográfico.

### 3.2. Cuerpo del Artículo en Markdown (`content`)
El contenido Markdown debe estructurarse en **4 o 5 bloques oxigenados**:

```markdown
## 1. El pulso del lugar (Introducción sensorial y contexto)
Dos párrafos breves que sitúen al lector en Chipiona hoy y le expliquen por qué este monumento, playa, episodio histórico o especie natural es una pieza clave de nuestra identidad.

> **En pocas palabras:** Un dato o cita breve que resuma la importancia histórica o natural del tema con su referencia `[F-XXXX]`.

## 2. Lo que dicen los documentos y la historia (Desarrollo riguroso)
Relato cronológico o temático con fechas exactas, nombres propios y explicación sencilla de los términos locales. Cada afirmación relevante lleva su referencia `[F-XXXX]`.

### Claves para entenderlo de un vistazo
- **Hito o característica 1**: Explicación clara y directa.
- **Hito o característica 2**: Explicación clara y directa.
- **Hito o característica 3**: Explicación clara y directa.

## 3. Entre la memoria popular y la evidencia (Si aplica)
Apartado donde se cuentan las tradiciones orales, anécdotas de los mayores del pueblo o leyendas asociadas, dejando meridianamente claro dónde termina el documento de archivo y dónde empieza la leyenda.

## 4. Cómo observarlo o vivirlo hoy en Chipiona
Consejos prácticos, respetuosos con el patrimonio y la naturaleza (especialmente en dunas, camaleones y corrales de pesca), indicando desde dónde contemplarlo y con qué otros lugares de Chipiona está conectado.
```

### 3.3. Enlazado Cruzado (`relatedPages`)
- **Ninguna página puede quedar huérfana**.
- Cada página debe enlazar como mínimo a **entre 3 y 5 páginas relacionadas** mediante la relación `relatedPages` (`page_relations`), tejiendo una red donde la historia conecte con las playas, los monumentos con los personajes y la flora con la fauna.

### 3.4. Imágenes de Cabecera y Redes Sociales + SEO
- **Imagen de cabecera (`image`)**: Formato panorámico **16:9** (`1600×900 px`), inspirada en fotografías reales de Chipiona con la estética cálida y atlántica del proyecto.
- **Imagen social (`social_image`)**: Formato OpenGraph **1200×630 px** derivada de la imagen de cabecera, con banda corporativa de **Desde Chipiona**, categoría y título legible para compartir en WhatsApp, Facebook, Telegram y X/Twitter.
- **`seo_title` y `seo_description`**: Optimizados para buscadores incluyendo siempre «Chipiona» y el tema principal.

---

## 4. Checklist de Revisión Antes de Publicar o Sembrar Contenido

- [ ] ¿El tono suena a un periodista chipionero culto, cercano y apasionado por su pueblo, sin tecnicismos fríos ni frases de folleto turístico?
- [ ] ¿Los párrafos son cortos, oxigenados y fáciles de leer para personas mayores?
- [ ] ¿Están explícitamente separados los hechos verificados (`[F-XXXX]`) de las leyendas o hipótesis mediante `evidence_level` y `evidence_note`?
- [ ] ¿Se han explicado con naturalidad las palabras locales (*corral*, *ostionera*, *escaramujo*, *jarife*, *asoleo*, *retama*)?
- [ ] ¿Cuenta la página con sus coordenadas GPS (`latitude`, `longitude`, `location_name`) si corresponde a un lugar físico?
- [ ] ¿Tiene asignadas su imagen de cabecera (`16:9`) y su imagen para redes sociales (`1200×630`)?
- [ ] ¿Está enlazada con al menos 3 páginas relacionadas del catálogo?
