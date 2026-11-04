<?php

return [
    // Navegación
    'welcome' => 'Bienvenido',
    'home' => 'Inicio',
    'about' => 'Sobre nosotros',
    'contact' => 'Contacto',
    'blog' => 'Blog',
    'pages' => 'Páginas',
    'privacy' => 'Política de privacidad',
    'cookies' => 'Política de cookies',

    // Autenticación
    'login' => 'Iniciar sesión',
    'register' => 'Registrarse',
    'logout' => 'Cerrar sesión',
    'profile' => 'Perfil',

    // Acciones
    'settings' => 'Configuración',
    'search' => 'Buscar',
    'read_more' => 'Leer más',
    'back' => 'Volver',
    'save' => 'Guardar',
    'cancel' => 'Cancelar',
    'delete' => 'Eliminar',
    'edit' => 'Editar',
    'create' => 'Crear',
    'update' => 'Actualizar',

    // Estados
    'published' => 'Publicado',
    'draft' => 'Borrador',
    'featured' => 'Destacado',
    'active' => 'Activo',
    'inactive' => 'Inactivo',

    // General
    'all_rights_reserved' => 'Todos los derechos reservados',
    'language' => 'Idioma',
    'no_results' => 'No se encontraron resultados',

    // Contacto
    'contact_success' => 'Mensaje enviado correctamente. Nos pondremos en contacto contigo pronto.',
    'contact_error' => 'Hubo un error al enviar el mensaje. Inténtalo de nuevo.',

    // ============================================================
    // Home — Hero
    // ============================================================
    'home_hero_badge' => 'Laravel 13 · Filament 5 · Tailwind 4 · Vue 3',
    'home_hero_title' => 'Skeleton Laravel Project',
    'home_hero_description' => 'Plantilla base lista para producción. Incluye autenticación completa, panel admin Filament, API REST con Sanctum, traducciones, SEO, Docker y todo lo necesario para arrancar tu próximo proyecto en minutos.',
    'home_hero_btn_docs' => 'Documentación en el Blog',
    'home_hero_btn_admin' => 'Panel Admin',
    'home_hero_btn_api' => 'API Docs',

    // Home — Quick Start
    'home_quickstart_title' => 'Inicio rápido',

    // Home — Features
    'home_features_title' => 'Todo lo que necesitas, ya implementado',
    'home_features_description' => 'No empieces desde cero. Este skeleton cubre las funcionalidades más comunes de cualquier proyecto Laravel profesional.',

    'home_feat_auth_title' => 'Autenticación completa',
    'home_feat_auth_desc' => 'Login, registro, verificación de email y recuperación de contraseña — tanto para web como para API REST con Sanctum.',

    'home_feat_filament_title' => 'Panel Admin Filament 5',
    'home_feat_filament_desc' => 'Dos paneles separados: /admin para administradores y /panel para usuarios. CRUD completo de todos los modelos.',

    'home_feat_api_title' => 'API REST documentada',
    'home_feat_api_desc' => 'API versionada en /api/v1 con autenticación Sanctum. Documentación auto-generada con Scribe en /docs.',

    'home_feat_i18n_title' => 'Bilingüe ES / EN',
    'home_feat_i18n_desc' => 'Español e inglés en toda la interfaz. Modelos con campos traducibles vía spatie/laravel-translatable. Selector de idioma en el frontend.',

    'home_feat_seo_title' => 'SEO completo',
    'home_feat_seo_desc' => 'Meta tags, Open Graph, Twitter Cards, JSON-LD, hreflang, canonical y sitemap XML generado automáticamente con spatie/laravel-sitemap.',

    'home_feat_content_title' => 'Posts, Pages y Categorías',
    'home_feat_content_desc' => 'Sistema de contenido completo con estados (draft/published), campos SEO, imágenes, categorías y campos traducibles. Todo gestionable desde Filament.',

    'home_feat_settings_title' => 'Settings dinámicos',
    'home_feat_settings_desc' => 'Configuración del sitio gestionable desde el panel: título, descripción, redes sociales, contacto y SEO. Accesibles en cualquier parte con setting(\'key\').',

    'home_feat_docker_title' => 'Docker incluido',
    'home_feat_docker_desc' => 'Dockerización completa lista para usar: PHP 8.5, Nginx, PostgreSQL. Entorno de desarrollo y producción configurados en docker-compose.yml.',

    'home_feat_tests_title' => 'Tests incluidos',
    'home_feat_tests_desc' => 'Feature tests para autenticación web y API, acceso a paneles, formulario de contacto y tests unitarios de servicios clave con PHPUnit.',

    // Home — Stack
    'home_stack_title' => 'Stack tecnológico',

    // Home — Paneles
    'home_panels_title' => 'Paneles incluidos',
    'home_panels_description' => 'Accede a los paneles con las credenciales de los seeders en desarrollo',
    'home_panel_admin_title' => 'Panel Admin',
    'home_panel_admin_desc' => 'Gestión completa del sistema',
    'home_panel_user_title' => 'Panel Usuario',
    'home_panel_user_desc' => 'Perfil y tokens API propios',
    'home_panel_api_title' => 'API Docs',
    'home_panel_api_desc' => 'Documentación auto-generada',

    // Home — Blog
    'home_blog_subtitle' => 'Blog & Documentación',

    // Carrusel (slide-carousel)
    'slide_prev' => 'Diapositiva anterior',
    'slide_next' => 'Diapositiva siguiente',
    'slide_go_to' => 'Ir a la diapositiva :n',

    // Paginación
    'pagination' => 'Paginación',

    // Panel de usuario — Widget de bienvenida
    'welcome_greeting' => '¡Hola, :name!',
    'active_tokens_count' => 'Tienes :count token(s) API activos.',

    // Páginas legales — Textos por defecto
    'privacy_default_text' => 'El contenido de la política de privacidad se gestiona desde el panel de administración (Secciones → zona "privacy").',
    'cookies_default_text' => 'El contenido de la política de cookies se gestiona desde el panel de administración (Secciones → zona "cookies").',

    // Contacto — Formulario
    'contact_name' => 'Nombre',
    'contact_email' => 'Correo electrónico',
    'contact_subject' => 'Asunto',
    'contact_message' => 'Mensaje',
    'contact_privacy_accept' => 'Acepto la',
    'contact_privacy_link' => 'política de privacidad',
    'contact_send' => 'Enviar mensaje',

    // Validaciones — Contacto
    'validation_name_required' => 'El nombre es obligatorio.',
    'validation_name_max' => 'El nombre no puede superar 255 caracteres.',
    'validation_email_required' => 'El correo electrónico es obligatorio.',
    'validation_email_email' => 'El correo electrónico no es válido.',
    'validation_subject_required' => 'El asunto es obligatorio.',
    'validation_message_required' => 'El mensaje es obligatorio.',
    'validation_message_max' => 'El mensaje no puede superar 5.000 caracteres.',
    'validation_privacy_required' => 'Debes aceptar la política de privacidad.',
    'validation_privacy_accepted' => 'Debes aceptar la política de privacidad.',
    'validation_recaptcha_required' => 'La verificación de seguridad es obligatoria. Recarga la página e inténtalo de nuevo.',
    'validation_recaptcha_failed' => 'No se ha podido verificar que no eres un robot. Inténtalo de nuevo.',

    // Panel usuario — Tokens API
    'api_tokens_description' => 'Los tokens de API te permiten conectar aplicaciones externas con tu cuenta de forma segura. Cada token actúa como una "contraseña temporal" que puedes revocar en cualquier momento sin afectar a tu contraseña principal.',
    'api_tokens_create_title' => 'Crear nuevo token',
    'api_tokens_placeholder' => 'Nombre del token (ej: Mi aplicación)',
    'api_tokens_created_msg' => 'Token generado — cópialo ahora, no se mostrará de nuevo:',
    'api_tokens_copy' => 'Copiar',
    'api_tokens_copied' => '¡Copiado!',
    'api_tokens_list_title' => 'Tokens activos',
    'api_tokens_col_name' => 'Nombre',
    'api_tokens_col_created' => 'Creado',
    'api_tokens_col_last_used' => 'Último uso',
    'api_tokens_col_expires' => 'Expira',
    'api_tokens_col_actions' => 'Acciones',
    'api_tokens_empty' => 'No tienes tokens de API creados.',
    'api_tokens_delete_confirm' => '¿Seguro que quieres eliminar este token?',
    'api_tokens_never' => 'Nunca',
    'api_tokens_no_expiry' => 'Sin expiración',
    'api_tokens_created_notify' => 'Token creado correctamente.',
    'api_tokens_deleted_notify' => 'Token eliminado correctamente.',
    'validation_token_name_required' => 'El nombre del token es obligatorio.',
    'validation_token_name_max' => 'El nombre no puede superar los 255 caracteres.',

    // Panel usuario — Perfil
    'profile_email_helper' => 'Si necesitas modificar tu email, contacta con el administrador.',
    'profile_password_helper' => 'Déjalo en blanco si no deseas cambiar la contraseña.',
    'profile_updated' => 'Perfil actualizado correctamente.',

    // Navegación, Accesibilidad y Footer adicionales
    'terms' => 'Términos y condiciones',
    'footer_links' => 'Enlaces',
    'footer_social' => 'Redes sociales',
    'skip_to_content' => 'Saltar al contenido principal',
    'nav_primary' => 'Navegación principal',
    'menu_open' => 'Abrir menú de navegación',
    'menu_close' => 'Cerrar menú de navegación',
    'close' => 'Cerrar',
    'language_switcher' => 'Selector de idioma',
    'carousel_label' => 'Carrusel destacado',
    'slide_pause' => 'Pausar carrusel',
    'slide_play' => 'Reproducir carrusel',

    // Errores HTTP
    'error_404_title' => 'Página no encontrada',
    'error_500_title' => 'Error interno del servidor',
    'back_home' => 'Volver al inicio',

    // Seguridad / Estado de cuenta / Tokens
    'account_deactivated' => 'Tu cuenta ha sido desactivada. Contacta con el administrador.',
    'api_tokens_limit_reached' => 'Has alcanzado el límite máximo de 20 tokens activos.',
    'password_reset_link_sent_if_exists' => 'Si el correo electrónico existe en nuestro sistema, recibirás un enlace para restablecer tu contraseña.',

    // Emails
    'mail_welcome_subject' => 'Bienvenido a :site',
    'mail_welcome_greeting' => '¡Bienvenido, :name!',
    'mail_welcome_thanks' => 'Gracias por registrarte en :site.',
    'mail_welcome_body' => 'Tu cuenta ha sido creada correctamente. Puedes acceder al panel de usuario desde nuestra web.',
    'mail_contact_subject' => 'Nuevo mensaje de contacto: :subject',
    'mail_contact_heading' => 'Nuevo mensaje de contacto',

    // Desde Chipiona — Textos públicos y participación
    'site_tagline' => 'Historia, Mar, Tierra y Memoria Viva de Chipiona',
    'explore_all' => 'Explorar Todo',
    'news_nav' => 'Noticias',
    'proverbs_nav' => 'Refranes',
    'weather_tides_nav' => 'Tiempo y Mareas',
    'phones_nav' => 'Teléfonos',
    'author_legal_nav' => 'Autoría y Legal',
    'contribute_btn' => 'Aportar Contenido',
    'font_size_label' => 'Tamaño de lectura',
    'turnstile_required' => 'Por favor, completa la verificación de seguridad de Cloudflare Turnstile.',
    'turnstile_failed' => 'No se pudo verificar el control de seguridad Turnstile. Inténtalo de nuevo.',
    'social_connected_as' => 'Identificado como :name (:provider)',
    'social_disconnect' => 'Desconectar',
    'social_connect_facebook' => 'Identificarme con Facebook',
    'social_connect_google' => 'Identificarme con Google',
    'correction_sent_success' => '¡Gracias! Tu propuesta de corrección ha sido registrada y será revisada por el equipo.',
    'suggestion_sent_success' => '¡Muchas gracias! Tu aportación sobre Chipiona se ha recibido correctamente.',
    'proverb_sent_success' => '¡Gracias por compartir este refrán chipionero! Se publicará tras su revisión.',
    'comment_sent_success' => '¡Gracias! Tu comentario se ha registrado y será visible tras su moderación.',

    // Descripciones SEO y metadatos de páginas
    'about_seo_description' => 'Conoce la iniciativa de investigación histórica, documental y divulgativa Desde Chipiona, impulsada por Raúl Caro Pastorino para preservar el patrimonio local.',
    'blog_seo_description' => 'Artículos, estudios documentales y reportajes de investigación sobre la historia, monumentos, naturaleza y cultura de Chipiona (Cádiz).',
    'contact_seo_description' => 'Ponte en contacto con el equipo de investigación de Desde Chipiona para consultas, colaboraciones documentales, fuentes de archivo o sugerencias.',
    'cookies_seo_description' => 'Política de cookies técnicas y almacenamiento local de accesibilidad del portal documental Desde Chipiona, sin rastreadores publicitarios.',
    'privacy_seo_description' => 'Política de privacidad, protección de datos personales (RGPD) y condiciones de participación ciudadana en el portal documental Desde Chipiona.',
    'legal_seo_description' => 'Aviso legal, atribución de autoría de Raúl Caro Pastorino, propiedad intelectual y licencia libre AGPLv3 del portal Desde Chipiona.',
    'news_seo_description' => 'Actualidad cultural, nuevos hallazgos documentales en archivos históricos y crónicas sobre el patrimonio marítimo y urbano de Chipiona.',
    'proverbs_seo_description' => 'Refranero popular chipionero: dichos de la mar, los corrales de pesca, el vino moscatel, el campo y la sabiduría tradicional de Chipiona.',
    'weather_seo_description' => 'Previsión meteorológica oficial (AEMET 11016) y tabla diaria de mareas, pleamares, bajamares y coeficientes para los corrales y playas de Chipiona.',
    'phones_seo_description' => 'Directorio actualizado de teléfonos oficiales, emergencias, servicios municipales, salud, seguridad y transporte público en Chipiona (Cádiz).',
    'suggestions_seo_description' => 'Envía fotografías antiguas, documentos históricos, historias familiares o propuestas de nuevos artículos para el archivo documental de Chipiona.',
    'page_number' => 'Página :page',

    // Validaciones y respuestas de API / Formularios
    'validation_email_max' => 'El correo electrónico no puede superar los 255 caracteres.',
    'validation_email_unique' => 'Ya existe una cuenta registrada con este correo electrónico.',
    'validation_password_required' => 'La contraseña es obligatoria.',
    'validation_password_min' => 'La contraseña debe tener al menos 8 caracteres.',
    'validation_password_confirmed' => 'La confirmación de la contraseña no coincide.',
    'validation_token_required' => 'El token de restablecimiento es obligatorio.',
    'validation_subject_max' => 'El asunto no puede superar los 255 caracteres.',
    'api_registered' => 'Usuario registrado correctamente.',
    'api_logged_in' => 'Sesión iniciada correctamente.',
    'api_logged_out' => 'Sesión cerrada correctamente.',
    'api_token_created' => 'Token de API creado correctamente.',
    'api_token_deleted' => 'Token de API revocado correctamente.',
    'api_token_not_found' => 'No se encontró el token solicitado.',
    'api_token_limit_reached' => 'Has alcanzado el límite máximo de :max tokens activos.',
    'confirm_password' => 'Confirmar contraseña',
    'welcome_subject' => 'Bienvenido a Desde Chipiona',

    // Textos de interfaz bilingüe (Navbar, Footer, Cookies, Home y Vistas públicas)
    'top_bar_author' => 'Iniciativa privada e investigación de',
    'top_bar_telegram' => 'Canal Telegram: @desdechipiona',
    'font_size_normal_title' => 'Tamaño de letra normal',
    'font_size_large_title' => 'Tamaño de letra grande',
    'font_size_xlarge_title' => 'Tamaño de letra muy grande',
    'brand_subtitle' => 'Historia, Mar, Tierra y Memoria Viva',
    'cookie_notice_text' => 'Utilizamos únicamente cookies técnicas esenciales de sesión, seguridad anti-spam (Cloudflare Turnstile) y preferencia local de tamaño de lectura. No usamos cookies publicitarias ni de rastreo.',
    'cookie_notice_more' => 'Más información',
    'cookie_notice_accept' => 'Entendido',
    'footer_explore_heading' => 'Explorar Chipiona',
    'footer_services_heading' => 'Servicios y Comunidad',
    'footer_links_legal_heading' => 'Enlaces y Legal',
    'footer_description' => 'Archivo documental e histórico de Chipiona: desde la Turris Caepionis romana y la Carta Puebla de 1477 hasta sus corrales de pesca, playas, flora, fauna y memoria oral.',
    'footer_author_note' => 'Investigación y desarrollo por',
    'Select an option' => 'Selecciona una opción…',

    // ============================================================
    // Textos públicos de vistas principales (Home, Refranes, etc.)
    // ============================================================
    'home_hero_badge_chipiona' => 'Archivo abierto, riguroso y cercano sobre nuestra tierra',
    'home_hero_title_prefix' => 'Todo lo que',
    'home_hero_title_suffix' => 'fue, es y guarda en su memoria',
    'home_hero_subtitle_chipiona' => 'Busca de forma sencilla entre siglos de historia documentada, monumentos, playas, corrales de pesca, fauna, flora, refranes y personajes.',
    'home_search_input_label' => '🔎 ¿Qué quieres descubrir hoy sobre Chipiona?',
    'home_search_placeholder_chipiona' => 'Escribe por ejemplo: Faro, Camaleón, Regla, Maremoto de 1755, Moscatel...',
    'home_search_button_label' => 'Buscar ahora',
    'home_filter_group_title' => '1. Filtrar por tema o apartado:',
    'home_filter_era_title' => '2. Época histórica o era:',
    'home_filter_evidence_title' => '3. Nivel de rigor y evidencia:',
    'home_filter_evidence_all' => 'Todos los niveles de rigor',
    'home_filter_map_only' => 'Solo lugares con mapa GPS',
    'home_filter_clear_all' => 'Limpiar filtros',
    'home_sort_featured' => 'Más destacados',
    'home_sort_title_asc' => 'Alfabético (A-Z)',
    'home_sort_title_desc' => 'Alfabético (Z-A)',
    'home_sort_recent' => 'Añadidos recientemente',
    'home_catalog_section_title' => 'Catálogo Documental de Chipiona',
    'home_catalog_records_found' => ':count fichas encontradas',
    'home_view_ficha' => 'Ver ficha completa',
    'home_no_results_title' => 'No se encontraron fichas con los filtros actuales',
    'home_no_results_desc' => 'Prueba a buscar con palabras más generales o elimina algunos filtros de época o tema.',

    // Refranero (Proverbs)
    'proverbs_header_badge' => '🗣️ Memoria Oral de Nuestros Mayores',
    'proverbs_header_title' => 'Refranero Popular y Dichos de Chipiona',
    'proverbs_header_subtitle' => 'La sabiduría de los antiguos corraleros, marineros y viñistas de Chipiona conservada palabra por palabra. Si en tu casa se dice un refrán o dicho del pueblo, súbelo aquí mismo.',
    'proverbs_search_placeholder' => 'Buscar refrán por palabra, viento, mar o moscatel...',
    'proverbs_search_btn' => 'Buscar',
    'proverbs_clear_btn' => 'Limpiar',
    'proverbs_cat_all' => '🌊 Todos los refranes',
    'proverbs_cat_mar' => '⚓ La Mar, Mareas y Corrales',
    'proverbs_cat_campo' => '🍇 El Campo, Huerta y Moscatel',
    'proverbs_cat_vientos' => '🌬️ Vientos (Levante y Poniente)',
    'proverbs_cat_fiestas' => '🎭 Fiestas y Tradiciones',
    'proverbs_cat_vida' => '🏡 Vida Cotidiana del Pueblo',
    'proverbs_source_oral' => 'Tradición oral / Familia vecina',
    'proverbs_verified_source' => 'Fuente contrastada',
    'proverbs_submit_title' => '¿Conoces otro refrán o dicho popular de Chipiona?',
    'proverbs_submit_desc' => 'Compártelo para que no se pierda. Será revisado y añadido al archivo con tu nombre.',
    'proverbs_form_text' => 'Texto del refrán o dicho *',
    'proverbs_form_meaning' => '¿Qué significa o en qué ocasión se dice?',
    'proverbs_form_category' => 'Ámbito o temática *',
    'proverbs_form_author' => 'Tu nombre o el de tu familia *',
    'proverbs_form_email' => 'Tu correo electrónico (opcional)',
    'proverbs_form_submit' => 'Enviar refrán al archivo',

    // Teléfonos Oficiales (Official Phones)
    'phones_header_badge' => '📞 Guía de Servicio Público Local',
    'phones_header_title' => 'Teléfonos Oficiales de Información y Urgencias de Chipiona',
    'phones_header_subtitle' => 'Directorio claro y con botones de llamada directa pensado para que cualquier vecino o visitante encuentre al instante el teléfono oficial que necesita.',
    'phones_search_placeholder' => 'Buscar servicio, urgencia, farmacia, taxi...',
    'phones_cat_all' => '📞 Todos los teléfonos',
    'phones_cat_emergencias' => '🚨 Emergencias y Seguridad 24h',
    'phones_cat_salud' => '🏥 Salud y Farmacias',
    'phones_cat_ayuntamiento' => '🏛️ Ayuntamiento y Servicios',
    'phones_cat_transporte' => '🚕 Transporte, Taxi y Puerto',
    'phones_cat_cultura' => '🏛️ Turismo, Cultura y Archivo',
    'phones_call_now' => 'Llamar ahora',
    'phones_urgent_label' => 'Urgencias 24h',
    'phones_schedule_label' => 'Horario:',
    'phones_address_label' => 'Dirección:',

    // Tiempo y Mareas (Weather)
    'weather_header_title' => 'El Tiempo y Tabla de Mareas en Chipiona',
    'weather_header_subtitle' => 'Previsión meteorológica oficial para el municipio de Chipiona y horarios diarios de pleamar y bajamar para visitar nuestras playas y observar los corrales de pesca.',
    'weather_today_title' => '☀️ El Tiempo Hoy en Chipiona',
    'weather_updated' => 'Actualizado: :date',
    'weather_temp_max' => 'Máxima prevista',
    'weather_temp_min' => 'Mínima prevista',
    'weather_sky_state' => 'Estado del cielo:',
    'weather_wind' => 'Viento dominante:',
    'weather_rain_prob' => 'Probabilidad de lluvia:',
    'weather_uv' => 'Índice UV máximo:',
    'weather_tides_title' => '🌊 Tabla de Mareas y Corrales Hoy',
    'weather_tides_coef' => 'Coeficiente de marea:',
    'weather_aguaje_badge' => '¡Aguaje vivo! Excelente para ver los corrales',
    'weather_tides_warning' => 'Para visitar los corrales a pie, acude 1 hora antes de la bajamar y nunca sobrepases la marea creciente.',

    // Sugerencias y Aportaciones (Suggestions)
    'suggestions_header_badge' => '✍️ Archivo Colaborativo de Chipiona',
    'suggestions_header_title' => 'Aporta tu memoria, documento o sugerencia',
    'suggestions_header_subtitle' => 'Ayúdanos a enriquecer la historia de Chipiona. Envía fotos antiguas, historias de tus abuelos, correcciones documentales o propuestas de nuevos artículos.',
    'suggestions_type_label' => 'Tipo de aportación *',
    'suggestions_type_correction' => 'Corrección o ampliación de un artículo existente',
    'suggestions_type_new' => 'Propuesta de un nuevo artículo o tema',
    'suggestions_type_photo' => 'Fotografía o documento antiguo para digitalizar',
    'suggestions_type_oral' => 'Memoria oral o historia vecinal',
    'suggestions_target_page' => 'Ficha o artículo al que te refieres (opcional)',
    'suggestions_title_label' => 'Título o tema de tu aportación *',
    'suggestions_description_label' => 'Cuéntanos con detalle tu aportación o corrección *',
    'suggestions_sources_label' => 'Fuentes, libros o recuerdos en los que te basas (opcional)',
    'suggestions_author_label' => 'Tu nombre completo *',
    'suggestions_email_label' => 'Tu correo electrónico de contacto *',
    'suggestions_submit_btn' => 'Enviar aportación al equipo',

    // Autoría, Propiedad Privada y Licencia de Cita
    'home_verified_sources_pill' => 'Contrastado con fuentes oficiales',
    'official_source_singular' => 'fuente oficial',
    'official_sources_plural' => 'fuentes oficiales',
    'official_sources_heading' => 'Fuentes Oficiales y Documentación Pública',
    'official_sources_disclaimer' => 'En cumplimiento de nuestro compromiso de rigor, este artículo cita exclusivamente fuentes procedentes de archivos históricos, organismos gubernamentales e instituciones académicas oficiales, las cuales hemos consultado y empleado como referencia para contrastar la veracidad histórica de los datos expuestos en esta redacción original.',
    'authorship_box_title' => 'Autoría, Propiedad Intelectual y Licencia de Cita',
    'authorship_box_text' => 'Este contenido e investigación documental es obra original y propiedad intelectual privada de Raúl Caro Pastorino. Se permite su difusión y compartición siempre que se cite expresamente al autor, las fuentes documentales y se enlace de forma visible a la página original en Desde Chipiona (Licencia CC BY-NC-SA 4.0).',
    'authorship_how_to_cite' => 'Cómo citar y compartir este artículo:',
    'footer_content_license' => 'Contenidos e investigación: propiedad privada de Raúl Caro Pastorino. Permitido compartir citando obligatoriamente autoría, fuentes y enlace a desdechipiona.es (CC BY-NC-SA 4.0).',
];
