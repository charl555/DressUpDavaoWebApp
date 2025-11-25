<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 px-4 sm:px-6 lg:px-8 pt-[150px] pb-12 max-w-7xl mx-auto">
    @php
        $rawImageRecords = $product->product_images;
        $galleryImages = [];
        $thumbnail = null;

        foreach ($rawImageRecords as $record) {
            if (empty($thumbnail) && !empty($record->thumbnail_image)) {
                $thumbnail = $record->thumbnail_image;
            }

            if (!empty($record->images)) {
                $decodedImages = is_array($record->images)
                    ? $record->images
                    : json_decode($record->images, true);

                if (is_array($decodedImages)) {
                    foreach ($decodedImages as $img) {
                        if ($img !== $thumbnail) {
                            $galleryImages[] = (object) ['image' => $img];
                        }
                    }
                }
            }
        }

        $model3D = $product->product_3d_models()->first();
        $modelPath = null;
        if ($model3D && $model3D->model_path) {
            // If path already starts with 'uploads/', use as is
            if (str_starts_with($model3D->model_path, 'uploads/')) {
                $modelPath = asset($model3D->model_path);
            }
            // If path starts with '3d-models/', add 'uploads/' prefix
            else if (str_starts_with($model3D->model_path, '3d-models/')) {
                $modelPath = asset('uploads/' . $model3D->model_path);
            }
            // For any other format, try to construct the path
            else {
                $modelPath = asset('uploads/3d-models/' . $model3D->model_path);
            }
        }
        $clippingData = $model3D ? $model3D->clipping_planes_data : null;
        $imagesCount = count($galleryImages);

        if ($modelPath && $thumbnail) {
            $galleryImages[] = (object) ['image' => $thumbnail];
            $thumbnail = null;
        }

        $imagesCount = count($galleryImages);
    @endphp

    @if ($thumbnail || $imagesCount > 0 || $modelPath)
        <div class="flex flex-col">
            {{-- Main Image/Model --}}
            <div
                class="bg-gray-50 rounded-xl overflow-hidden shadow-lg aspect-video w-full h-[600px] mb-6 relative border border-gray-200">
                @if ($modelPath)
                    <canvas id="renderCanvas" class="w-full h-full"></canvas>
                @elseif ($thumbnail)
                    <img src="{{ asset('uploads/' . $thumbnail) }}" alt="{{ $product->name }}"
                        class="w-full h-full object-cover cursor-pointer transition-transform duration-300 hover:scale-105"
                        onclick="openImageModal('{{ asset('uploads/' . $thumbnail) }}')" />
                @else
                    <div class="flex items-center justify-center h-full w-full text-gray-500">
                        <div class="text-center">
                            <svg class="w-16 h-16 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <p class="text-gray-600 font-medium">No Preview Available</p>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Gallery Thumbnails --}}
            @if ($imagesCount > 0)
                <div class="grid grid-cols-4 gap-3">
                    @foreach ($galleryImages as $img)
                        @php $imgPath = $img->image ?? ($img['image'] ?? null); @endphp
                        @if ($imgPath)
                            <div
                                class="bg-gray-100 h-24 sm:h-28 rounded-lg overflow-hidden cursor-pointer border-2 border-transparent hover:border-purple-500 transition-all duration-200 group">
                                <img src="{{ asset('uploads/' . $imgPath) }}" alt="Product Image"
                                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-200"
                                    onclick="openImageModal('{{ asset('uploads/' . $imgPath) }}')" />
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
    @endif

    {{-- Product Details --}}
    <div class="flex flex-col pt-8 lg:pt-0">
        {{-- Product Header --}}
        <div class="pb-6 border-b border-gray-200 mb-8">
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-900 leading-tight mb-4 font-serif">
                {{ $product->name }}
            </h1>
            <div class="flex items-center space-x-4">
                @if ($product->status === 'Available')
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Available
                    </span>
                @else
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Unavailable
                    </span>
                @endif
            </div>
        </div>
        {{-- Product Information --}}
        <div class="space-y-6 mb-8">
            <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Product Details
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-700">
                    <p><span class="font-semibold text-gray-900">Description:</span>
                        {{ $product->description ?? 'No description available.' }}</p>
                    <p><span class="font-semibold text-gray-900">Inclusions:</span> {{ $product->inclusions ?? 'N/A' }}
                    </p>
                    <p><span class="font-semibold text-gray-900">Type:</span> {{ $product->type }}</p>
                    <p><span class="font-semibold text-gray-900">Style:</span> {{ $product->subtype ?? 'N/A' }}</p>
                    <p><span class="font-semibold text-gray-900">Size:</span> {{ $product->size }}</p>
                    <p><span class="font-semibold text-gray-900">Colors:</span> {{ $product->colors }}</p>
                    <p><span class="font-semibold text-gray-900">Fabric:</span> {{ $product->fabric ?? 'N/A' }}</p>
                    <p><span class="font-semibold text-gray-900">Events:</span>
                        {{ $product->events->pluck('event_name')->implode(', ') ?? 'N/A' }}</p>
                </div>
            </div>

            {{-- Measurements Section --}}
            @if ($product->type === 'Gown')
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Gown Measurements
                    </h3>
                    <div class="grid grid-cols-2 gap-4 text-gray-700">
                        @php
                            $gownMeasurements = [
                                'Length' => $product->product_measurements->gown_length ?? null,
                                'Upper Chest' => $product->product_measurements->gown_upper_chest ?? null,
                                'Chest' => $product->product_measurements->gown_chest ?? null,
                                'Waist' => $product->product_measurements->gown_waist ?? null,
                                'Hips' => $product->product_measurements->gown_hips ?? null,
                                'Shoulder' => $product->product_measurements->gown_shoulder ?? null,
                                'Bust' => $product->product_measurements->gown_bust ?? null
                            ];

                            // Filter out null, empty, or 'N/A' values
                            $validGownMeasurements = array_filter($gownMeasurements, function ($value) {
                                return !is_null($value) && $value !== '' && $value !== 'N/A' && $value !== 'n/a';
                            });
                        @endphp

                        @if(count($validGownMeasurements) > 0)
                            @foreach($validGownMeasurements as $label => $value)
                                <p><span class="font-medium">{{ $label }}:</span> {{ $value }} in</p>
                            @endforeach
                        @else
                            <p class="text-gray-500 col-span-2">No gown measurements available.</p>
                        @endif
                    </div>
                </div>
            @elseif ($product->type === 'Suit')
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Suit Measurements
                    </h3>
                    <div class="grid grid-cols-2 gap-4 text-gray-700">
                        @php
                            $suitMeasurements = [
                                'Jacket Chest' => $product->product_measurements->jacket_chest ?? null,
                                'Jacket Length' => $product->product_measurements->jacket_length ?? null,
                                'Shoulder' => $product->product_measurements->jacket_shoulder ?? null,
                                'Sleeve Length' => $product->product_measurements->jacket_sleeve_length ?? null,
                                'Sleeve Width' => $product->product_measurements->jacket_sleeve_width ?? null,
                                'Bicep' => $product->product_measurements->jacket_bicep ?? null,
                                'Arm Hole' => $product->product_measurements->jacket_arm_hole ?? null,
                                'Jacket Waist' => $product->product_measurements->jacket_waist ?? null,
                                'Jacket Hip' => $product->product_measurements->jacket_hip ?? null,
                                'Trouser Waist' => $product->product_measurements->trouser_waist ?? null,
                                'Trouser Hip' => $product->product_measurements->trouser_hip ?? null,
                                'Inseam' => $product->product_measurements->trouser_inseam ?? null,
                                'Outseam' => $product->product_measurements->trouser_outseam ?? null,
                                'Thigh' => $product->product_measurements->trouser_thigh ?? null,
                                'Leg Opening' => $product->product_measurements->trouser_leg_opening ?? null,
                                'Crotch' => $product->product_measurements->trouser_crotch ?? null
                            ];

                            // Filter out null, empty, or 'N/A' values
                            $validSuitMeasurements = array_filter($suitMeasurements, function ($value) {
                                return !is_null($value) && $value !== '' && $value !== 'N/A' && $value !== 'n/a';
                            });
                        @endphp

                        @if(count($validSuitMeasurements) > 0)
                            @foreach($validSuitMeasurements as $label => $value)
                                <p><span class="font-medium">{{ $label }}:</span> {{ $value }} in</p>
                            @endforeach
                        @else
                            <p class="text-gray-500 col-span-2">No suit measurements available.</p>
                        @endif
                    </div>
                </div>
            @endif

              {{-- Recommendation Section --}}
@auth
    @if($product->fit_score > 0)
        <div class="mb-6">
            @include('partials.recommendation-card', [
                'recommendation' => $product->recommendation,
                'fitScore' => $product->fit_score
            ])
        </div>
    @endif
@endauth

            {{-- Favorites Section --}}
            @auth
                @if(auth()->user()->role !== 'Admin' && auth()->user()->role !== 'SuperAdmin')
                    <div class="flex items-center space-x-3 mb-6">
                        @if(auth()->user()->hasFavorited($product))
                            <form action="{{ route('products.unfavorite', $product) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="flex items-center space-x-2 text-purple-600 hover:text-purple-700 transition-colors duration-200 p-2 rounded-lg hover:bg-purple-50"
                                    title="Remove from favorites">
                                    <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24">
                                        <path
                                            d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                                    </svg>
                                </button>
                            </form>
                        @else
                            <form action="{{ route('products.favorite', $product) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit"
                                    class="flex items-center space-x-2 text-gray-400 hover:text-purple-600 transition-colors duration-200 p-2 rounded-lg hover:bg-purple-50"
                                    title="Add to favorites">
                                    <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24">
                                        <path
                                            d="M16.5 3c-1.74 0-3.41.81-4.5 2.09C10.91 3.81 9.24 3 7.5 3 4.42 3 2 5.42 2 8.5c0 3.78 3.4 6.86 8.55 11.54L12 21.35l1.45-1.32C18.6 15.36 22 12.28 22 8.5 22 5.42 19.58 3 16.5 3zm-4.4 15.55l-.1.1-.1-.1C7.14 14.24 4 11.39 4 8.5 4 6.5 5.5 5 7.5 5c1.54 0 3.04.99 3.57 2.36h1.87C13.46 5.99 14.96 5 16.5 5c2 0 3.5 1.5 3.5 3.5 0 2.89-3.14 5.74-7.9 10.05z" />
                                    </svg>
                                </button>
                            </form>
                        @endif
                        <span class="text-sm text-gray-600 font-medium">{{ $product->favorites_count }} favorites</span>
                    </div>
                @endif
            @else
                <div class="flex items-center space-x-3 mb-6 text-gray-400" title="Login to add to favorites">
                    <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24">
                        <path
                            d="M16.5 3c-1.74 0-3.41.81-4.5 2.09C10.91 3.81 9.24 3 7.5 3 4.42 3 2 5.42 2 8.5c0 3.78 3.4 6.86 8.55 11.54L12 21.35l1.45-1.32C18.6 15.36 22 12.28 22 8.5 22 5.42 19.58 3 16.5 3zm-4.4 15.55l-.1.1-.1-.1C7.14 14.24 4 11.39 4 8.5 4 6.5 5.5 5 7.5 5c1.54 0 3.04.99 3.57 2.36h1.87C13.46 5.99 14.96 5 16.5 5c2 0 3.5 1.5 3.5 3.5 0 2.89-3.14 5.74-7.9 10.05z" />
                    </svg>
                    <span class="text-sm font-medium">Login to Favorite</span>
                </div>
            @endauth

            {{-- Availability Status --}}
            <div class="mb-8">
                @if ($product->status === 'Rented')
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                            <p class="text-yellow-800 font-medium">
                                This product is currently rented and will be returned on
                                <strong>{{ $product->rentals->first()->return_date->format('F j, Y') ?? 'N/A' }}</strong>.
                            </p>
                        </div>
                    </div>
                @elseif ($product->status === 'Reserved')
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <p class="text-blue-800 font-medium">This product is currently reserved on
                                <strong>{{ $product->bookings->first()->booking_date->format('F j, Y') ?? 'N/A' }}</strong>.
                            </p>
                        </div>
                    </div>
                @elseif ($product->status !== 'Available')
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                            <p class="text-red-800 font-medium">This product is currently unavailable.</p>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Availability Calendar --}}
            <div class="bg-gray-50 rounded-lg p-6 mb-8">
                <div class="flex items-center justify-between cursor-pointer" onclick="toggleCalendar()">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Availability Calendar
                    </h3>
                    <svg id="calendarIcon" class="w-5 h-5 text-gray-500 transition-transform duration-300" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>

                <div id="calendarContent" class="mt-4 space-y-4 hidden">
                    @php
                        // Get all unavailable dates with status consideration
                        $unavailableDates = [];

                        // Get rental periods (pickup_date to return_date) - only for active rentals
                        foreach ($product->rentals as $rental) {
                            // Only mark dates as unavailable if rental status is active
                            $activeRentalStatuses = ['Rented', 'Picked Up', 'Overdue'];
                            if (in_array($rental->status, $activeRentalStatuses)) {
                                $start = \Carbon\Carbon::parse($rental->pickup_date);
                                $end = \Carbon\Carbon::parse($rental->return_date);

                                for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                                    $unavailableDates[$date->format('Y-m-d')] = 'rented';
                                }
                            }
                            // If rental status is 'Returned', don't mark any dates as unavailable
                        }

                        // Get booking dates (booking_date only) - ONLY mark bookings as reserved if status is 'On Going'
                        foreach ($product->bookings as $booking) {

                            if ($booking->status === 'On Going') {
                                $bookingDate = \Carbon\Carbon::parse($booking->booking_date);
                                $unavailableDates[$bookingDate->format('Y-m-d')] = 'reserved';
                            }

                        }
                    @endphp

                    {{-- Month Navigation --}}
                    <div class="flex items-center justify-between bg-white rounded-lg border border-gray-200 p-4">
                        <button onclick="changeMonth(-1)"
                            class="flex items-center space-x-2 text-purple-600 hover:text-purple-700 font-medium transition-colors duration-200 p-2 rounded-lg hover:bg-purple-50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                            <span>Previous</span>
                        </button>

                        <h4 id="currentMonth" class="text-lg font-semibold text-gray-900">
                            {{ \Carbon\Carbon::today()->format('F Y') }}
                        </h4>

                        <button onclick="changeMonth(1)"
                            class="flex items-center space-x-2 text-purple-600 hover:text-purple-700 font-medium transition-colors duration-200 p-2 rounded-lg hover:bg-purple-50">
                            <span>Next</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>

                    {{-- Calendar Grid --}}
                    <div class="bg-white rounded-lg border border-gray-200 p-4">
                        <div class="grid grid-cols-7 gap-1 mb-2">
                            @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
                                <div class="text-center text-sm font-medium text-gray-500 py-2">{{ $day }}</div>
                            @endforeach
                        </div>

                        <div id="calendarGrid" class="grid grid-cols-7 gap-1">
                            {{-- Calendar days will be populated by JavaScript --}}
                        </div>
                    </div>

                    {{-- Legend --}}
                    <div class="flex flex-wrap items-center justify-center gap-4 pt-4 border-t border-gray-200">
                        <div class="flex items-center space-x-2">
                            <span class="inline-block w-3 h-3 bg-green-500 rounded-full"></span>
                            <span class="text-xs text-gray-600">Available</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="inline-block w-3 h-3 bg-yellow-500 rounded-full"></span>
                            <span class="text-xs text-gray-600">Reserved</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="inline-block w-3 h-3 bg-red-500 rounded-full"></span>
                            <span class="text-xs text-gray-600">Rented</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-3 h-3 border-2 border-purple-500 rounded"></div>
                            <span class="text-xs text-gray-600">Today</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-xs text-gray-400 italic">Gray = Other month</span>
                        </div>
                    </div>

                    {{-- <p class="text-sm text-gray-600 text-center">
                        Use the navigation buttons to view availability for different months.
                        Green dates are available, yellow dates are reserved, and red dates are rented.
                    </p> --}}
                </div>
            </div>

            {{-- Inquire Button --}}
            @if(auth()->guest() || (auth()->check() && auth()->user()->role !== 'Admin' && auth()->user()->role !== 'SuperAdmin'))
                <div class="flex justify-center md:justify-start mb-8">
                    <button id="inquireButton"
                        class="text-lg px-8 py-4 w-full md:w-auto rounded-lg shadow-md transition-all duration-300 ease-in-out font-semibold flex items-center justify-center
                                                                                    bg-gradient-to-r from-purple-600 to-indigo-600 text-white hover:from-purple-700 hover:to-indigo-700 hover:shadow-lg"
                        onclick="openInquiryModal()">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        Inquire Now
                    </button>
                </div>
            @endif

            {{-- Shop Information --}}
            <div class="border-t border-gray-200 pt-6">
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        Shop Information
                    </h3>
                    <p class="text-gray-700 mb-2"><span class="font-semibold">Sold by:</span>
                        {{ $product->user->shop->shop_name }}</p>
                    <p class="text-gray-600 mb-4">{{ $product->user->shop->shop_address ?? 'No address provided' }}</p>
                    <a href="{{ route('shop.overview', $product->user->shop) }}"
                        class="inline-flex items-center text-purple-600 hover:text-purple-700 font-medium transition-colors duration-200">
                        View Shop Profile
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Keep the existing 3D model and image modal scripts --}}
    @if ($modelPath)
        <script src="https://cdn.babylonjs.com/babylon.js"></script>
        <script src="https://cdn.babylonjs.com/loaders/babylonjs.loaders.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const canvas = document.getElementById('renderCanvas');
                const engine = new BABYLON.Engine(canvas, true);
                const scene = new BABYLON.Scene(engine);
                scene.clearColor = new BABYLON.Color3(1, 1, 1);

                const camera = new BABYLON.ArcRotateCamera("camera",
                    Math.PI / 2, Math.PI / 3, 3, BABYLON.Vector3.Zero(), scene);
                camera.attachControl(canvas, true);

                // Smooth controls for desktop and mobile
                camera.wheelPrecision = 25; // Faster zoom
                camera.panningSensibility = 5000; // Slower panning
                camera.angularSensibilityX = 8000; // Slower rotation
                camera.angularSensibilityY = 8000; // Slower rotation

                // Pinch controls for mobile
                camera.pinchPrecision = 80; // Faster pinch zoom
                camera.pinchDeltaPercentage = 0.002;

                // Inertia for smooth stops
                camera.inertia = 0.9;
                camera.panningInertia = 0.9;


                camera.minZ = 0.001;
                camera.maxZ = 1000;

                const light = new BABYLON.HemisphericLight("light",
                    new BABYLON.Vector3(1, 1, 0), scene);

                camera.useAutoRotationBehavior = true;
                const autoRotate = camera.autoRotationBehavior;
                if (autoRotate) {
                    autoRotate.idleRotationSpeed = 0.1;
                    autoRotate.idleRotationWaitTime = 3000;
                    autoRotate.idleRotationSpinUpTime = 2000;
                    autoRotate.zoomStopsAnimation = false;
                }

                canvas.addEventListener('wheel', (event) => {
                    event.preventDefault();
                }, { passive: false });

                BABYLON.SceneLoader.Append("", "{{ $modelPath }}", scene, function () {
                    const meshes = scene.meshes.filter(m => m.name !== "__root__");
                    if (meshes.length > 0) {
                        const boundingInfo = meshes[0].getBoundingInfo().boundingBox;
                        const center = boundingInfo.centerWorld;
                        const radius = boundingInfo.extendSizeWorld.length();

                        camera.target = center;
                        camera.radius = radius * 2;
                        camera.lowerRadiusLimit = radius * 0.001; // Very close zoom
                        camera.upperRadiusLimit = radius * 10;
                    }
                });

                engine.runRenderLoop(() => scene.render());
                window.addEventListener('resize', () => engine.resize());
            });
        </script>
    @endif

    <div id="imageModal" class="fixed inset-0 bg-black/70 hidden items-center justify-center z-50">
        <div class="relative max-w-4xl w-full mx-4">
            <button onclick="closeImageModal()"
                class="absolute top-4 right-4 bg-white rounded-full p-2 shadow-lg hover:bg-gray-100 transition-colors duration-200 z-10">
                <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <img id="modalImage" src="" class="w-full max-h-[80vh] object-contain rounded-lg shadow-2xl" />
        </div>
    </div>

    <script>
        function openImageModal(src) {
            document.getElementById('modalImage').src = src;
            document.getElementById('imageModal').classList.remove('hidden');
            document.getElementById('imageModal').classList.add('flex');
            document.body.style.overflow = 'hidden';
        }
        function closeImageModal() {
            document.getElementById('imageModal').classList.add('hidden');
            document.getElementById('imageModal').classList.remove('flex');
            document.body.style.overflow = '';
        }
        // Close modal when clicking outside image
        document.getElementById('imageModal').addEventListener('click', function (e) {
            if (e.target.id === 'imageModal') {
                closeImageModal();
            }
        });

        let currentDate = new Date();
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        const unavailableDates = @json($unavailableDates);

        function toggleCalendar() {
            const content = document.getElementById('calendarContent');
            const icon = document.getElementById('calendarIcon');

            if (content.classList.contains('hidden')) {
                content.classList.remove('hidden');
                icon.classList.remove('rotate-0');
                icon.classList.add('rotate-180');
                renderCalendar();
            } else {
                content.classList.add('hidden');
                icon.classList.remove('rotate-180');
                icon.classList.add('rotate-0');
            }
        }

        function changeMonth(direction) {
            const newDate = new Date(currentDate);
            newDate.setMonth(newDate.getMonth() + direction);

            // Only allow navigation to current or future months
            const minDate = new Date(today.getFullYear(), today.getMonth(), 1);
            if (newDate >= minDate) {
                currentDate = newDate;
                renderCalendar();
            }
        }

        function renderCalendar() {
            const year = currentDate.getFullYear();
            const month = currentDate.getMonth();

            // Update month header
            document.getElementById('currentMonth').textContent =
                new Date(year, month).toLocaleDateString('en-US', { month: 'long', year: 'numeric' });

            // Update button states
            const prevButton = document.querySelector('button[onclick="changeMonth(-1)"]');
            const isCurrentMonth = year === today.getFullYear() && month === today.getMonth();

            if (prevButton) {
                if (isCurrentMonth) {
                    prevButton.disabled = true;
                    prevButton.classList.add('opacity-50', 'cursor-not-allowed');
                    prevButton.classList.remove('hover:bg-purple-50', 'hover:text-purple-700');
                } else {
                    prevButton.disabled = false;
                    prevButton.classList.remove('opacity-50', 'cursor-not-allowed');
                    prevButton.classList.add('hover:bg-purple-50', 'hover:text-purple-700');
                }
            }

            const calendarGrid = document.getElementById('calendarGrid');
            calendarGrid.innerHTML = '';

            const firstDay = new Date(year, month, 1);
            const lastDay = new Date(year, month + 1, 0);
            const startDate = new Date(firstDay);
            startDate.setDate(startDate.getDate() - firstDay.getDay());

            const endDate = new Date(lastDay);
            endDate.setDate(endDate.getDate() + (6 - lastDay.getDay()));

            for (let date = new Date(startDate); date <= endDate; date.setDate(date.getDate() + 1)) {
                // Create date string in YYYY-MM-DD format for consistent comparison
                const dateString = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`;

                const isCurrentMonth = date.getMonth() === month;
                const isToday = date.toDateString() === today.toDateString();
                const isUnavailable = unavailableDates[dateString];
                const statusType = unavailableDates[dateString];

                // Create a clean date without time for comparison
                const cleanDate = new Date(date);
                cleanDate.setHours(0, 0, 0, 0);
                const isPast = cleanDate < today && !isToday;

                const dayElement = document.createElement('div');
                dayElement.className = `text-center p-2 border border-gray-100 rounded-lg
            ${!isCurrentMonth ? 'bg-gray-50 text-gray-400' : 'bg-white'}
            ${isPast ? 'opacity-60' : ''}
            ${isToday ? 'ring-2 ring-purple-500 ring-inset' : ''}
            ${isUnavailable ? (statusType === 'rented' ? 'bg-red-100 text-red-800 border-red-200' : 'bg-yellow-100 text-yellow-800 border-yellow-200') : ''}`;

                dayElement.innerHTML = `
            <div class="text-sm font-medium">${date.getDate()}</div>
            ${isUnavailable ? `
                <div class="text-xs mt-1">
                    <span class="inline-block w-2 h-2 ${statusType === 'rented' ? 'bg-red-500' : 'bg-yellow-500'} rounded-full"></span>
                </div>
            ` : (isCurrentMonth && !isPast ? `
                <div class="text-xs mt-1 text-green-600">
                    <span class="inline-block w-2 h-2 bg-green-500 rounded-full"></span>
                </div>
            ` : '')}
        `;

                calendarGrid.appendChild(dayElement);
            }
        }

        // Initialize calendar when page loads
        document.addEventListener('DOMContentLoaded', function () {
            renderCalendar();
        });
    </script>