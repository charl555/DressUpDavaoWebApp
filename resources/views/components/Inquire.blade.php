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
                <label for="rentalDate" class="block text-gray-700 text-sm font-semibold mb-2">Desired Rental
                    Date:</label>
                <input type="date" id="rentalDate" name="rentalDate" required
                    class="w-full p-3 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500">
            </div>

            <div>
                <label for="returnDate" class="block text-gray-700 text-sm font-semibold mb-2">Desired Return
                    Date:</label>
                <input type="date" id="returnDate" name="returnDate" required
                    class="w-full p-3 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500">
            </div>

            <div>
                <label for="inquiryMessage" class="block text-gray-700 text-sm font-semibold mb-2">Additional Details
                    (Optional):</label>
                <textarea id="inquiryMessage" name="inquiryMessage" rows="4"
                    class="w-full p-3 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500"
                    placeholder="E.g., specific event date, delivery preference, any questions..."></textarea>
            </div>

            <p class="text-sm text-gray-600 mt-4">
                By submitting this inquiry, you agree to be contacted by DressUp Davao Boutique regarding the rental of
                this product.
            </p>

            <button type="submit"
                class="w-full bg-purple-600 text-white py-3 rounded-md font-semibold hover:bg-purple-700 transition-colors duration-300 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">
                Send Inquiry
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

        // Show the modal when the inquire button is clicked
        inquireButton.addEventListener('click', function () {
            inquiryModal.classList.remove('hidden');
            // Optional: Disable scrolling on the body when modal is open
            document.body.style.overflow = 'hidden';
        });

        // Hide the modal when the close button is clicked
        closeModalButton.addEventListener('click', function () {
            inquiryModal.classList.add('hidden');
            // Optional: Re-enable scrolling on the body
            document.body.style.overflow = '';
        });

        // Hide the modal if clicked outside the modal content
        inquiryModal.addEventListener('click', function (event) {
            if (event.target === inquiryModal) {
                inquiryModal.classList.add('hidden');
                document.body.style.overflow = '';
            }
        });

        // Handle form submission (this is where the inquiry process begins)
        inquiryForm.addEventListener('submit', function (event) {
            event.preventDefault(); // Prevent default form submission

            const rentalDate = document.getElementById('rentalDate').value;
            const returnDate = document.getElementById('returnDate').value;
            const inquiryMessage = document.getElementById('inquiryMessage').value;

            // Here, you would send this data to your backend
            // For demonstration, we'll just log it and close the modal
            console.log('Inquiry Submitted:');
            console.log('Product:', 'Red Dress'); // You'd dynamically get product name/ID
            console.log('Desired Rental Date:', rentalDate);
            console.log('Desired Return Date:', returnDate);
            console.log('Additional Message:', inquiryMessage);

            // You would typically send an AJAX request here (e.g., using fetch API or Axios)
            // Example using fetch:
            /*
            fetch('/api/inquire-rental', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': 'YOUR_CSRF_TOKEN_HERE' // Important for Laravel/security
                },
                body: JSON.stringify({
                    product_id: 'PRODUCT_ID_HERE', // Pass the actual product ID
                    rental_date: rentalDate,
                    return_date: returnDate,
                    message: inquiryMessage
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Your inquiry has been sent successfully!');
                    inquiryModal.classList.add('hidden');
                    document.body.style.overflow = '';
                    inquiryForm.reset(); // Clear the form
                } else {
                    alert('Failed to send inquiry: ' + (data.message || 'Unknown error.'));
                }
            })
            .catch(error => {
                console.error('Error sending inquiry:', error);
                alert('An error occurred. Please try again.');
            });
            */

            // For now, just simulate success and close
            alert('Your inquiry has been sent successfully!');
            inquiryModal.classList.add('hidden');
            document.body.style.overflow = '';
            inquiryForm.reset(); // Clear the form
        });
    });
</script>