<x-filament-panels::page>

    @php
        $shop = \App\Models\Shops::where('user_id', auth()->id())->first();
        $hasAccess = $shop?->allow_3d_model_access ?? false;
    @endphp

    @if(!$hasAccess)
        <div class="space-y-6">
            <x-filament::section>
                <x-slot name="heading">
                    Access Denied
                </x-slot>

                <x-slot name="description">
                    Your account currently does not have access to this page.
                </x-slot>


            </x-filament::section>
        </div>
    @else
        <div class="space-y-6">
            <!-- Header Section -->
            <x-filament::section>
                <x-slot name="heading">
                    3D Model Modifier
                </x-slot>

                <x-slot name="description">
                    Select a 3D model and adjust clipping planes to customize the view. Changes are automatically saved.
                </x-slot>

                <!-- Model Selection -->
                <div class="space-y-4">
                    <div>
                        <x-filament::input.wrapper>
                            <x-filament::input.select wire:model="model-select" onchange="changeModel(this)" class="w-full">
                                <option value="">Select a 3D Model to Modify</option>
                                @foreach ($models as $model)
                                    <option value="{{ asset('storage/' . $model->model_path) }}"
                                        data-id="{{ $model->product_3d_model_id }}"
                                        data-clipping='@json($model->clipping_planes_data)'>
                                        {{ $model->product->name ?? 'Product ' . $model->product_id }}
                                        ({{ $model->product->type ?? 'Unknown Type' }})
                                    </option>
                                @endforeach
                            </x-filament::input.select>
                        </x-filament::input.wrapper>
                    </div>

                    @if(count($models) == 0)
                        <div class="text-center py-8">
                            <div class="text-gray-500 dark:text-gray-400">

                                <p class="text-lg font-medium">No 3D Models Available</p>
                                <p class="text-sm">Create 3D models first or attach models to products to use the modifier.</p>
                            </div>
                        </div>
                    @endif
                </div>
            </x-filament::section>

            <!-- 3D Viewer Section -->
            <x-filament::section>
                <x-slot name="heading">
                    3D Model Viewer
                </x-slot>

                <x-slot name="description">
                    Interactive 3D model viewer with clipping plane controls
                </x-slot>

                <div id="babylon-container"
                    class="w-full h-96 bg-gray-100 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 mb-6">
                </div>
            </x-filament::section>

            <!-- Clipping Plane Controls -->
            <x-filament::section>
                <x-slot name="heading">
                    Clipping Plane Controls
                </x-slot>

                <x-slot name="description">
                    Adjust the clipping planes to customize how the 3D model is displayed. Changes are saved automatically.
                </x-slot>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-6">
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                <span class="inline-flex items-center">
                                    <span class="w-2 h-2 bg-blue-500 rounded-full mr-2"></span>
                                    Plane X+ (Right Side)
                                </span>
                            </label>
                            <input type="range" id="clipPlaneXPos" min="-10" max="10" step="0.01" value="10"
                                oninput="updateClipping()"
                                class="w-full h-3 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700 slider">
                            <div class="flex justify-between text-xs text-gray-500 mt-2">
                                <span>-10</span>
                                <span>0</span>
                                <span>10</span>
                            </div>
                        </div>

                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                <span class="inline-flex items-center">
                                    <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                                    Plane X- (Left Side)
                                </span>
                            </label>
                            <input type="range" id="clipPlaneXNeg" min="-10" max="10" step="0.01" value="-10"
                                oninput="updateClipping()"
                                class="w-full h-3 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700 slider">
                            <div class="flex justify-between text-xs text-gray-500 mt-2">
                                <span>-10</span>
                                <span>0</span>
                                <span>10</span>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                <span class="inline-flex items-center">
                                    <span class="w-2 h-2 bg-purple-500 rounded-full mr-2"></span>
                                    Plane Z+ (Front)
                                </span>
                            </label>
                            <input type="range" id="clipPlaneZPos" min="-10" max="10" step="0.01" value="10"
                                oninput="updateClipping()"
                                class="w-full h-3 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700 slider">
                            <div class="flex justify-between text-xs text-gray-500 mt-2">
                                <span>-10</span>
                                <span>0</span>
                                <span>10</span>
                            </div>
                        </div>

                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                <span class="inline-flex items-center">
                                    <span class="w-2 h-2 bg-orange-500 rounded-full mr-2"></span>
                                    Plane Z- (Back)
                                </span>
                            </label>
                            <input type="range" id="clipPlaneZNeg" min="-10" max="10" step="0.01" value="-10"
                                oninput="updateClipping()"
                                class="w-full h-3 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700 slider">
                            <div class="flex justify-between text-xs text-gray-500 mt-2">
                                <span>-10</span>
                                <span>0</span>
                                <span>10</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap gap-4 mt-8">
                    <x-filament::button onclick="saveClipping()" color="primary" size="lg">
                        Save Clipping Settings
                    </x-filament::button>
                    <x-filament::button onclick="resetClipping()" color="gray" size="lg">
                        Reset to Default
                    </x-filament::button>
                    {{-- <x-filament::button onclick="exportSettings()" color="info" size="lg">
                        Export Settings
                    </x-filament::button> --}}
                </div>
            </x-filament::section>
        </div>

        <style>
            .slider::-webkit-slider-thumb {
                appearance: none;
                height: 20px;
                width: 20px;
                border-radius: 50%;
                background: #6366f1;
                cursor: pointer;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            }

            .slider::-moz-range-thumb {
                height: 20px;
                width: 20px;
                border-radius: 50%;
                background: #6366f1;
                cursor: pointer;
                border: none;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            }

            .slider::-webkit-slider-track {
                height: 12px;
                border-radius: 6px;
                background: linear-gradient(to right, #ef4444, #f59e0b, #10b981);
            }

            .slider::-moz-range-track {
                height: 12px;
                border-radius: 6px;
                background: linear-gradient(to right, #ef4444, #f59e0b, #10b981);
            }
        </style>

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

                camera = new BABYLON.ArcRotateCamera(
                    "camera",
                    Math.PI / 2,
                    Math.PI / 4,
                    5,
                    BABYLON.Vector3.Zero(),
                    scene
                );
                camera.attachControl(canvas, true);

                // 🎯 Slower, smoother desktop controls
                camera.wheelPrecision = 100;         // Zoom sensitivity
                camera.panningSensibility = 2000;    // Panning speed
                camera.angularSensibilityX = 4000;   // Rotation X
                camera.angularSensibilityY = 4000;   // Rotation Y

                // 📱 Slower mobile gestures
                camera.pinchPrecision = 200;         // Pinch zoom slower
                camera.pinchDeltaPercentage = 0.002; // Fine-tuned pinch
                camera.useNaturalPinchZoom = true;   // Smooth mobile pinch

                const light = new BABYLON.HemisphericLight("light", new BABYLON.Vector3(1, 1, 0), scene);

                engine.runRenderLoop(() => scene.render());
                window.addEventListener('resize', () => engine.resize());

                // ✅ Prevent page scroll when mouse is over canvas
                canvas.addEventListener('wheel', (event) => {
                    if (document.activeElement === canvas || canvas.matches(':hover')) {
                        event.preventDefault();
                    }
                }, { passive: false });

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

                scene.meshes.forEach(mesh => mesh.dispose());

                BABYLON.SceneLoader.Append("", modelUrl, scene, function () {
                    selectedMeshes = scene.meshes.filter(m => m.name !== "__root__");

                    if (selectedMeshes.length > 0) {
                        const boundingInfo = selectedMeshes[0].getBoundingInfo().boundingBox;
                        const center = boundingInfo.centerWorld;
                        const size = boundingInfo.extendSizeWorld.length();

                        camera.target = center;
                        camera.radius = size * 1.5;
                        camera.lowerRadiusLimit = size * 0.8;
                        camera.upperRadiusLimit = size * 5;

                        const maxRange = Math.ceil(Math.max(
                            boundingInfo.maximum.x - boundingInfo.minimum.x,
                            boundingInfo.maximum.z - boundingInfo.minimum.z
                        )) * 2;

                        document.getElementById('clipPlaneXPos').max = maxRange;
                        document.getElementById('clipPlaneXNeg').min = -maxRange;
                        document.getElementById('clipPlaneZPos').max = maxRange;
                        document.getElementById('clipPlaneZNeg').min = -maxRange;

                        document.querySelectorAll('input[type="range"]').forEach(input => {
                            input.step = 0.01;
                        });
                    }

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
                    .then(data => alert('Clipping settings saved!'))
                    .catch(err => console.error(err));
            }

            function resetClipping() {
                document.getElementById('clipPlaneXPos').value = 10;
                document.getElementById('clipPlaneXNeg').value = -10;
                document.getElementById('clipPlaneZPos').value = 10;
                document.getElementById('clipPlaneZNeg').value = -10;
                updateClipping();
            }

            function exportSettings() {
                const settings = {
                    clipPlaneXPos: document.getElementById('clipPlaneXPos').value,
                    clipPlaneXNeg: document.getElementById('clipPlaneXNeg').value,
                    clipPlaneZPos: document.getElementById('clipPlaneZPos').value,
                    clipPlaneZNeg: document.getElementById('clipPlaneZNeg').value,
                    timestamp: new Date().toISOString()
                };

                const dataStr = JSON.stringify(settings, null, 2);
                const dataBlob = new Blob([dataStr], { type: 'application/json' });
                const url = URL.createObjectURL(dataBlob);
                const link = document.createElement('a');
                link.href = url;
                link.download = 'clipping-settings.json';
                link.click();
                URL.revokeObjectURL(url);
            }

            document.addEventListener('DOMContentLoaded', initBabylon);
        </script>

    @endif


</x-filament-panels::page>