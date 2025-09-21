<script type="module" src="https://unpkg.com/@google/model-viewer/dist/model-viewer.min.js"></script>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Shop Overview</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-white min-h-screen flex flex-col">
    <main class="flex-grow">
        <x-Navbar />
        <x-ShopDetailsCardComponent :shop="$shop" />
        <x-ProductListComponent :products="$products" />
        <x-ChatWindow />
    </main>
</body>

</html>