<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-white h-full">
    <main>

        <div class="min-h-screen flex items-center justify-center bg-white px-4 sm:px-6 lg:px-8">
            <div class="w-full max-w-md space-y-8">
                <div class="text-center">
                    <img class="mx-auto h-18 w-auto" src="{{ asset('images/Dressupdavaologo.png') }}"
                        alt="DressUp Davao" />
                    <h2 class="mt-10 text-center text-2xl/9 font-bold tracking-tight text-gray-900">
                        Register an Account
                    </h2>
                </div>

                <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
                    <form id="multi-step-form" class="space-y-6" action="{{ route('register.submit') }}" method="POST">
                        @csrf
                        <div id="step-1" class="step">

                            <div>
                                <label for="name" class="block text-sm/6 font-medium text-gray-900">Full
                                    Name</label>
                                <div class="mt-2">
                                    <input type="text" name="name" id="name" autocomplete="name" required
                                        class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-violet-600 sm:text-sm/6" />
                                </div>
                            </div>
                            <div>
                                <label for="email" class="block text-sm/6 font-medium text-gray-900">Email
                                    address</label>
                                <div class="mt-2">
                                    <input type="email" name="email" id="email" autocomplete="email" required
                                        class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-violet-600 sm:text-sm/6" />
                                </div>
                            </div>
                            <div>
                                <label for="password" class="block text-sm/6 font-medium text-gray-900">Password</label>
                                <div class="mt-2">
                                    <input type="password" name="password" id="password" autocomplete="new-password"
                                        required
                                        class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-violet-600 sm:text-sm/6" />
                                </div>
                            </div>
                            <div>
                                <label for="password_confirmation"
                                    class="block text-sm/6 font-medium text-gray-900">Confirm Password</label>
                                <div class="mt-2 pb-5">
                                    <input type="password" name="password_confirmation" id="password_confirmation"
                                        autocomplete="new-password" required
                                        class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-violet-600 sm:text-sm/6" />
                                </div>
                            </div>
                            <div>
                                <button type="button" onclick="nextStep()"
                                    class="flex w-full justify-center rounded-md bg-violet-600 px-3 py-1.5 text-sm/6 font-semibold text-white shadow-xs hover:bg-violet-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-violet-600">
                                    Next
                                </button>
                            </div>
                        </div>

                        <div id="step-2" class="step hidden">

                            <div class="space-y-6">
                                <div>
                                    <label for="color_preference"
                                        class="block text-sm/6 font-medium text-gray-900">Color Preference</label>
                                    <div class="mt-2">
                                        <select id="color_preference" name="color_preference"
                                            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-violet-600 sm:text-sm/6">
                                            <option value="">Choose a color</option>
                                            <option value="red">Red</option>
                                            <option value="blue">Blue</option>
                                            <option value="green">Green</option>
                                        </select>
                                    </div>
                                </div>
                                <div>
                                    <label for="style_preference_2"
                                        class="block text-sm/6 font-medium text-gray-900">Occasion</label>
                                    <div class="mt-2">
                                        <select id="style_preference_2" name="style_preference_2"
                                            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-violet-600 sm:text-sm/6">
                                            <option value="">Choose an occasion</option>
                                            <option value="formal">Formal</option>
                                            <option value="casual">Casual</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-6 flex gap-4">
                                <button type="button" onclick="prevStep()"
                                    class="flex-1 justify-center rounded-md border border-gray-300 px-3 py-1.5 text-sm/6 font-semibold text-gray-700 shadow-xs hover:bg-gray-100">
                                    Back
                                </button>
                                <button type="button" onclick="nextStep()"
                                    class="flex-1 justify-center rounded-md bg-violet-600 px-3 py-1.5 text-sm/6 font-semibold text-white shadow-xs hover:bg-violet-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-violet-600">
                                    Next
                                </button>
                            </div>
                        </div>

                        <div id="step-3" class="step hidden">

                            <div class="grid grid-cols-2 gap-4">
                                <div
                                    class="bg-gray-50 rounded-lg p-4 text-center cursor-pointer transition-shadow hover:shadow-lg body-type-card">
                                    <img src="https://via.placeholder.com/150x200.png?text=Apple" alt="Apple Body Type"
                                        class="w-full h-auto rounded-md mb-2">
                                    <span class="font-semibold text-gray-900">Apple</span>
                                </div>
                                <div
                                    class="bg-gray-50 rounded-lg p-4 text-center cursor-pointer transition-shadow hover:shadow-lg body-type-card">
                                    <img src="https://via.placeholder.com/150x200.png?text=Pear" alt="Pear Body Type"
                                        class="w-full h-auto rounded-md mb-2">
                                    <span class="font-semibold text-gray-900">Pear</span>
                                </div>
                                <div
                                    class="bg-gray-50 rounded-lg p-4 text-center cursor-pointer transition-shadow hover:shadow-lg body-type-card">
                                    <img src="https://via.placeholder.com/150x200.png?text=Hourglass"
                                        alt="Hourglass Body Type" class="w-full h-auto rounded-md mb-2">
                                    <span class="font-semibold text-gray-900">Hourglass</span>
                                </div>
                                <div
                                    class="bg-gray-50 rounded-lg p-4 text-center cursor-pointer transition-shadow hover:shadow-lg body-type-card">
                                    <img src="https://via.placeholder.com/150x200.png?text=Rectangle"
                                        alt="Rectangle Body Type" class="w-full h-auto rounded-md mb-2">
                                    <span class="font-semibold text-gray-900">Rectangle</span>
                                </div>
                            </div>
                            <input type="hidden" name="body_type" id="body-type-input">
                            <div class="mt-6 flex gap-4">
                                <button type="button" onclick="prevStep()"
                                    class="flex-1 justify-center rounded-md border border-gray-300 px-3 py-1.5 text-sm/6 font-semibold text-gray-700 shadow-xs hover:bg-gray-100">
                                    Back
                                </button>
                                <button type="submit"
                                    class="flex-1 justify-center rounded-md bg-violet-600 px-3 py-1.5 text-sm/6 font-semibold text-white shadow-xs hover:bg-violet-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-violet-600">
                                    Register
                                </button>
                            </div>
                        </div>
                    </form>
                    <p class="mt-10 text-center text-sm/6 text-gray-500">
                        Already have an account?
                        <a href="/login" class="font-semibold text-violet-600 hover:text-violet-500">Sign in</a>
                    </p>
                </div>
            </div>
        </div>


        <style>
            .error-message {
                color: #dc2626;
                /* Tailwind red-600 */
                font-size: 0.875rem;
                /* text-sm */
                margin-top: 0.25rem;
                /* mt-1 */
            }
        </style>

        <script>
            let currentStep = 1;
            const steps = document.querySelectorAll('.step');

            function showStep(stepNumber) {
                steps.forEach(step => step.classList.add('hidden'));
                document.getElementById(`step-${stepNumber}`).classList.remove('hidden');
                currentStep = stepNumber;
            }

            function clearErrors(step) {
                step.querySelectorAll('.error-message').forEach(el => el.textContent = '');
                step.querySelectorAll('input, select').forEach(input => {
                    input.classList.remove('border-red-500', 'focus:outline-red-500');
                });
            }

            function setError(input, message) {
                let errorDiv = input.parentElement.querySelector('.error-message');
                if (!errorDiv) {
                    errorDiv = document.createElement('div');
                    errorDiv.classList.add('error-message');
                    input.parentElement.appendChild(errorDiv);
                }
                errorDiv.textContent = message;
                input.classList.add('border-red-500', 'focus:outline-red-500');
            }

            function validateStep(stepNumber) {
                const step = document.getElementById(`step-${stepNumber}`);
                const inputs = step.querySelectorAll('input, select');
                clearErrors(step);

                let valid = true;

                // Required fields
                for (let input of inputs) {
                    if (input.hasAttribute('required') && !input.value.trim()) {
                        setError(input, 'This field is required.');
                        valid = false;
                    }
                }

                if (stepNumber === 1) {
                    const email = document.getElementById('email');
                    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (email.value && !emailPattern.test(email.value.trim())) {
                        setError(email, 'Please enter a valid email address.');
                        valid = false;
                    }

                    const password = document.getElementById('password');
                    const confirmPassword = document.getElementById('password_confirmation');
                    if (password.value && confirmPassword.value && password.value !== confirmPassword.value) {
                        setError(confirmPassword, 'Passwords do not match.');
                        valid = false;
                    }
                }

                if (stepNumber === 2) {
                    const colorPref = document.getElementById('color_preference');
                    const occasion = document.getElementById('style_preference_2');
                    if (!colorPref.value) {
                        setError(colorPref, 'Please select a color preference.');
                        valid = false;
                    }
                    if (!occasion.value) {
                        setError(occasion, 'Please select an occasion.');
                        valid = false;
                    }
                }

                if (stepNumber === 3) {
                    const bodyType = document.getElementById('body-type-input');
                    if (!bodyType.value) {
                        // No input element to attach directly, so show a global error
                        let grid = step.querySelector('.grid');
                        let errorDiv = step.querySelector('.bodytype-error');
                        if (!errorDiv) {
                            errorDiv = document.createElement('div');
                            errorDiv.classList.add('error-message', 'bodytype-error');
                            grid.insertAdjacentElement('afterend', errorDiv);
                        }
                        errorDiv.textContent = 'Please select a body type.';
                        valid = false;
                    }
                }

                return valid;
            }

            function nextStep() {
                if (validateStep(currentStep)) {
                    if (currentStep < steps.length) {
                        showStep(currentStep + 1);
                    }
                }
            }

            function prevStep() {
                if (currentStep > 1) {
                    showStep(currentStep - 1);
                }
            }

            // Body type selection logic
            document.querySelectorAll('.body-type-card').forEach(card => {
                card.addEventListener('click', () => {
                    document.querySelectorAll('.body-type-card').forEach(c => {
                        c.classList.remove('ring-2', 'ring-violet-600');
                    });
                    card.classList.add('ring-2', 'ring-violet-600');
                    const selectedBodyType = card.querySelector('span').textContent;
                    document.getElementById('body-type-input').value = selectedBodyType;

                    // Clear error when body type is selected
                    const errorDiv = document.querySelector('.bodytype-error');
                    if (errorDiv) errorDiv.textContent = '';
                });
            });

            // Initially show the first step
            document.addEventListener('DOMContentLoaded', () => {
                showStep(1);
            });
        </script>

    </main>
</body>

</html>