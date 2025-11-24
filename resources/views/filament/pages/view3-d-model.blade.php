<x-filament-panels::page>
    <style>
        html,
        body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
        }

        #renderCanvas {
            width: 100%;
            height: 80vh;
            border: 1px solid #ccc;
            display: block;
            touch-action: none;
            /* Prevent browser touch actions */
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

        .debug-info {
            background: #fff3cd;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            border-left: 4px solid #ffc107;
        }

        .error-info {
            background: #f8d7da;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            border-left: 4px solid #dc3545;
            color: #721c24;
        }
    </style>

    <div class="mb-4 flex items-center gap-4">
        <x-filament::button color="info" tag="a" href="{{ url()->previous() }}">
            ← Back
        </x-filament::button>

        <h2 class="text-xl font-semibold mb-2">
            Viewing 3D Model: {{ $modelName }}
        </h2>
    </div>

    {{-- <div class="model-info">
        <p><strong>Source:</strong> Kiri Engine Generated Model</p>
        <p><strong>Model URL:</strong> <code id="modelUrl">{{ $modelUrl }}</code></p>
        <p><strong>File Status:</strong> <span id="fileStatus">Loading model...</span></p>
        @if(isset($debugInfo['model_file']))
        <p><strong>File Path:</strong> <code>{{ $debugInfo['model_file'] }}</code></p>
        @endif
    </div>

    <div id="errorContainer" class="error-info" style="display: none;">
        <h4 class="font-bold">Error Loading Model:</h4>
        <p id="errorMessage"></p>
    </div>

    @if(config('app.debug') && isset($debugInfo))
    <div class="debug-info">
        <h4 class="font-bold">Debug Information:</h4>
        <pre class="text-xs">{{ json_encode($debugInfo, JSON_PRETTY_PRINT) }}</pre>
    </div>
    @endif --}}

    <canvas id="renderCanvas"></canvas>

    <script src="https://cdn.babylonjs.com/babylon.js"></script>
    <script src="https://cdn.babylonjs.com/loaders/babylonjs.loaders.min.js"></script>

    <script>
        const canvas = document.getElementById('renderCanvas');
        const engine = new BABYLON.Engine(canvas, true);
        const scene = new BABYLON.Scene(engine);
        scene.clearColor = new BABYLON.Color3(1, 1, 1);

        // Camera setup - optimized for smooth controls
        const camera = new BABYLON.ArcRotateCamera(
            "camera",
            Math.PI / 2,
            Math.PI / 3,
            3,
            BABYLON.Vector3.Zero(),
            scene
        );

        // SMOOTH CAMERA CONTROLS - DESKTOP & MOBILE
        camera.attachControl(canvas, true);

        // Rotation controls - much smoother and slower
        camera.angularSensibilityX = 8000; // Slower rotation (higher value = slower)
        camera.angularSensibilityY = 8000; // Slower rotation

        // Panning controls - smoother panning
        camera.panningSensibility = 5000; // Higher value = slower panning
        camera.panningOriginTarget = BABYLON.Vector3.Zero(); // Smooth panning reference

        // Zoom controls - smooth and precise
        camera.wheelPrecision = 15;              // lower = faster scroll zoom
        camera.wheelDeltaPercentage = 0.01;

        // Pinch controls for mobile - smooth zooming
        camera.pinchPrecision = 40;              // lower = faster
        camera.pinchDeltaPercentage = 0.01;      // higher = faster response
        // Inertia for smooth stops
        camera.inertia = 0.9; // Higher value = more inertia (smoother stops)
        camera.panningInertia = 0.9; // Panning inertia

        // Clipping for close zoom
        camera.minZ = 0.001;
        camera.maxZ = 1000;

        // Light
        const light = new BABYLON.HemisphericLight("light", new BABYLON.Vector3(1, 1, 0), scene);

        // Auto rotation - smoother
        camera.useAutoRotationBehavior = true;
        const autoRotateBehavior = camera.autoRotationBehavior;
        if (autoRotateBehavior) {
            autoRotateBehavior.idleRotationSpeed = 0.1; // Slower auto-rotation
            autoRotateBehavior.idleRotationWaitTime = 3000; // Longer wait before auto-rotate
            autoRotateBehavior.idleRotationSpinUpTime = 2000; // Slower spin-up
            autoRotateBehavior.zoomStopsAnimation = false;
        }

        // Load the model
        const modelUrl = "{{ $modelUrl }}";
        console.log('Loading stored 3D model from:', modelUrl);

        // Use proper parameter order for SceneLoader.Append
        BABYLON.SceneLoader.Append(
            "", // rootUrl
            modelUrl, // sceneFilename
            scene, // scene
            function (scene) {
                // Success callback
                console.log('Model loaded successfully');

                const meshes = scene.meshes.filter(m => m.name !== "__root__");

                if (meshes.length > 0) {
                    const boundingInfo = meshes[0].getBoundingInfo().boundingBox;
                    const center = boundingInfo.centerWorld;
                    const radius = boundingInfo.extendSizeWorld.length();

                    camera.target = center;
                    camera.radius = radius * 2; // Comfortable starting distance

                    // Smooth zoom limits
                    camera.lowerRadiusLimit = radius * 0.001; // Very close
                    camera.upperRadiusLimit = radius * 10;   // Reasonable far limit

                    // Dynamic sensitivity based on model size for consistent feel
                    const baseSensitivity = radius / 5;
                    camera.angularSensibilityX = 8000 * baseSensitivity;
                    camera.angularSensibilityY = 8000 * baseSensitivity;
                    camera.panningSensibility = 5000 * baseSensitivity;

                    document.getElementById('fileStatus').textContent = 'Model loaded successfully ✓';
                    document.getElementById('fileStatus').style.color = 'green';

                    console.log('Camera configured for smooth controls:', {
                        radius: radius,
                        angularSensibility: camera.angularSensibilityX,
                        panningSensibility: camera.panningSensibility,
                        wheelPrecision: camera.wheelPrecision
                    });
                } else {
                    console.warn('No meshes found in the model');
                    document.getElementById('fileStatus').textContent = 'No meshes found in model';
                    document.getElementById('fileStatus').style.color = 'orange';
                }
            },
            function (progress) {
                // Progress callback
                console.log('Loading progress:', progress);
                if (progress.lengthComputable) {
                    const percent = (progress.loaded / progress.total * 100).toFixed(1);
                    document.getElementById('fileStatus').textContent = `Loading: ${percent}%`;
                }
            },
            function (scene, message, exception) {
                // Error callback
                console.error('Error loading model:', message, exception);
                document.getElementById('fileStatus').textContent = 'Error loading model';
                document.getElementById('fileStatus').style.color = 'red';

                // Show error to user
                document.getElementById('errorContainer').style.display = 'block';
                document.getElementById('errorMessage').textContent = message || 'Unknown error occurred';

                console.error('Full error details:', {
                    message: message,
                    exception: exception,
                    modelUrl: modelUrl
                });

                // Try alternative loading method
                setTimeout(() => {
                    console.log('Trying alternative loader...');
                    tryAlternativeLoader();
                }, 1000);
            }
        );

        function tryAlternativeLoader() {
            console.log('Using alternative loader...');

            // Alternative loading method - create a new scene
            const newScene = new BABYLON.Scene(engine);
            newScene.clearColor = new BABYLON.Color3(1, 1, 1);

            // Copy camera with smooth settings
            camera.scene = newScene;
            light.scene = newScene;

            // Try loading with different method
            BABYLON.SceneLoader.Load(
                "", // rootUrl
                modelUrl, // sceneFilename
                newScene, // scene
                function (newScene) {
                    // Success callback
                    console.log('Model loaded with alternative method');
                    document.getElementById('fileStatus').textContent = 'Model loaded (alternative method) ✓';
                    document.getElementById('fileStatus').style.color = 'green';
                    document.getElementById('errorContainer').style.display = 'none';

                    // Configure smooth controls for new scene
                    const meshes = newScene.meshes.filter(m => m.name !== "__root__");
                    if (meshes.length > 0) {
                        const boundingInfo = meshes[0].getBoundingInfo().boundingBox;
                        const radius = boundingInfo.extendSizeWorld.length();

                        camera.lowerRadiusLimit = radius * 0.001;
                        camera.upperRadiusLimit = radius * 10;

                        const baseSensitivity = radius / 5;
                        camera.angularSensibilityX = 8000 * baseSensitivity;
                        camera.angularSensibilityY = 8000 * baseSensitivity;
                        camera.panningSensibility = 5000 * baseSensitivity;
                    }

                    // Update scene reference
                    scene.dispose();
                    window.currentScene = newScene;
                },
                function (progress) {
                    // Progress callback
                    console.log('Alternative loader progress:', progress);
                },
                function (newScene, message, exception) {
                    // Error callback
                    console.error('Alternative loader also failed:', message, exception);
                    document.getElementById('fileStatus').textContent = 'All loading methods failed';
                    document.getElementById('errorMessage').textContent = 'All loading attempts failed: ' + message;
                }
            );
        }

        // Enhanced input handling for ultra-smooth controls
        canvas.addEventListener('wheel', (event) => {
            if (document.activeElement === canvas || canvas.matches(':hover')) {
                event.preventDefault();
            }
        }, { passive: false });

        // Prevent context menu on canvas for better mobile experience
        canvas.addEventListener('contextmenu', (event) => {
            event.preventDefault();
        });

        engine.runRenderLoop(() => {
            const currentScene = window.currentScene || scene;
            currentScene.render();
        });

        window.addEventListener('resize', () => engine.resize());

        // Debug info for smooth controls
        console.log('Smooth camera controls configured:', {
            angularSensibilityX: camera.angularSensibilityX,
            angularSensibilityY: camera.angularSensibilityY,
            panningSensibility: camera.panningSensibility,
            wheelPrecision: camera.wheelPrecision,
            pinchPrecision: camera.pinchPrecision,
            inertia: camera.inertia
        });
    </script>
</x-filament-panels::page>