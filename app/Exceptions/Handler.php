<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            // Ambil nama menu dari nama Route (misal dari "uster.koreksi.batal_muat.view")
            $menuName = 'general';
            $request = request();
            
            if ($request && $request->route()) {
                $routeName = $request->route()->getName();
                $parts = explode('.', $routeName);
                
                // Ambil struktur nama menu, contoh: "batal_muat"
                if (isset($parts[2]) && !empty($parts[2])) {
                    $menuName = $parts[2];
                } elseif (isset($parts[1]) && !empty($parts[1])) {
                    $menuName = $parts[1];
                }
            } elseif ($request && $request->segment(1)) {
                // Fallback ke segment URL pertama jika route name tidak ada
                $menuName = $request->segment(1);
            }

            // Generate channel dan file log secara on-the-fly (Dinamis)
            \Illuminate\Support\Facades\Log::build([
                'driver' => 'daily',
                'path' => storage_path('logs/error_' . $menuName . '.log'),
                'days' => 14,
            ])->error("🚨 [ERROR SYSTEM] - Crash pada menu: " . strtoupper($menuName), [
                'Pesan_Error' => $e->getMessage(),
                'File_Yang_Error' => basename($e->getFile()), 
                'Path_Lengkap' => $e->getFile(),
                'Komentar' => 'Periksa sintaks atau variabel pada file ' . basename($e->getFile()) . ' baris ke-' . $e->getLine() . '.',
                'Line' => $e->getLine(),
                'URL' => $request ? $request->fullUrl() : 'N/A'
            ]);
        });
    }
}
