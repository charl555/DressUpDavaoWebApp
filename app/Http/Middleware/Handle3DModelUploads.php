<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Closure;

class Handle3DModelUploads
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if this is a 3D model upload request
        if ($this->is3DModelUpload($request)) {
            // Increase memory limit for large file processing
            $memoryLimit = config('3d-models.performance.memory_limit', '512M');
            ini_set('memory_limit', $memoryLimit);

            // Increase execution time for large file uploads
            $timeout = config('3d-models.performance.timeout', 300);
            set_time_limit($timeout);

            // Note: upload_max_filesize and post_max_size cannot be changed at runtime
            // These must be set in php.ini or .htaccess
            // We can only adjust memory and execution time here
        }

        return $next($request);
    }

    /**
     * Check if the request is for 3D model upload
     */
    private function is3DModelUpload(Request $request): bool
    {
        // Check if it's a Filament admin route for 3D model management
        if ($request->is('admin/*') && $request->hasFile('model_file')) {
            return true;
        }

        // Check if any uploaded file has 3D model extensions
        foreach ($request->allFiles() as $file) {
            if (is_array($file)) {
                foreach ($file as $singleFile) {
                    if ($this->is3DModelFile($singleFile)) {
                        return true;
                    }
                }
            } else {
                if ($this->is3DModelFile($file)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Check if a file is a 3D model file
     */
    private function is3DModelFile($file): bool
    {
        if (!$file || !method_exists($file, 'getClientOriginalExtension')) {
            return false;
        }

        $extension = strtolower($file->getClientOriginalExtension());
        $allowedExtensions = config('3d-models.allowed_extensions', ['glb', 'gltf']);

        return in_array($extension, $allowedExtensions);
    }
}
