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

        // 🎯 Adjust sensitivity for slower control
        camera.wheelPrecision = 100;         // Zoom speed (higher = slower)
        camera.panningSensibility = 2000;    // Panning sensitivity (higher = slower)
        camera.angularSensibilityX = 4000;   // Rotation horizontal (higher = slower)
        camera.angularSensibilityY = 4000;   // Rotation vertical (higher = slower)

        // 📱 Adjust for mobile gestures (slower pinch & swipe)
        camera.pinchPrecision = 200;         // Pinch zoom slower
        camera.pinchDeltaPercentage = 0.002; // Fine-tuned pinch control

        // Light setup
        const light = new BABYLON.HemisphericLight("light", new BABYLON.Vector3(1, 1, 0), scene);

        // Auto rotation
        camera.useAutoRotationBehavior = true;
        const autoRotateBehavior = camera.autoRotationBehavior;
        if (autoRotateBehavior) {
            autoRotateBehavior.idleRotationSpeed = 0.2;
            autoRotateBehavior.idleRotationWaitTime = 2000;
            autoRotateBehavior.idleRotationSpinUpTime = 1000;
        }

        // Clipping planes
        const clippingData = @json($clippingData);
        scene.clipPlane = new BABYLON.Plane(1, 0, 0, 10);
        scene.clipPlane2 = new BABYLON.Plane(-1, 0, 0, 10);
        scene.clipPlane3 = new BABYLON.Plane(0, 0, 1, 10);
        scene.clipPlane4 = new BABYLON.Plane(0, 0, -1, 10);

        if (clippingData) {
            scene.clipPlane = new BABYLON.Plane(1, 0, 0, -(clippingData.xPos ?? 100));
            scene.clipPlane2 = new BABYLON.Plane(-1, 0, 0, clippingData.xNeg ?? -100);
            scene.clipPlane3 = new BABYLON.Plane(0, 0, 1, -(clippingData.zPos ?? 100));
            scene.clipPlane4 = new BABYLON.Plane(0, 0, -1, clippingData.zNeg ?? -100);
        }

        // Load model and fit camera
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

        // ✅ Prevent page scroll when mouse is over canvas
        canvas.addEventListener('wheel', (event) => {
            if (document.activeElement === canvas || canvas.matches(':hover')) {
                event.preventDefault();
            }
        }, { passive: false });

        engine.runRenderLoop(() => scene.render());
        window.addEventListener('resize', () => engine.resize());
    </script>



</x-filament-panels::page>