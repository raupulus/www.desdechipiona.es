<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * Comando Artisan para instalar y configurar inicialmente el proyecto
 * (entorno, dependencias, clave, enlace de storage, migraciones, seeders y assets).
 */
class ProjectInstall extends Command
{
    /**
     * La firma del comando en consola.
     *
     * @var string
     */
    protected $signature = 'project:install {--force : Ejecutar sin confirmación}';

    /**
     * La descripción del comando.
     *
     * @var string
     */
    protected $description = 'Instala y configura el proyecto';

    /**
     * Ejecuta la instalación guiada o desatendida del proyecto.
     */
    public function handle(): int
    {
        $this->info('=== Instalación del proyecto ===');

        if ($this->laravel->isProduction() && ! $this->option('force') && ! $this->confirm('⚠ Estás en entorno de producción. ¿Deseas continuar con project:install?', false)) {
            $this->warn('Instalación cancelada.');

            return Command::FAILURE;
        }

        if (! file_exists(base_path('.env'))) {
            copy(base_path('.env.example'), base_path('.env'));
            $this->info('✓ .env creado');
        }

        if ($this->option('force') || $this->confirm('¿Ejecutar composer install?', true)) {
            $exitCode = 0;
            passthru('composer install', $exitCode);
            if ($exitCode !== 0) {
                $this->error('Error al ejecutar composer install.');

                return Command::FAILURE;
            }
        }

        if ($this->option('force') || $this->confirm('¿Ejecutar pnpm install?', true)) {
            $exitCode = 0;
            passthru('pnpm install', $exitCode);
            if ($exitCode !== 0) {
                $this->error('Error al ejecutar pnpm install.');

                return Command::FAILURE;
            }
        }

        if (empty(config('app.key')) && ($this->option('force') || $this->confirm('¿Generar key?', true))) {
            if ($this->call('key:generate', ['--force' => $this->option('force')]) !== Command::SUCCESS) {
                return Command::FAILURE;
            }
        }

        if (! file_exists(public_path('storage'))) {
            if ($this->call('storage:link') !== Command::SUCCESS) {
                return Command::FAILURE;
            }
        }

        if ($this->option('force') || $this->confirm('¿Ejecutar migraciones?', true)) {
            if ($this->call('migrate', ['--force' => $this->option('force')]) !== Command::SUCCESS) {
                return Command::FAILURE;
            }
        }

        $this->copySeedImages();

        if ($this->option('force') || $this->confirm('¿Ejecutar seeders?', true)) {
            if ($this->call('db:seed', ['--force' => $this->option('force')]) !== Command::SUCCESS) {
                return Command::FAILURE;
            }
        }

        if ($this->option('force') || $this->confirm('¿Compilar assets frontend (pnpm run build)?', true)) {
            $exitCode = 0;
            passthru('pnpm run build', $exitCode);
            if ($exitCode !== 0) {
                $this->error('Error al compilar los assets con pnpm run build.');

                return Command::FAILURE;
            }
        }

        $this->info('=== Instalación completada ===');

        return Command::SUCCESS;
    }

    /**
     * Copia las imágenes de muestra al storage para que los seeders tengan imágenes reales.
     * Copia public/images/default/slides/ → storage/app/public/slides/
     */
    protected function copySeedImages(): void
    {
        $sourceSlidesDir = public_path('images/default/slides');
        $destSlidesDir = storage_path('app/public/slides');

        if (! is_dir($sourceSlidesDir)) {
            $this->warn('⚠ No se encontró el directorio de slides de muestra: '.$sourceSlidesDir);

            return;
        }

        File::ensureDirectoryExists($destSlidesDir);

        $copied = 0;
        foreach (File::files($sourceSlidesDir) as $file) {
            $dest = $destSlidesDir.DIRECTORY_SEPARATOR.$file->getFilename();
            if (! file_exists($dest)) {
                File::copy($file->getPathname(), $dest);
                $copied++;
            }
        }

        if ($copied > 0) {
            $this->info("✓ {$copied} imágenes de slides copiadas al storage");
        } else {
            $this->info('✓ Imágenes de slides ya presentes en storage');
        }
    }
}
