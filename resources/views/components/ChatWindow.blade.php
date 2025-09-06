<div>
    <button id="openChatBtn"
        class="fixed bottom-6 right-6 bg-purple-600 text-white p-4 rounded-full shadow-lg
               hover:bg-purple-700 transition-all duration-300 z-50 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">
        <span class="text-lg font-semibold">Chat</span>
    </button>

    <div id="chatWindow"
        class="fixed bottom-6 right-6 w-[700px] h-96 bg-white rounded-lg shadow-xl border border-gray-200
               flex flex-col transform translate-y-full opacity-0 invisible transition-all duration-300 ease-in-out z-40">

        <div class="flex justify-between items-center bg-purple-600 text-white p-3 rounded-t-lg">
            <h3 class="text-lg font-semibold">Messages</h3>
            <button id="closeChatBtn" class="text-white hover:text-gray-200 focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
        </div>

        <div class="flex flex-grow">

            <div class="w-1/3 border-r border-gray-200 overflow-y-auto">
                <div class="p-3 bg-gray-50 border-b border-gray-200">
                    <input type="text" placeholder="Search contacts..."
                        class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500">
                </div>

                <div class="contact-item flex items-center p-3 cursor-pointer hover:bg-gray-100 transition-colors duration-150 active-chat"
                    data-chat-id="user1">
                    <img src="https://via.placeholder.com/40" alt="User Avatar"
                        class="w-10 h-10 rounded-full mr-3 object-cover">
                    <div class="flex-grow">
                        <p class="font-semibold text-gray-800">John Doe</p>
                        <p class="text-sm text-gray-500 truncate">Last message preview...</p>
                    </div>
                    <span class="text-xs text-gray-400">2h ago</span>
                </div>
                <div class="contact-item flex items-center p-3 cursor-pointer hover:bg-gray-100 transition-colors duration-150"
                    data-chat-id="user2">
                    <img src="https://via.placeholder.com/40" alt="User Avatar"
                        class="w-10 h-10 rounded-full mr-3 object-cover">
                    <div class="flex-grow">
                        <p class="font-semibold text-gray-800">Jane Smith</p>
                        <p class="text-sm text-gray-500 truncate">Okay, sounds good!</p>
                    </div>
                    <span class="text-xs text-gray-400">Yesterday</span>
                </div>

                <div class="contact-item flex items-center p-3 cursor-pointer hover:bg-gray-100 transition-colors duration-150"
                    data-chat-id="user3">
                    <img src="https://via.placeholder.com/40" alt="User Avatar"
                        class="w-10 h-10 rounded-full mr-3 object-cover">
                    <div class="flex-grow">
                        <p class="font-semibold text-gray-800">Team Lead</p>
                        <p class="text-sm text-gray-500 truncate">Meeting at 3 PM.</p>
                    </div>
                    <span class="text-xs text-gray-400">Mon</span>
                </div>
            </div>

            <div class="flex-grow flex flex-col">
                <div class="bg-gray-50 border-b border-gray-200 p-3 flex items-center">
                    <img src="https://via.placeholder.com/40" alt="Active User Avatar"
                        class="w-10 h-10 rounded-full mr-3 object-cover">
                    <p class="font-semibold text-lg text-gray-800" id="activeChatName">John Doe</p>
                </div>

                <div class="flex-grow p-4 overflow-y-auto space-y-4" id="chatMessagesContainer">
                    <div class="flex justify-end">
                        <div class="bg-purple-500 text-white p-3 rounded-lg max-w-[75%]">
                            <p>Hey John! How are you doing today? Just wanted to check in about the project.</p>
                            <span class="block text-xs text-purple-100 mt-1 text-right">5:30 PM</span>
                        </div>
                    </div>

                    <div class="flex justify-start">
                        <div class="bg-gray-200 text-gray-800 p-3 rounded-lg max-w-[75%]">
                            <p>I'm good, thanks! Project is on track. I'll send you an update by end of day.</p>
                            <span class="block text-xs text-gray-500 mt-1 text-left">5:32 PM</span>
                        </div>
                    </div>

                </div>

                <div class="border-t border-gray-200 p-3 flex items-center">
                    <input type="text" placeholder="Type a message..."
                        class="flex-grow p-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring-1 focus:ring-purple-500">
                    <button
                        class="bg-purple-600 text-white p-2 rounded-r-md hover:bg-purple-700 transition-colors duration-300 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const openChatBtn = document.getElementById('openChatBtn');
            const chatWindow = document.getElementById('chatWindow');
            const closeChatBtn = document.getElementById('closeChatBtn');
            const contactItems = document.querySelectorAll('.contact-item');
            const activeChatName = document.getElementById('activeChatName');
            const chatMessagesContainer = document.getElementById('chatMessagesContainer');

            // --- Function to show the chat button ---
            const showChatButton = () => {
                openChatBtn.classList.remove('opacity-0', 'invisible');
                openChatBtn.classList.add('opacity-100', 'visible');
            };

            // --- Function to hide the chat button ---
            const hideChatButton = () => {
                openChatBtn.classList.remove('opacity-100', 'visible');
                openChatBtn.classList.add('opacity-0', 'invisible');
            };

            // --- Chat Window Open/Close Logic ---
            openChatBtn.addEventListener('click', () => {
                chatWindow.classList.remove('translate-y-full', 'opacity-0', 'invisible');
                chatWindow.classList.add('translate-y-0', 'opacity-100', 'visible');
                hideChatButton(); // Hide the button when the window opens
            });

            closeChatBtn.addEventListener('click', () => {
                chatWindow.classList.remove('translate-y-0', 'opacity-100', 'visible');
                chatWindow.classList.add('translate-y-full', 'opacity-0', 'invisible');
                showChatButton(); // Show the button when the window closes
            });

            // --- Chat Selection Logic ---
            contactItems.forEach(item => {
                item.addEventListener('click', () => {
                    // Remove 'active-chat' from all items
                    contactItems.forEach(contact => contact.classList.remove('active-chat'));

                    // Add 'active-chat' to the clicked item
                    item.classList.add('active-chat');

                    // Update active chat name (e.g., from the contact item's text)
                    const contactName = item.querySelector('p.font-semibold').textContent;
                    activeChatName.textContent = contactName;

                    // --- Dynamic Chat Message Loading (To be implemented by you) ---
                    const chatId = item.dataset.chatId;

                    // Clear previous messages
                    chatMessagesContainer.innerHTML = '';

                    // Placeholder for loading messages:
                    chatMessagesContainer.innerHTML = `
                        <div class="flex justify-center text-gray-400 text-sm">
                            --- Conversation with ${contactName} (${chatId}) ---
                        </div>
                        <div class="flex justify-start">
                            <div class="bg-gray-200 text-gray-800 p-3 rounded-lg max-w-[75%]">
                                This is a new chat with ${contactName}.
                            </div>
                        </div>
                    `;

                    // Optionally scroll to bottom of chat messages
                    chatMessagesContainer.scrollTop = chatMessagesContainer.scrollHeight;
                });
            });

            // --- Initial Load: Select the first contact by default ---
            if (contactItems.length > 0) {
                contactItems[0].click(); // Simulate a click on the first contact
            }
        });
    </script>

    <style>
        /* Custom style for the active chat item in the contact list */
        .contact-item.active-chat {
            background-color: #f3e8ff;
            /* purple-50 */
            border-left: 4px solid #8b5cf6;
            /* purple-500 */
        }
    </style>
</div>