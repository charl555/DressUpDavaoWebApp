<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Forum</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-white h-full">
    <main>
        <x-navbar />
        <div class="py-40 bg-gradient-to-b from-white to-gray-50">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Title Section -->
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold text-gray-900 mb-6" style="font-family: 'Playfair Display', serif;">
                        Community Forum
                    </h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                        Join the DressUp Davao Community Forum where users and shop owners connect, share, and grow
                        together.
                    </p>
                </div>

                <!-- Forum Features -->
                <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3 mb-12">
                    <!-- Feature 1 -->
                    <div class="text-center group">
                        <div
                            class="bg-gradient-to-br from-purple-100 to-indigo-100 w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:from-purple-200 group-hover:to-indigo-200 transition-all duration-300">
                            <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Ask Questions</h3>
                        <p class="text-gray-600 text-sm">Get help from the community and experts</p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="text-center group">
                        <div
                            class="bg-gradient-to-br from-purple-100 to-indigo-100 w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:from-purple-200 group-hover:to-indigo-200 transition-all duration-300">
                            <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a2 2 0 01-2-2v-1m8-8V5a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l4-4z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Share Experiences</h3>
                        <p class="text-gray-600 text-sm">Discuss rental experiences and tips</p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="text-center group">
                        <div
                            class="bg-gradient-to-br from-purple-100 to-indigo-100 w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:from-purple-200 group-hover:to-indigo-200 transition-all duration-300">
                            <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Request Features</h3>
                        <p class="text-gray-600 text-sm">Suggest improvements and new features</p>
                    </div>
                </div>

                <!-- Forum Categories -->
                <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6 text-center"
                        style="font-family: 'Playfair Display', serif;">
                        Forum Categories
                    </h3>
                    <div class="grid gap-6 md:grid-cols-2">
                        <!-- Left Column -->
                        <div class="space-y-4">
                            <div
                                class="flex items-center p-4 bg-purple-50 rounded-lg border border-purple-100 group hover:bg-purple-100 transition-all duration-300">
                                <div
                                    class="bg-white w-10 h-10 rounded-lg flex items-center justify-center mr-4 shadow-sm">
                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a2 2 0 01-2-2v-1m8-8V5a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l4-4z" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900">General Discussion</h4>
                                    <p class="text-sm text-gray-600">Ask questions and share experiences</p>
                                </div>
                            </div>

                            <div
                                class="flex items-center p-4 bg-purple-50 rounded-lg border border-purple-100 group hover:bg-purple-100 transition-all duration-300">
                                <div
                                    class="bg-white w-10 h-10 rounded-lg flex items-center justify-center mr-4 shadow-sm">
                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900">Feature Requests</h4>
                                    <p class="text-sm text-gray-600">Suggest improvements to the platform</p>
                                </div>
                            </div>

                            <div
                                class="flex items-center p-4 bg-purple-50 rounded-lg border border-purple-100 group hover:bg-purple-100 transition-all duration-300">
                                <div
                                    class="bg-white w-10 h-10 rounded-lg flex items-center justify-center mr-4 shadow-sm">
                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900">Shop Owners</h4>
                                    <p class="text-sm text-gray-600">Connect with other fashion shops</p>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-4">
                            <div
                                class="flex items-center p-4 bg-purple-50 rounded-lg border border-purple-100 group hover:bg-purple-100 transition-all duration-300">
                                <div
                                    class="bg-white w-10 h-10 rounded-lg flex items-center justify-center mr-4 shadow-sm">
                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900">Event Styling</h4>
                                    <p class="text-sm text-gray-600">Discuss fittings and gown care</p>
                                </div>
                            </div>

                            <div
                                class="flex items-center p-4 bg-purple-50 rounded-lg border border-purple-100 group hover:bg-purple-100 transition-all duration-300">
                                <div
                                    class="bg-white w-10 h-10 rounded-lg flex items-center justify-center mr-4 shadow-sm">
                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900">Safe Space</h4>
                                    <p class="text-sm text-gray-600">Moderated by DressUp Davao team</p>
                                </div>
                            </div>

                            <div
                                class="flex items-center p-4 bg-purple-50 rounded-lg border border-purple-100 group hover:bg-purple-100 transition-all duration-300">
                                <div
                                    class="bg-white w-10 h-10 rounded-lg flex items-center justify-center mr-4 shadow-sm">
                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900">Community</h4>
                                    <p class="text-sm text-gray-600">Friendly space for all members</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CTA Section -->
                <div class="text-center mt-12">
                    <a href="/forum"
                        class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold rounded-lg shadow-lg hover:from-purple-700 hover:to-indigo-700 transform hover:scale-105 transition-all duration-300">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a2 2 0 01-2-2v-1m8-8V5a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l4-4z" />
                        </svg>
                        Join the Community Forum
                    </a>
                </div>
            </div>
        </div>

    </main>
    <x-footer />

</body>

</html>