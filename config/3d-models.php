<?php

return [
    /*
    |--------------------------------------------------------------------------
    | 3D Model Upload Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for 3D model file uploads and processing
    |
    */

    // Maximum file size in bytes (100MB)
    'max_file_size' => env('3D_MODEL_MAX_SIZE', 104857600),

    // Maximum file size in KB for Filament (100MB)
    'max_file_size_kb' => env('3D_MODEL_MAX_SIZE_KB', 102400),

    // Allowed file extensions
    'allowed_extensions' => ['glb', 'gltf'],

    // Allowed MIME types
    'allowed_mime_types' => [
        'application/octet-stream',  // For .glb files
        'model/gltf+json',          // For .gltf files
        'application/json',         // Alternative for .gltf files
        'text/plain',               // Fallback for .gltf files
        'model/gltf-binary',        // Standard MIME type for GLB
    ],

    // Storage configuration
    'storage' => [
        'disk' => env('3D_MODEL_DISK', 'public'),
        'directory' => env('3D_MODEL_DIRECTORY', 'product-models'),
        'visibility' => env('3D_MODEL_VISIBILITY', 'public'),
    ],

    // Validation settings
    'validation' => [
        'strict_mime_check' => env('3D_MODEL_STRICT_MIME', false),
        'validate_gltf_json' => env('3D_MODEL_VALIDATE_GLTF', true),
        'validate_glb_header' => env('3D_MODEL_VALIDATE_GLB', true),
    ],

    // Performance settings
    'performance' => [
        'chunk_size' => env('3D_MODEL_CHUNK_SIZE', 8192), // 8KB chunks for upload
        'timeout' => env('3D_MODEL_TIMEOUT', 300), // 5 minutes
        'memory_limit' => env('3D_MODEL_MEMORY_LIMIT', '512M'),
    ],
];
