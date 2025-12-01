<x-filament-panels::page>
    <style>
        html,
        body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
        }

        .model-container {
            width: 100%;
            height: 80vh;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            overflow: hidden;
            background: #f8fafc;
        }

        .back-button {
            background: #4E9F3D;
            color: white;
            padding: 8px 15px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            margin-bottom: 15px;
            display: inline-block;
        }

        .back-button:hover {
            background: #348a23;
        }

        .model-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            border-left: 4px solid #3b82f6;
        }

        .controls-info {
            background: #e8f5e8;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            border-left: 4px solid #10b981;
        }

        .error-info {
            background: #f8d7da;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            border-left: 4px solid #dc3545;
            color: #721c24;
        }

        .loading-indicator {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 200px;
            flex-direction: column;
            gap: 10px;
        }

        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3b82f6;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .controls-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px;
            margin-top: 15px;
        }

        .control-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px;
            background: white;
            border-radius: 6px;
            border: 1px solid #e5e7eb;
        }

        .control-icon {
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #3b82f6;
            color: white;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
    </style>

    <!-- Load Google Model Viewer -->
    <script type="module" src="https://unpkg.com/@google/model-viewer/dist/model-viewer.min.js"></script>

    <div class="mb-4 flex items-center gap-4">
        <x-filament::button color="info" tag="a" href="{{ url()->previous() }}">
            ← Back
        </x-filament::button>


    </div>




    <!-- Error Container (Hidden by default) -->
    <div id="errorContainer" class="error-info" style="display: none;">
        <h3 class="font-semibold text-lg mb-2">Error Loading Model</h3>
        <p id="errorMessage"></p>
    </div>

    <!-- Model Viewer Container -->
    <div class="model-container">
        <model-viewer id="modelViewer" src="{{ $modelUrl }}" alt="{{ $modelName }}" camera-controls auto-rotate
            auto-rotate-delay="0" ar ar-modes="webxr scene-viewer quick-look" shadow-intensity="1"
            environment-image="neutral" exposure="1" camera-orbit="0deg 75deg 105%" field-of-view="30deg"
            min-camera-orbit="auto auto 50%" max-camera-orbit="auto auto 400%" min-field-of-view="10deg"
            max-field-of-view="45deg" interaction-policy="allow-when-focused" style="width: 100%; height: 100%;"
            loading="eager">

            <!-- Loading Spinner -->
            <div slot="loading" class="loading-indicator">
                <div class="spinner"></div>
                <p>Loading 3D Model...</p>
            </div>

            <!-- Error Slot -->
            <div slot="error" class="loading-indicator" style="color: #dc3545;">
                <svg style="width: 40px; height: 40px; color: #dc3545;" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z" />
                </svg>
                <p>Failed to load 3D model</p>
            </div>

        </model-viewer>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modelViewer = document.getElementById('modelViewer');
            const fileStatus = document.getElementById('fileStatus');
            const errorContainer = document.getElementById('errorContainer');
            const errorMessage = document.getElementById('errorMessage');

            console.log('Loading 3D model from:', "{{ $modelUrl }}");

            // Model loaded successfully
            modelViewer.addEventListener('load', () => {
                console.log('3D model loaded successfully');
                fileStatus.textContent = 'Model loaded successfully ✓';
                fileStatus.style.color = 'green';
                errorContainer.style.display = 'none';
            });

            // Model loading progress
            modelViewer.addEventListener('progress', (event) => {
                const progress = event.detail.totalProgress * 100;
                console.log('Loading progress:', progress.toFixed(1) + '%');
                fileStatus.textContent = `Loading: ${progress.toFixed(1)}%`;
            });

            // Model error
            modelViewer.addEventListener('error', (event) => {
                console.error('Error loading 3D model:', event.detail);
                fileStatus.textContent = 'Error loading model';
                fileStatus.style.color = 'red';

                errorMessage.textContent = 'Failed to load the 3D model. Please check the console for details.';
                errorContainer.style.display = 'block';

                // Try alternative format if available
                setTimeout(() => {
                    tryAlternativeFormat();
                }, 2000);
            });

            // Model visibility changed
            modelViewer.addEventListener('visibilitychange', (event) => {
                console.log('Model visibility:', event.detail.visible ? 'visible' : 'hidden');
            });

            // Camera change events
            modelViewer.addEventListener('camera-change', (event) => {
                // Optional: Log camera changes for debugging
                // console.log('Camera changed:', event.detail);
            });

            // Enhanced error handling for unsupported formats
            function tryAlternativeFormat() {
                const currentSrc = modelViewer.src;
                console.log('Trying to handle format issues for:', currentSrc);

                // You could implement format conversion logic here
                // For now, just show a more helpful error message
                errorMessage.textContent = 'The 3D model format might not be fully supported by your browser. Try using Chrome or Edge for best compatibility.';
            }

            // Add keyboard controls for better accessibility
            document.addEventListener('keydown', (event) => {
                if (document.activeElement === modelViewer || modelViewer.matches(':hover')) {
                    switch (event.key) {
                        case 'r':
                        case 'R':
                            // Reset camera
                            modelViewer.cameraOrbit = '0deg 75deg 105%';
                            event.preventDefault();
                            break;
                        case '+':
                        case '=':
                            // Zoom in
                            const currentZoom = parseFloat(modelViewer.getAttribute('camera-orbit').split(' ')[2]);
                            modelViewer.cameraOrbit = `0deg 75deg ${Math.max(50, currentZoom * 0.8)}%`;
                            event.preventDefault();
                            break;
                        case '-':
                        case '_':
                            // Zoom out
                            const currentZoomOut = parseFloat(modelViewer.getAttribute('camera-orbit').split(' ')[2]);
                            modelViewer.cameraOrbit = `0deg 75deg ${Math.min(400, currentZoomOut * 1.2)}%`;
                            event.preventDefault();
                            break;
                    }
                }
            });

            // Mobile-specific enhancements
            if ('ontouchstart' in window) {
                // Add touch-specific optimizations
                modelViewer.setAttribute('interaction-policy', 'allow-when-focused');
                modelViewer.style.touchAction = 'pan-y';
            }

            // Debug information
            console.log('Google Model Viewer initialized with settings:', {
                src: modelViewer.src,
                cameraControls: modelViewer.hasAttribute('camera-controls'),
                autoRotate: modelViewer.hasAttribute('auto-rotate'),
                environmentImage: modelViewer.getAttribute('environment-image'),
                exposure: modelViewer.getAttribute('exposure')
            });
        });

        // Handle page visibility changes to pause auto-rotation when not visible
        document.addEventListener('visibilitychange', function () {
            const modelViewer = document.getElementById('modelViewer');
            if (document.hidden) {
                modelViewer.removeAttribute('auto-rotate');
            } else {
                modelViewer.setAttribute('auto-rotate', '');
            }
        });
    </script>
</x-filament-panels::page>