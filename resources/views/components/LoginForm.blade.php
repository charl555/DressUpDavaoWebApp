<div class="min-h-screen flex items-center justify-center bg-white px-4 sm:px-6 lg:px-8">
    <div class="w-full max-w-md space-y-8">
        <div class="text-center">
            <img class="mx-auto h-18 w-auto" src="{{ asset('images/Dressupdavaologo.png') }}" alt="DressUp Davao" />
            <h2 class="mt-10 text-center text-2xl/9 font-bold tracking-tight text-gray-900">Sign in to your account</h2>
        </div>

        <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
            {{-- LOGIN FORM --}}
            <form class="space-y-6" action="{{ route('login') }}" method="POST">
                @csrf

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm/6 font-medium text-gray-900">Email address</label>
                    <div class="mt-2">
                        <input type="email" name="email" id="email" autocomplete="email" required class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 
                            outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 
                            focus:outline-2 focus:-outline-offset-2 focus:outline-violet-600 sm:text-sm/6" />
                    </div>
                </div>

                {{-- Password with toggle --}}
                <div x-data="{ show: false }">
                    <div class="flex items-center justify-between">
                        <label for="password" class="block text-sm/6 font-medium text-gray-900">Password</label>
                        <div class="text-sm">
                            <a href="#" class="font-semibold text-violet-600 hover:text-violet-500">Forgot password?</a>
                        </div>
                    </div>
                    <div class="mt-2 relative">
                        <input :type="show ? 'text' : 'password'" name="password" id="password"
                            autocomplete="current-password" required class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 
                            outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 
                            focus:outline-2 focus:-outline-offset-2 focus:outline-violet-600 sm:text-sm/6" />

                        <!-- Eye Icon -->
                        <button type="button" @click="show = !show"
                            class="absolute inset-y-0 right-3 flex items-center text-gray-500 hover:text-gray-700">
                            <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 
                                    12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 
                                    0 .639C20.577 16.49 16.64 19.5 
                                    12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                            <svg x-show="show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 
                                    1.934 12C3.226 16.338 7.244 19.5 
                                    12 19.5c1.865 0 3.611-.45 
                                    5.148-1.242M6.228 6.228A10.45 10.45 
                                    0 0 1 12 4.5c4.756 0 8.773 
                                    3.162 10.065 7.498a10.523 
                                    10.523 0 0 1-4.293 5.774M6.228 
                                    6.228 3 3m3.228 3.228L21 
                                    21" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Submit --}}
                <div>
                    <button type="submit" class="flex w-full justify-center rounded-md bg-violet-600 px-3 py-1.5 text-sm/6 
                        font-semibold text-white shadow-xs hover:bg-violet-500 
                        focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-violet-600">
                        Sign in
                    </button>
                </div>
            </form>

            <p class="mt-10 text-center text-sm/6 text-gray-500">
                Not a member?
                <a href="/register" class="font-semibold text-violet-600 hover:text-violet-500">Register</a>
            </p>
        </div>
    </div>
</div>

{{-- Alpine.js --}}
<script src="//unpkg.com/alpinejs" defer></script>