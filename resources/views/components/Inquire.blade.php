<div id="inquiryModal"
    class="hidden fixed inset-0 bg-black text-black bg-opacity-50 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-lg p-6 relative">
        <button id="closeModalButton"
            class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 text-2xl font-bold">
            &times;
        </button>

        <h3 class="text-2xl font-bold text-gray-900 mb-6 text-center">Inquire About Rental</h3>

        <form id="inquiryForm" action="#" method="POST" class="space-y-4">
            <div>
                <label for="userName" class="block text-gray-700 text-sm font-semibold mb-2">Name:</label>
                <input type="text" id="userName" name="userName" readonly
                    class="w-full p-3 border border-gray-300 rounded-md bg-gray-100 text-gray-600 cursor-not-allowed"
                    value="@auth{{ auth()->user()->name }}@else Please log in to inquire @endauth">
            </div>

            <div>
                <label for="userEmail" class="block text-gray-700 text-sm font-semibold mb-2">Email:</label>
                <input type="email" id="userEmail" name="userEmail" readonly
                    class="w-full p-3 border border-gray-300 rounded-md bg-gray-100 text-gray-600 cursor-not-allowed"
                    value="@auth{{ auth()->user()->email }}@else Please log in to inquire @endauth">
            </div>

            <div>
                <label for="rentalDate" class="block text-gray-700 text-sm font-semibold mb-2">Desired Rental
                    Date:</label>
                <input type="date" id="rentalDate" name="rentalDate" required
                    class="w-full p-3 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500">
            </div>

            <div>
                <label for="inquiryMessage" class="block text-gray-700 text-sm font-semibold mb-2">Message:</label>
                <textarea id="inquiryMessage" name="inquiryMessage" rows="4" required
                    class="w-full p-3 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500"
                    placeholder="I would like to inquire about this product."></textarea>
            </div>

            <p class="text-sm text-gray-600 mt-4">
                By submitting this inquiry, you agree to be contacted by DressUp Davao Boutique regarding the rental of
                this product.
            </p>

            <button type="submit" id="submitInquiryBtn"
                class="w-full bg-purple-600 text-white py-3 rounded-md font-semibold hover:bg-purple-700 transition-colors duration-300 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">
                @auth
                    Send Inquiry
                @else
                    Login to Inquire
                @endauth
            </button>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const inquireButton = document.getElementById('inquireButton');
        const inquiryModal = document.getElementById('inquiryModal');
        const closeModalButton = document.getElementById('closeModalButton');
        const inquiryForm = document.getElementById('inquiryForm');
        const isAuthenticated = @json(auth()->check());

        // Get product information from the page
        const getProductInfo = () => {
            // Use the product data passed from the Overview component
            if (window.productData) {
                return {
                    productName: window.productData.name,
                    productId: window.productData.id,
                    productOwner: window.productData.owner,
                    shopName: window.productData.shop
                };
            }

            // Fallback: Try to get product name from the page title or heading
            const productNameElement = document.querySelector('h1') || document.querySelector('.product-name');
            const productName = productNameElement ? productNameElement.textContent.trim() : 'Product';

            // Get product ID from URL or data attribute
            const urlParts = window.location.pathname.split('/');
            const productId = urlParts[urlParts.length - 1];

            return { productName, productId, productOwner: 'Shop Owner', shopName: 'Shop' };
        };

        // Show the modal when the inquire button is clicked
        inquireButton.addEventListener('click', function () {
            inquiryModal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';

            // Pre-fill the message with product information
            const { productName, shopName } = getProductInfo();
            const messageTextarea = document.getElementById('inquiryMessage');
            if (messageTextarea.value === '' || messageTextarea.value === 'I would like to inquire about this product.') {
                messageTextarea.value = `I would like to inquire about this product: ${productName}`;
            }
        });

        // Hide the modal when the close button is clicked
        closeModalButton.addEventListener('click', function () {
            inquiryModal.classList.add('hidden');
            document.body.style.overflow = '';
        });

        // Hide the modal if clicked outside the modal content
        inquiryModal.addEventListener('click', function (event) {
            if (event.target === inquiryModal) {
                inquiryModal.classList.add('hidden');
                document.body.style.overflow = '';
            }
        });

        // Handle form submission
        inquiryForm.addEventListener('submit', async function (event) {
            event.preventDefault();

            // Check if user is authenticated
            if (!isAuthenticated) {
                // Redirect to login page
                window.location.href = '/login';
                return;
            }

            const rentalDate = document.getElementById('rentalDate').value;
            const inquiryMessage = document.getElementById('inquiryMessage').value;
            const { productName, productId, productOwner, shopName } = getProductInfo();

            // Validate required fields
            if (!rentalDate || !inquiryMessage.trim()) {
                alert('Please fill in all required fields.');
                return;
            }

            // Disable submit button to prevent double submission
            const submitBtn = document.getElementById('submitInquiryBtn');
            const originalText = submitBtn.textContent;
            submitBtn.disabled = true;
            submitBtn.textContent = 'Sending...';

            try {
                // Format the inquiry message for the chat system
                const formattedMessage = `🔍 PRODUCT INQUIRY\n\n` +
                    `Product: ${productName}\n` +
                    `Shop: ${shopName}\n` +
                    `Desired Rental Date: ${rentalDate}\n\n` +
                    `Message: ${inquiryMessage}`;

                // Send the inquiry as a chat message to the product owner
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
                        original_message: inquiryMessage
                    })
                });

                const data = await response.json();

                if (response.ok) {
                    alert(`Your inquiry has been sent successfully to ${shopName}! ${productOwner} will respond to you soon through the chat system.`);
                    inquiryModal.classList.add('hidden');
                    document.body.style.overflow = '';
                    inquiryForm.reset();
                } else {
                    throw new Error(data.message || 'Failed to send inquiry');
                }
            } catch (error) {
                console.error('Error sending inquiry:', error);
                alert('Failed to send inquiry. Please try again or contact support.');
            } finally {
                // Re-enable submit button
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            }
        });
    });
</script>