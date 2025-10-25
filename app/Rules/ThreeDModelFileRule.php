<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;
use Closure;

class ThreeDModelFileRule implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$value instanceof UploadedFile) {
            $fail('The file must be a valid upload.');
            return;
        }

        // Check file extension
        $extension = strtolower($value->getClientOriginalExtension());
        $allowedExtensions = config('3d-models.allowed_extensions', ['glb', 'gltf']);
        if (!in_array($extension, $allowedExtensions)) {
            $fail('The file must be a ' . implode(' or ', array_map(fn($ext) => ".$ext", $allowedExtensions)) . ' file.');
            return;
        }

        // Check file size
        $maxSize = config('3d-models.max_file_size', 104857600);
        if ($value->getSize() > $maxSize) {
            $maxSizeMB = round($maxSize / 1024 / 1024);
            $fail("The file size must not exceed {$maxSizeMB}MB.");
            return;
        }

        // Additional validation for GLTF files
        if ($extension === 'gltf' && config('3d-models.validation.validate_gltf_json', true)) {
            $content = file_get_contents($value->getPathname());
            if ($content === false) {
                $fail('Unable to read the GLTF file.');
                return;
            }

            // Basic JSON validation for GLTF files
            $json = json_decode($content, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $fail('The GLTF file must contain valid JSON.');
                return;
            }

            // Check for required GLTF properties
            if (!isset($json['asset']) || !isset($json['asset']['version'])) {
                $fail('The GLTF file is missing required asset information.');
                return;
            }
        }

        // For GLB files, check the binary header
        if ($extension === 'glb' && config('3d-models.validation.validate_glb_header', true)) {
            $handle = fopen($value->getPathname(), 'rb');
            if ($handle === false) {
                $fail('Unable to read the GLB file.');
                return;
            }

            // Read first 4 bytes to check GLB magic number
            $magic = fread($handle, 4);
            fclose($handle);

            // GLB files should start with "glTF" (0x46546C67)
            if ($magic !== 'glTF') {
                $fail('The GLB file format is invalid.');
                return;
            }
        }
    }
}
