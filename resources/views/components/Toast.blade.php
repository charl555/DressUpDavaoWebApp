<div id="toast-container" x-data="toastData()" x-show="show"
    x-transition:enter="transform ease-out duration-300 transition" x-transition:enter-start="translate-y-5 opacity-0"
    x-transition:enter-end="translate-y-0 opacity-100" x-transition:leave="transform ease-in duration-200 transition"
    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-cloak
    class="fixed bottom-6 right-6 z-[9999]">
    <div class="flex items-center gap-3 rounded-lg px-4 py-3 shadow-lg text-sm font-medium" :class="{
            'bg-green-600 text-white': type === 'success',
            'bg-red-600 text-white': type === 'error',
            'bg-yellow-500 text-white': type === 'warning',
            'bg-blue-600 text-white': type === 'info'
        }">
        <!-- Icon based on type -->
        <div x-show="type === 'success'">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                    clip-rule="evenodd"></path>
            </svg>
        </div>
        <div x-show="type === 'error'">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                    clip-rule="evenodd"></path>
            </svg>
        </div>
        <div x-show="type === 'warning'">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                    clip-rule="evenodd"></path>
            </svg>
        </div>
        <div x-show="type === 'info'">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                    clip-rule="evenodd"></path>
            </svg>
        </div>
        <span x-text="message"></span>
        <!-- Close button -->
        <button @click="hide()" class="ml-2 opacity-70 hover:opacity-100 transition-opacity">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                    clip-rule="evenodd"></path>
            </svg>
        </button>
    </div>
</div>

<script>
    // Alpine.js component data
    function toastData() {
        return {
            show: false,
            message: '',
            type: 'success',
            timeout: null,

            showToast(message, type = 'success', duration = 4000) {
                clearTimeout(this.timeout);
                this.message = message;
                this.type = type;
                this.show = true;
                this.timeout = setTimeout(() => this.hide(), duration);
            },

            hide() {
                this.show = false;
                clearTimeout(this.timeout);
            }
        }
    }

    // Global function to show toast from anywhere
    window.showToast = function (message, type = 'success', duration = 4000) {
        // Wait for Alpine to be ready
        document.addEventListener('alpine:init', () => {
            const toastEl = document.getElementById('toast-container');
            if (toastEl && toastEl._x_dataStack && toastEl._x_dataStack[0]) {
                toastEl._x_dataStack[0].showToast(message, type, duration);
            }
        });

        // If Alpine is already initialized
        const toastEl = document.getElementById('toast-container');
        if (toastEl && toastEl._x_dataStack && toastEl._x_dataStack[0]) {
            toastEl._x_dataStack[0].showToast(message, type, duration);
        }
    };

    // Test function for debugging
    window.testToast = function () {
        showToast('Test message - Toast is working!', 'success');
    };
</script>

@if (session('success'))
    <script>
        document.addEventListener('livewire:navigated', () => showToast(@json(session('success')), 'success'));
        document.addEventListener('DOMContentLoaded', () => showToast(@json(session('success')), 'success'));
    </script>
@endif

@if (session('error'))
    <script>
        document.addEventListener('livewire:navigated', () => showToast(@json(session('error')), 'error'));
        document.addEventListener('DOMContentLoaded', () => showToast(@json(session('error')), 'error'));
    </script>
@endif