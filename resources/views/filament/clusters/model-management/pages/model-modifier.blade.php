<x-filament-panels::page>
    <div class="space-y-4">
        <!-- Dropdown to select 3D model -->
        <div>
            <label for="model-select" class="font-semibold">Choose a 3D Model:</label>
            <x-filament::input.wrapper>
                <x-filament::input.select wire:model="model-select" onchange="changeModel(this)">
                    <option value="">Select 3D Model</option>
                    @foreach ($models as $model)
                        <option value="{{ asset('storage/' . $model->model_path) }}"
                            data-id="{{ $model->product_3d_model_id }}" data-clipping='@json($model->clipping_planes_data)'>
                            {{ $model->product->name ?? 'Product ' . $model->product_id }}
                        </option>
                    @endforeach
                </x-filament::input.select>
            </x-filament::input.wrapper>


        </div>

        <!-- Babylon.js Container -->
        <div id="babylon-container"
            style="width:100%; height:600px; background:#f5f5f5; margin-top: 10px; margin-bottom: 10px;"></div>
        <!-- Clipping Plane Controls -->
        <div class="mt-4">
            <h3 class="font-semibold mb-2">Adjust Clipping Planes (4 sides)</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>

                    <label>Plane X+</label>
                    <input type="range" id="clipPlaneXPos" min="-10" max="10" step="0.01" value="10"
                        oninput="updateClipping()">
                </div>
                <div>
                    <label>Plane X-</label>
                    <input type="range" id="clipPlaneXNeg" min="-10" max="10" step="0.01" value="-10"
                        oninput="updateClipping()">
                </div>
                <div>
                    <label>Plane Z+</label>
                    <input type="range" id="clipPlaneZPos" min="-10" max="10" step="0.01" value="10"
                        oninput="updateClipping()">
                </div>
                <div>
                    <label>Plane Z-</label>
                    <input type="range" id="clipPlaneZNeg" min="-10" max="10" step="0.01" value="-10"
                        oninput="updateClipping()">
                </div>
            </div>
            <x-filament::button onclick="saveClipping()">
                Save Clipping Settings
            </x-filament::button>
        </div>
    </div>

    <!-- Babylon.js Scripts -->
    <script src="https://cdn.babylonjs.com/babylon.js"></script>
    <script src="https://cdn.babylonjs.com/loaders/babylonjs.loaders.min.js"></script>

    <script>
        let canvas, engine, scene, camera;
        let selectedMeshes = [];
        let currentModelId = null;

        function initBabylon() {
            canvas = document.createElement('canvas');
            canvas.style.width = '100%';
            canvas.style.height = '100%';
            document.getElementById('babylon-container').appendChild(canvas);

            engine = new BABYLON.Engine(canvas, true);
            scene = new BABYLON.Scene(engine);
            scene.clearColor = new BABYLON.Color3(1, 1, 1);

            camera = new BABYLON.ArcRotateCamera("camera", Math.PI / 2, Math.PI / 4, 5, BABYLON.Vector3.Zero(), scene);
            camera.attachControl(canvas, true);

            // ✅ Smooth scrolling
            camera.wheelPrecision = 50;
            camera.panningSensibility = 50;

            const light = new BABYLON.HemisphericLight("light", new BABYLON.Vector3(1, 1, 0), scene);

            engine.runRenderLoop(() => scene.render());
            window.addEventListener('resize', () => engine.resize());

            // Default clipping planes
            scene.clipPlane = new BABYLON.Plane(1, 0, 0, -10);
            scene.clipPlane2 = new BABYLON.Plane(-1, 0, 0, -10);
            scene.clipPlane3 = new BABYLON.Plane(0, 0, 1, -10);
            scene.clipPlane4 = new BABYLON.Plane(0, 0, -1, -10);
        }

        function updateClipping() {
            const xPos = parseFloat(document.getElementById('clipPlaneXPos').value);
            const xNeg = parseFloat(document.getElementById('clipPlaneXNeg').value);
            const zPos = parseFloat(document.getElementById('clipPlaneZPos').value);
            const zNeg = parseFloat(document.getElementById('clipPlaneZNeg').value);

            scene.clipPlane = new BABYLON.Plane(1, 0, 0, -xPos);
            scene.clipPlane2 = new BABYLON.Plane(-1, 0, 0, xNeg);
            scene.clipPlane3 = new BABYLON.Plane(0, 0, 1, -zPos);
            scene.clipPlane4 = new BABYLON.Plane(0, 0, -1, zNeg);
        }

        function changeModel(select) {
            const modelUrl = select.value;
            currentModelId = select.options[select.selectedIndex].getAttribute('data-id');
            const clippingData = JSON.parse(select.options[select.selectedIndex].getAttribute('data-clipping') || '{}');

            if (!modelUrl) return;

            // Remove previous meshes
            scene.meshes.forEach(mesh => mesh.dispose());

            BABYLON.SceneLoader.Append("", modelUrl, scene, function () {
                selectedMeshes = scene.meshes.filter(m => m.name !== "__root__");

                if (selectedMeshes.length > 0) {
                    const boundingInfo = selectedMeshes[0].getBoundingInfo().boundingBox;
                    const center = boundingInfo.centerWorld;
                    const size = boundingInfo.extendSizeWorld.length();

                    // ✅ Camera closer to model
                    camera.target = center;
                    camera.radius = size * 1.5;
                    camera.lowerRadiusLimit = size * 0.8;
                    camera.upperRadiusLimit = size * 5;

                    // ✅ Restore dynamic slider range adjustment
                    const maxRange = Math.ceil(Math.max(boundingInfo.maximum.x - boundingInfo.minimum.x,
                        boundingInfo.maximum.z - boundingInfo.minimum.z)) * 2;

                    document.getElementById('clipPlaneXPos').max = maxRange;
                    document.getElementById('clipPlaneXNeg').min = -maxRange;
                    document.getElementById('clipPlaneZPos').max = maxRange;
                    document.getElementById('clipPlaneZNeg').min = -maxRange;

                    document.querySelectorAll('input[type="range"]').forEach(input => {
                        input.step = 0.01;
                    });
                }

                // Apply saved clipping data if exists
                if (clippingData) {
                    document.getElementById('clipPlaneXPos').value = clippingData.xPos ?? 10;
                    document.getElementById('clipPlaneXNeg').value = clippingData.xNeg ?? -10;
                    document.getElementById('clipPlaneZPos').value = clippingData.zPos ?? 10;
                    document.getElementById('clipPlaneZNeg').value = clippingData.zNeg ?? -10;
                    updateClipping();
                }
            });
        }

        function saveClipping() {
            const xPos = parseFloat(document.getElementById('clipPlaneXPos').value);
            const xNeg = parseFloat(document.getElementById('clipPlaneXNeg').value);
            const zPos = parseFloat(document.getElementById('clipPlaneZPos').value);
            const zNeg = parseFloat(document.getElementById('clipPlaneZNeg').value);

            fetch(`/save-clipping/${currentModelId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ xPos, xNeg, zPos, zNeg })
            })
                .then(response => response.json())
                .then(data => {
                    alert('Clipping settings saved!');
                })
                .catch(err => console.error(err));
        }

        document.addEventListener('DOMContentLoaded', initBabylon);
    </script>
</x-filament-panels::page>