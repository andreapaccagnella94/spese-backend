<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use thiagoalessio\tesseract_ocr\TesseractOCR;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Configura il percorso di Tesseract se specificato nelle variabili d'ambiente
        $tesseractPath = env('TESSERACT_PATH');
        if ($tesseractPath) {
            TesseractOCR::setTesseractPath($tesseractPath);
        }
    }
}
