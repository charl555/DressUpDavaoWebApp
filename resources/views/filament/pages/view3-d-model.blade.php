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
    </style>

    <div class="mb-4 flex items-center gap-4">
        <x-filament::button color="info" tag="a" href="{{ url()->previous() }}">
            ← Back
        </x-filament::button>

        <h2 class="text-xl font-semibold mb-2">
            Viewing 3D Model: {{ $product->name }}
        </h2>
    </div>

    <canvas id="renderCanvas"></canvas>

    <script src="https://cdn.babylonjs.com/babylon.js"></script>
    <script src="https://cdn.babylonjs.com/loaders/babylonjs.loaders.min.js"></script>

    <script>
        const canvas = document.getElementById('renderCanvas');
        const engine = new BABYLON.Engine(canvas, true);
        const scene = new BABYLON.Scene(engine);
        scene.clearColor = new BABYLON.Color3(1, 1, 1);

        // Camera setup
        const camera = new BABYLON.ArcRotateCamera(
            "camera",
            Math.PI / 2,
            Math.PI / 3,
            3,
            BABYLON.Vector3.Zero(),
            scene
        );
        camera.attachControl(canvas, true);

        // Sensitivity adjustments
        camera.wheelPrecision = 100;
        camera.panningSensibility = 2000;
        camera.angularSensibilityX = 4000;
        camera.angularSensibilityY = 4000;

        camera.pinchPrecision = 200;
        camera.pinchDeltaPercentage = 0.002;

        // Light
        const light = new BABYLON.HemisphericLight("light", new BABYLON.Vector3(1, 1, 0), scene);

        // Auto rotation
        camera.useAutoRotationBehavior = true;
        const autoRotateBehavior = camera.autoRotationBehavior;
        if (autoRotateBehavior) {
            autoRotateBehavior.idleRotationSpeed = 0.2;
            autoRotateBehavior.idleRotationWaitTime = 2000;
            autoRotateBehavior.idleRotationSpinUpTime = 1000;
        }

        // ---------------------------------------
        // 🔥 HARD-CODED DEFAULT CLIPPING PLANES
        // ---------------------------------------
        const clippingData = @json($clippingData);

        // Default clipping radius (closer to center)
        const DEFAULT_X_POS = 0.4;
        const DEFAULT_X_NEG = -0.4;
        const DEFAULT_Z_POS = 0.4;
        const DEFAULT_Z_NEG = -0.4;

        // Apply defaults first
        scene.clipPlane = new BABYLON.Plane(1, 0, 0, -DEFAULT_X_POS);
        scene.clipPlane2 = new BABYLON.Plane(-1, 0, 0, DEFAULT_X_NEG);
        scene.clipPlane3 = new BABYLON.Plane(0, 0, 1, -DEFAULT_Z_POS);
        scene.clipPlane4 = new BABYLON.Plane(0, 0, -1, DEFAULT_Z_NEG);

        // Apply saved clipping planes ONLY if they exist 
        if (clippingData && Object.keys(clippingData).length > 0) {
            scene.clipPlane = new BABYLON.Plane(1, 0, 0, -(clippingData.xPos ?? DEFAULT_X_POS));
            scene.clipPlane2 = new BABYLON.Plane(-1, 0, 0, (clippingData.xNeg ?? DEFAULT_X_NEG));
            scene.clipPlane3 = new BABYLON.Plane(0, 0, 1, -(clippingData.zPos ?? DEFAULT_Z_POS));
            scene.clipPlane4 = new BABYLON.Plane(0, 0, -1, (clippingData.zNeg ?? DEFAULT_Z_NEG));
        }
        // ---------------------------------------

        // Load model and auto-fit camera
        BABYLON.SceneLoader.Append("", "{{ $modelUrl }}", scene, function () {
            const meshes = scene.meshes.filter(m => m.name !== "__root__");

            if (meshes.length > 0) {
                const boundingInfo = meshes[0].getBoundingInfo().boundingBox;
                const center = boundingInfo.centerWorld;
                const radius = boundingInfo.extendSizeWorld.length();

                camera.target = center;
                camera.radius = radius * 1.5;
                camera.lowerRadiusLimit = radius * 0.8;
                camera.upperRadiusLimit = radius * 5;
            }
        });

        // Prevent page scrolling while using canvas
        canvas.addEventListener('wheel', (event) => {
            if (document.activeElement === canvas || canvas.matches(':hover')) {
                event.preventDefault();
            }
        }, { passive: false });

        engine.runRenderLoop(() => scene.render());
        window.addEventListener('resize', () => engine.resize());
    </script>
</x-filament-panels::page>