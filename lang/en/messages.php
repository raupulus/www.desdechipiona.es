<?php

return [
    // Navigation
    'welcome' => 'Welcome',
    'home' => 'Home',
    'about' => 'About us',
    'contact' => 'Contact',
    'blog' => 'Blog',
    'pages' => 'Pages',
    'privacy' => 'Privacy policy',
    'cookies' => 'Cookies policy',

    // Authentication
    'login' => 'Login',
    'register' => 'Register',
    'logout' => 'Logout',
    'profile' => 'Profile',

    // Actions
    'settings' => 'Settings',
    'search' => 'Search',
    'read_more' => 'Read more',
    'back' => 'Back',
    'save' => 'Save',
    'cancel' => 'Cancel',
    'delete' => 'Delete',
    'edit' => 'Edit',
    'create' => 'Create',
    'update' => 'Update',

    // Status
    'published' => 'Published',
    'draft' => 'Draft',
    'featured' => 'Featured',
    'active' => 'Active',
    'inactive' => 'Inactive',

    // General
    'all_rights_reserved' => 'All rights reserved',
    'language' => 'Language',
    'no_results' => 'No results found',

    // Contact
    'contact_success' => 'Message sent successfully. We will get back to you soon.',
    'contact_error' => 'There was an error sending the message. Please try again.',

    // ============================================================
    // Home — Hero
    // ============================================================
    'home_hero_badge' => 'Laravel 13 · Filament 5 · Tailwind 4 · Vue 3',
    'home_hero_title' => 'Skeleton Laravel Project',
    'home_hero_description' => 'Production-ready base template. Includes full authentication, Filament admin panel, REST API with Sanctum, translations, SEO, Docker and everything you need to start your next project in minutes.',
    'home_hero_btn_docs' => 'Documentation on Blog',
    'home_hero_btn_admin' => 'Admin Panel',
    'home_hero_btn_api' => 'API Docs',

    // Home — Quick Start
    'home_quickstart_title' => 'Quick start',

    // Home — Features
    'home_features_title' => 'Everything you need, already implemented',
    'home_features_description' => 'Don\'t start from scratch. This skeleton covers the most common features of any professional Laravel project.',

    'home_feat_auth_title' => 'Full authentication',
    'home_feat_auth_desc' => 'Login, registration, email verification and password recovery — for both web and REST API with Sanctum.',

    'home_feat_filament_title' => 'Filament 5 Admin Panel',
    'home_feat_filament_desc' => 'Two separate panels: /admin for administrators and /panel for users. Full CRUD for all models.',

    'home_feat_api_title' => 'Documented REST API',
    'home_feat_api_desc' => 'Versioned API at /api/v1 with Sanctum authentication. Auto-generated documentation with Scribe at /docs.',

    'home_feat_i18n_title' => 'Bilingual ES / EN',
    'home_feat_i18n_desc' => 'Spanish and English across the entire interface. Models with translatable fields via spatie/laravel-translatable. Language selector on the frontend.',

    'home_feat_seo_title' => 'Complete SEO',
    'home_feat_seo_desc' => 'Meta tags, Open Graph, Twitter Cards, JSON-LD, hreflang, canonical and XML sitemap auto-generated with spatie/laravel-sitemap.',

    'home_feat_content_title' => 'Posts, Pages & Categories',
    'home_feat_content_desc' => 'Complete content system with statuses (draft/published), SEO fields, images, categories and translatable fields. All manageable from Filament.',

    'home_feat_settings_title' => 'Dynamic settings',
    'home_feat_settings_desc' => 'Site configuration manageable from the panel: title, description, social media, contact and SEO. Accessible anywhere with setting(\'key\').',

    'home_feat_docker_title' => 'Docker included',
    'home_feat_docker_desc' => 'Full Docker setup ready to use: PHP 8.5, Nginx, PostgreSQL. Development and production environments configured in docker-compose.yml.',

    'home_feat_tests_title' => 'Tests included',
    'home_feat_tests_desc' => 'Feature tests for web and API authentication, panel access, contact form and unit tests for key services with PHPUnit.',

    // Home — Stack
    'home_stack_title' => 'Technology stack',

    // Home — Panels
    'home_panels_title' => 'Included panels',
    'home_panels_description' => 'Access the panels with seeder credentials in development',
    'home_panel_admin_title' => 'Admin Panel',
    'home_panel_admin_desc' => 'Full system management',
    'home_panel_user_title' => 'User Panel',
    'home_panel_user_desc' => 'Profile and own API tokens',
    'home_panel_api_title' => 'API Docs',
    'home_panel_api_desc' => 'Auto-generated documentation',

    // Home — Blog
    'home_blog_subtitle' => 'Blog & Documentation',

    // Carousel (slide-carousel)
    'slide_prev' => 'Previous slide',
    'slide_next' => 'Next slide',
    'slide_go_to' => 'Go to slide :n',

    // Pagination
    'pagination' => 'Pagination',

    // User panel — Welcome widget
    'welcome_greeting' => 'Hello, :name!',
    'active_tokens_count' => 'You have :count active API token(s).',

    // Legal pages — Default texts
    'privacy_default_text' => 'Privacy policy content is managed from the admin panel (Sections → zone "privacy").',
    'cookies_default_text' => 'Cookies policy content is managed from the admin panel (Sections → zone "cookies").',

    // Contact — Form
    'contact_name' => 'Name',
    'contact_email' => 'Email',
    'contact_subject' => 'Subject',
    'contact_message' => 'Message',
    'contact_privacy_accept' => 'I accept the',
    'contact_privacy_link' => 'privacy policy',
    'contact_send' => 'Send message',

    // Validations — Contact
    'validation_name_required' => 'Name is required.',
    'validation_name_max' => 'Name cannot exceed 255 characters.',
    'validation_email_required' => 'Email is required.',
    'validation_email_email' => 'Email is not valid.',
    'validation_subject_required' => 'Subject is required.',
    'validation_message_required' => 'Message is required.',
    'validation_message_max' => 'Message cannot exceed 5,000 characters.',
    'validation_privacy_required' => 'You must accept the privacy policy.',
    'validation_privacy_accepted' => 'You must accept the privacy policy.',
    'validation_recaptcha_required' => 'Security verification is required. Reload the page and try again.',
    'validation_recaptcha_failed' => 'We could not verify that you are not a robot. Please try again.',

    // User panel — API Tokens
    'api_tokens_description' => 'API tokens allow you to securely connect external applications to your account. Each token acts as a "temporary password" that you can revoke at any time without affecting your main password.',
    'api_tokens_create_title' => 'Create new token',
    'api_tokens_placeholder' => 'Token name (e.g. My application)',
    'api_tokens_created_msg' => 'Token generated — copy it now, it won\'t be shown again:',
    'api_tokens_copy' => 'Copy',
    'api_tokens_copied' => 'Copied!',
    'api_tokens_list_title' => 'Active tokens',
    'api_tokens_col_name' => 'Name',
    'api_tokens_col_created' => 'Created',
    'api_tokens_col_last_used' => 'Last used',
    'api_tokens_col_expires' => 'Expires',
    'api_tokens_col_actions' => 'Actions',
    'api_tokens_empty' => 'You don\'t have any API tokens created.',
    'api_tokens_delete_confirm' => 'Are you sure you want to delete this token?',
    'api_tokens_never' => 'Never',
    'api_tokens_no_expiry' => 'No expiry',
    'api_tokens_created_notify' => 'Token created successfully.',
    'api_tokens_deleted_notify' => 'Token deleted successfully.',
    'validation_token_name_required' => 'Token name is required.',
    'validation_token_name_max' => 'Name cannot exceed 255 characters.',

    // User panel — Profile
    'profile_email_helper' => 'If you need to change your email, please contact the administrator.',
    'profile_password_helper' => 'Leave blank if you don\'t want to change your password.',
    'profile_updated' => 'Profile updated successfully.',

    // Navigation, Accessibility & Footer additions
    'terms' => 'Terms & conditions',
    'footer_links' => 'Links',
    'footer_social' => 'Social media',
    'skip_to_content' => 'Skip to main content',
    'nav_primary' => 'Primary navigation',
    'menu_open' => 'Open navigation menu',
    'menu_close' => 'Close navigation menu',
    'close' => 'Close',
    'language_switcher' => 'Language selector',
    'carousel_label' => 'Featured carousel',
    'slide_pause' => 'Pause carousel',
    'slide_play' => 'Play carousel',

    // HTTP Errors
    'error_404_title' => 'Page not found',
    'error_500_title' => 'Internal server error',
    'back_home' => 'Back to home',

    // Security / Account status / Tokens
    'account_deactivated' => 'Your account has been deactivated. Please contact the administrator.',
    'api_tokens_limit_reached' => 'You have reached the maximum limit of 20 active tokens.',
    'password_reset_link_sent_if_exists' => 'If the email address exists in our system, you will receive a password reset link.',

    // Emails
    'mail_welcome_subject' => 'Welcome to :site',
    'mail_welcome_greeting' => 'Welcome, :name!',
    'mail_welcome_thanks' => 'Thank you for registering at :site.',
    'mail_welcome_body' => 'Your account has been created successfully. You can access the user panel from our website.',
    'mail_contact_subject' => 'New contact message: :subject',
    'mail_contact_heading' => 'New contact message',

    // Desde Chipiona — Public texts & participation
    'site_tagline' => 'History, Sea, Land and Living Memory of Chipiona',
    'explore_all' => 'Explore All',
    'news_nav' => 'News',
    'proverbs_nav' => 'Proverbs',
    'weather_tides_nav' => 'Weather & Tides',
    'phones_nav' => 'Official Phones',
    'author_legal_nav' => 'Authorship & Legal',
    'contribute_btn' => 'Contribute Content',
    'font_size_label' => 'Reading size',
    'turnstile_required' => 'Please complete the Cloudflare Turnstile security verification.',
    'turnstile_failed' => 'Could not verify the Turnstile security check. Please try again.',
    'social_connected_as' => 'Identified as :name (:provider)',
    'social_disconnect' => 'Disconnect',
    'social_connect_facebook' => 'Identify with Facebook',
    'social_connect_google' => 'Identify with Google',
    'correction_sent_success' => 'Thank you! Your correction proposal has been recorded and will be reviewed.',
    'suggestion_sent_success' => 'Thank you! Your contribution about Chipiona has been received.',
    'proverb_sent_success' => 'Thank you for sharing this Chipiona proverb! It will be published after review.',
    'comment_sent_success' => 'Thank you! Your comment has been recorded and will be visible after moderation.',

    // SEO descriptions & page metadata
    'about_seo_description' => 'Learn about the historical, documentary, and educational research initiative Desde Chipiona, led by Raúl Caro Pastorino to preserve local heritage.',
    'blog_seo_description' => 'Articles, documentary studies, and research reports on the history, monuments, nature, and culture of Chipiona (Cádiz).',
    'contact_seo_description' => 'Get in touch with the Desde Chipiona research team for inquiries, documentary collaborations, archival sources, or suggestions.',
    'cookies_seo_description' => 'Technical cookie and local accessibility storage policy of the Desde Chipiona documentary portal, free of advertising trackers.',
    'privacy_seo_description' => 'Privacy policy, personal data protection (GDPR), and citizen participation terms on the Desde Chipiona documentary portal.',
    'legal_seo_description' => 'Legal notice, authorship attribution of Raúl Caro Pastorino, intellectual property, and AGPLv3 open license of Desde Chipiona.',
    'news_seo_description' => 'Cultural news, new documentary findings in historical archives, and chronicles on the maritime and urban heritage of Chipiona.',
    'proverbs_seo_description' => 'Popular proverbs of Chipiona: sayings about the sea, fishing corrals, Moscatel wine, the countryside, and traditional wisdom.',
    'weather_seo_description' => 'Official weather forecast (AEMET 11016) and daily tide table, high tides, low tides, and coefficients for Chipiona beaches and fishing corrals.',
    'phones_seo_description' => 'Updated directory of official phone numbers, emergencies, municipal services, healthcare, security, and public transport in Chipiona (Cádiz).',
    'suggestions_seo_description' => 'Submit vintage photographs, historical documents, family stories, or proposals for new articles to the Chipiona documentary archive.',
    'page_number' => 'Page :page',

    // API & Form validation messages
    'validation_email_max' => 'The email address may not be greater than 255 characters.',
    'validation_email_unique' => 'An account with this email address already exists.',
    'validation_password_required' => 'The password field is required.',
    'validation_password_min' => 'The password must be at least 8 characters.',
    'validation_password_confirmed' => 'The password confirmation does not match.',
    'validation_token_required' => 'The password reset token is required.',
    'validation_subject_max' => 'The subject may not be greater than 255 characters.',
    'api_registered' => 'User registered successfully.',
    'api_logged_in' => 'Logged in successfully.',
    'api_logged_out' => 'Logged out successfully.',
    'api_token_created' => 'API token created successfully.',
    'api_token_deleted' => 'API token revoked successfully.',
    'api_token_not_found' => 'The requested token was not found.',
    'api_token_limit_reached' => 'You have reached the maximum limit of :max active tokens.',
    'confirm_password' => 'Confirm password',
    'welcome_subject' => 'Welcome to Desde Chipiona',

    // Bilingual UI texts (Navbar, Footer, Cookies, Home & Public views)
    'top_bar_author' => 'Private initiative & research by',
    'top_bar_telegram' => 'Telegram Channel: @desdechipiona',
    'font_size_normal_title' => 'Normal font size',
    'font_size_large_title' => 'Large font size',
    'font_size_xlarge_title' => 'Extra large font size',
    'brand_subtitle' => 'History, Sea, Land and Living Memory',
    'cookie_notice_text' => 'We only use essential technical session cookies, anti-spam security (Cloudflare Turnstile), and local reading size preferences. No advertising or tracking cookies are used.',
    'cookie_notice_more' => 'Learn more',
    'cookie_notice_accept' => 'Got it',
    'footer_explore_heading' => 'Explore Chipiona',
    'footer_services_heading' => 'Services & Community',
    'footer_links_legal_heading' => 'Links & Legal',
    'footer_description' => 'Documentary and historical archive of Chipiona: from the Roman Turris Caepionis and the 1477 Town Charter to its fishing corrals, beaches, flora, fauna, and oral memory.',
    'footer_author_note' => 'Research and development by',
    'Select an option' => 'Select an option…',
];
