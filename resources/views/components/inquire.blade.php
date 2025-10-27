<div id="inquiryModal"
    class="hidden fixed inset-0 bg-black/50 items-center justify-center p-2 sm:p-4 z-50 overflow-y-auto">
    <div
        class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-auto p-4 sm:p-6 relative my-4 min-w-[280px] max-h-[90vh] overflow-y-auto">
        <button id="closeModalButton"
            class="absolute top-2 right-2 sm:top-4 sm:right-4 text-gray-500 hover:text-gray-700 transition-colors duration-200 p-1 rounded-full hover:bg-gray-100 z-10">
            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <h3
            class="text-xl sm:text-2xl font-bold text-gray-900 mb-4 sm:mb-6 text-center flex items-center justify-center">
            <svg class="w-5 h-5 sm:w-6 sm:h-6 mr-2 text-purple-600" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
            </svg>
            Inquire About Rental
        </h3>

        <form id="inquiryForm" action="#" method="POST" class="space-y-4 sm:space-y-6">
            {{-- User Information --}}
            <div class="grid grid-cols-1 gap-3 sm:gap-4">
                <div>
                    <label for="userName" class="block text-gray-700 text-sm font-semibold mb-1 sm:mb-2">Name</label>
                    <input type="text" id="userName" name="userName" readonly
                        class="w-full p-2 sm:p-3 border border-gray-300 rounded-lg bg-gray-50 text-gray-600 cursor-not-allowed focus:ring-2 focus:ring-purple-200 text-sm sm:text-base"
                        value="@auth{{ auth()->user()->name }}@else log in to inquire @endauth">
                </div>

                <div>
                    <label for="userEmail" class="block text-gray-700 text-sm font-semibold mb-1 sm:mb-2">Email</label>
                    <input type="email" id="userEmail" name="userEmail" readonly
                        class="w-full p-2 sm:p-3 border border-gray-300 rounded-lg bg-gray-50 text-gray-600 cursor-not-allowed focus:ring-2 focus:ring-purple-200 text-sm sm:text-base"
                        value="@auth{{ auth()->user()->email }}@else log in to inquire @endauth">
                </div>
            </div>

            {{-- Rental Date --}}
            <div>
                <label for="rentalDate"
                    class="block text-gray-700 text-sm font-semibold mb-1 sm:mb-2 flex items-center">
                    <svg class="w-4 h-4 mr-1 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Desired Rental Date
                </label>
                <input type="date" id="rentalDate" name="rentalDate" required
                    class="w-full p-2 sm:p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors duration-200 text-sm sm:text-base">
            </div>

            {{-- Message --}}
            <div>
                <label for="inquiryMessage"
                    class="block text-gray-700 text-sm font-semibold mb-1 sm:mb-2 flex items-center">
                    <svg class="w-4 h-4 mr-1 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                    </svg>
                    Message
                </label>
                <textarea id="inquiryMessage" name="inquiryMessage" rows="3" required
                    class="w-full p-2 sm:p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors duration-200 resize-none text-sm sm:text-base"
                    placeholder="I would like to inquire about this product..."></textarea>
            </div>

            {{-- Shop Policy Section --}}
            <div class="border-t border-gray-200 pt-3 sm:pt-4">
                <h4 class="text-sm font-semibold text-gray-700 mb-2 sm:mb-3 flex items-center">
                    <svg class="w-4 h-4 mr-1 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    Shop Policy
                </h4>
                <div id="shopPolicyContent"
                    class="bg-gray-50 p-3 sm:p-4 rounded-lg max-h-24 sm:max-h-32 overflow-y-auto text-xs sm:text-sm text-gray-600 border border-gray-200">
                    Loading shop policy...
                </div>

                {{-- Agreement Checkbox - Hidden for guests --}}
                <div id="agreementSection"
                    class="mt-3 sm:mt-4 flex items-start space-x-2 sm:space-x-3 @guest hidden @endguest">
                    <input type="checkbox" id="agreeToPolicy" name="agreeToPolicy"
                        class="h-4 w-4 sm:h-5 sm:w-5 text-purple-600 focus:ring-purple-500 border-gray-300 rounded mt-0.5 flex-shrink-0">
                    <label for="agreeToPolicy" class="block text-xs sm:text-sm text-gray-700 leading-tight">
                        I have read and agree to the shop's rental policy
                    </label>
                </div>
            </div>

            {{-- Terms Notice --}}
            <p class="text-xs sm:text-sm text-gray-600 text-center border-t border-gray-200 pt-3 sm:pt-4">
                By submitting this inquiry, you agree to be contacted by DressUp Davao regarding the rental of this
                product.
            </p>

            {{-- Submit Button --}}
            <button type="submit" id="submitInquiryBtn" disabled
                class="w-full flex justify-center items-center bg-gradient-to-r from-purple-600 to-indigo-600 text-white py-2 sm:py-3 rounded-lg font-semibold transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 relative disabled:from-purple-300 disabled:to-indigo-300 disabled:cursor-not-allowed hover:from-purple-700 hover:to-indigo-700 shadow-md hover:shadow-lg text-sm sm:text-base">
                @auth
                    <span class="btn-text">Send Inquiry</span>
                    <span
                        class="loading hidden absolute right-4 sm:right-6 animate-spin border-2 border-white border-t-transparent rounded-full w-4 h-4 sm:w-5 sm:h-5"></span>
                @else
                    Login to Inquire
                @endauth
            </button>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const inquiryModal = document.getElementById('inquiryModal');
        const closeModalButton = document.getElementById('closeModalButton');
        const inquiryForm = document.getElementById('inquiryForm');
        const isAuthenticated = @json(auth()->check());
        const submitBtn = document.getElementById('submitInquiryBtn');
        const btnText = submitBtn.querySelector('.btn-text');
        const loadingIcon = submitBtn.querySelector('.loading');
        const agreeToPolicyCheckbox = document.getElementById('agreeToPolicy');
        const shopPolicyContent = document.getElementById('shopPolicyContent');
        const agreementSection = document.getElementById('agreementSection');
        const cooldownSeconds = 60;
        let lastInquiryTime = 0;
        let cooldownInterval;
        let currentShopPolicy = '';

        // Function to show/hide agreement section based on authentication
        const toggleAgreementSection = () => {
            if (!isAuthenticated) {
                agreementSection.classList.add('hidden');
            } else {
                agreementSection.classList.remove('hidden');
            }
        };

        // Function to update submit button state
        const updateSubmitButtonState = () => {
            if (!isAuthenticated) {
                // For guests, always show "Login to Inquire" and keep disabled
                submitBtn.disabled = true;
                submitBtn.classList.add('from-purple-300', 'to-indigo-300', 'cursor-not-allowed');
                submitBtn.classList.remove('from-purple-600', 'to-indigo-600', 'hover:from-purple-700', 'hover:to-indigo-700', 'cursor-pointer');
                return;
            }

            const isAgreed = agreeToPolicyCheckbox.checked;
            const rentalDate = document.getElementById('rentalDate').value;
            const inquiryMessage = document.getElementById('inquiryMessage').value.trim();

            if (isAgreed && rentalDate && inquiryMessage) {
                submitBtn.disabled = false;
                submitBtn.classList.remove('from-purple-300', 'to-indigo-300', 'cursor-not-allowed');
                submitBtn.classList.add('from-purple-600', 'to-indigo-600', 'hover:from-purple-700', 'hover:to-indigo-700', 'cursor-pointer');
            } else {
                submitBtn.disabled = true;
                submitBtn.classList.add('from-purple-300', 'to-indigo-300', 'cursor-not-allowed');
                submitBtn.classList.remove('from-purple-600', 'to-indigo-600', 'hover:from-purple-700', 'hover:to-indigo-700', 'cursor-pointer');
            }
        };

        // Only allow future dates (not today)
        const dateInput = document.getElementById('rentalDate');
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        dateInput.setAttribute('min', tomorrow.toISOString().split('T')[0]);

        const getProductInfo = () => {
            if (window.productData) {
                return {
                    productName: window.productData.name,
                    productId: window.productData.id,
                    productOwner: window.productData.owner,
                    shopName: window.productData.shop,
                    shopId: window.productData.shopId
                };
            }

            // Fallback: Try to get shop ID from the product
            const productNameElement = document.querySelector('h1') || document.querySelector('.product-name');
            const productName = productNameElement ? productNameElement.textContent.trim() : 'Product';
            const urlParts = window.location.pathname.split('/');
            const productId = urlParts[urlParts.length - 1];

            return {
                productName,
                productId,
                productOwner: 'Shop Owner',
                shopName: 'Shop',
                shopId: window.productData?.shopId || null
            };
        };

        const fetchShopPolicy = async (productId) => {
            if (!productId) {
                shopPolicyContent.innerHTML = '<p class="text-gray-500 text-center py-4">No product ID available for policy lookup.</p>';
                console.warn('fetchShopPolicy called without productId');
                return;
            }

            console.log('Fetching shop policy for product ID:', productId, 'Auth status:', isAuthenticated);

            try {
                // Use the simplified route path
                const policyResponse = await fetch(`/shop-policy/${productId}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    }
                });

                const policyData = await policyResponse.json();
                console.log('Shop policy response:', policyResponse.status, policyData);

                if (policyResponse.ok && policyData.policy) {
                    currentShopPolicy = policyData.policy;
                    shopPolicyContent.innerHTML = `
                        <div>
                            ${policyData.shop_name ? `<p class="text-sm font-medium text-gray-700 mb-2">${policyData.shop_name} Rental Policy:</p>` : ''}
                            <p class="whitespace-pre-wrap text-gray-600">${currentShopPolicy}</p>
                        </div>
                    `;
                } else if (policyResponse.status === 401) {
                    // Handle authentication error - show login prompt
                    shopPolicyContent.innerHTML = `
                        <div class="text-center py-4">
                            <p class="text-gray-500 mb-3">Please log in to view the shop policy</p>
                            <button type="button" onclick="window.location.href='/login'"
                                class="bg-gradient-to-r from-purple-600 to-indigo-600 text-white px-4 py-2 rounded-lg hover:from-purple-700 hover:to-indigo-700 transition-all duration-200 font-medium">
                                Log In Now
                            </button>
                        </div>
                    `;
                } else if (policyData.error) {
                    shopPolicyContent.innerHTML = `<p class="text-gray-500 text-center py-4">${policyData.error}</p>`;
                } else {
                    shopPolicyContent.innerHTML = '<p class="text-gray-500 text-center py-4">No shop policy available.</p>';
                }
            } catch (error) {
                console.error('Error fetching shop policy:', error);
                shopPolicyContent.innerHTML = `
                    <div class="text-center py-4">
                        <p class="text-red-500 text-sm mb-2">Unable to load shop policy</p>
                        <p class="text-gray-400 text-xs">${error.message}</p>
                        <button type="button" onclick="window.retryFetchShopPolicy()"
                            class="mt-2 text-purple-600 hover:text-purple-700 text-sm underline">
                            Try Again
                        </button>
                    </div>
                `;
            }
        };

        const closeModal = () => {
            inquiryModal.classList.add('hidden');
            document.body.style.overflow = '';
            if (agreeToPolicyCheckbox) {
                agreeToPolicyCheckbox.checked = false;
            }
            updateSubmitButtonState();
        };

        const openModal = () => {
            inquiryModal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';

            const { productName, productId } = getProductInfo();
            const messageTextarea = document.getElementById('inquiryMessage');

            // Only pre-fill message for authenticated users
            if (isAuthenticated && (messageTextarea.value.trim() === '' || messageTextarea.value.includes('inquire about'))) {
                messageTextarea.value = `I would like to inquire about this product: ${productName}`;
            }

            // Show/hide agreement section based on authentication
            toggleAgreementSection();

            // Fetch shop policy (will show login prompt for guests)
            fetchShopPolicy(productId);
        };

        document.getElementById('inquireButton')?.addEventListener('click', openModal);

        // Event listeners for form validation (only for authenticated users)
        if (isAuthenticated) {
            agreeToPolicyCheckbox.addEventListener('change', updateSubmitButtonState);
        }
        dateInput.addEventListener('change', updateSubmitButtonState);
        document.getElementById('inquiryMessage').addEventListener('input', updateSubmitButtonState);

        closeModalButton.addEventListener('click', closeModal);
        inquiryModal.addEventListener('click', e => {
            if (e.target === inquiryModal) closeModal();
        });

        inquiryForm.addEventListener('submit', async function (event) {
            event.preventDefault();

            if (!isAuthenticated) {
                window.location.href = '/login';
                return;
            }

            const now = Date.now();
            const timeSinceLastInquiry = (now - lastInquiryTime) / 1000;
            if (timeSinceLastInquiry < cooldownSeconds) {
                const remaining = Math.ceil(cooldownSeconds - timeSinceLastInquiry);
                showToast(`Please wait ${remaining}s before sending another inquiry.`, 'warning');
                return;
            }

            const rentalDate = dateInput.value;
            const inquiryMessage = document.getElementById('inquiryMessage').value.trim();
            const { productName, productId, productOwner, shopName } = getProductInfo();

            if (!rentalDate || !inquiryMessage) {
                showToast('Fill in all required fields.', 'error');
                return;
            }

            if (!agreeToPolicyCheckbox.checked) {
                showToast('Agree to the shop policy before sending an inquiry.', 'error');
                return;
            }

            submitBtn.disabled = true;
            btnText.textContent = 'Sending...';
            loadingIcon.classList.remove('hidden');

            try {
                const formattedMessage =
                    `🔍 PRODUCT INQUIRY\n\nProduct: ${productName}\nShop: ${shopName}\nDesired Rental Date: ${rentalDate}\n\nMessage: ${inquiryMessage}`;

                const response = await fetch('/chat/send-inquiry', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        product_id: productId,
                        message: formattedMessage,
                        rental_date: rentalDate,
                        original_message: inquiryMessage,
                        thumbnail_path: window.productData?.thumbnail || null
                    })
                });

                const data = await response.json();

                if (response.ok) {
                    showToast(`Your inquiry has been sent successfully to ${shopName}!`, 'success', 5000);
                    inquiryForm.reset();
                    agreeToPolicyCheckbox.checked = false;
                    lastInquiryTime = now;

                    setTimeout(closeModal, 700);

                    let remaining = cooldownSeconds;
                    cooldownInterval = setInterval(() => {
                        remaining--;
                        btnText.textContent = `Wait ${remaining}s`;
                        if (remaining <= 0) {
                            clearInterval(cooldownInterval);
                            updateSubmitButtonState();
                            btnText.textContent = 'Send Inquiry';
                        }
                    }, 1000);
                } else {
                    throw new Error(data.message || 'Failed to send inquiry.');
                }
            } catch (error) {
                console.error('Error:', error);
                showToast('Failed to send inquiry. Please try again later.', 'error');
                submitBtn.disabled = false;
                updateSubmitButtonState();
            } finally {
                loadingIcon.classList.add('hidden');
            }
        });

        // Make retry function globally accessible
        window.retryFetchShopPolicy = () => {
            const { productId } = getProductInfo();
            fetchShopPolicy(productId);
        };

        // Initialize agreement section visibility and button state
        toggleAgreementSection();
        updateSubmitButtonState();
    });
</script>