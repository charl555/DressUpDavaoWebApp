@auth
    {{-- Hide chat window for admins --}}
    @if(auth()->user()->role !== 'Admin' && auth()->user()->role !== 'SuperAdmin')
        <div>
            <!-- Floating Chat Button -->
            <button id="openChatBtn"
                class="fixed bottom-6 right-6 bg-purple-600 text-white p-4 rounded-full shadow-lg hover:bg-purple-700 transition-all duration-300 z-[9999] focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 isolate">
                <span class="text-lg font-semibold">Chat</span>
                <span id="unreadBadge"
                    class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-6 w-6 items-center justify-center hidden">0</span>
            </button>

            <!-- Chat Window -->
            <div id="chatWindow"
                class="fixed bottom-6 right-6 bg-white rounded-lg shadow-xl border border-gray-200 flex flex-col transform translate-y-full opacity-0 invisible transition-all duration-300 ease-in-out z-[9998] overflow-hidden resize isolate"
                style="width: 900px; height: 600px; min-width: 400px; min-height: 400px; max-width: 95vw; max-height: 90vh;">

                <!-- Chat Header -->
                <div id="chatHeader"
                    class="flex justify-between items-center bg-purple-600 text-white p-3 rounded-t-lg cursor-move">
                    <h3 class="text-lg font-semibold">Messages</h3>
                    <button id="closeChatBtn" class="text-white hover:text-gray-200 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </button>
                </div>

                <!-- Chat Body -->
                <div class="flex flex-grow min-h-0" id="chatBody">
                    <!-- Contact List -->
                    <div id="contactsSection" class="w-1/3 border-r border-gray-200 overflow-y-auto hidden sm:block">
                        <div class="p-3 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                            <input type="text" id="searchContacts" placeholder="Search contacts..."
                                class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500">
                        </div>
                        <div id="contactsList" class="overflow-y-auto">
                            <div class="flex items-center justify-center p-8 text-gray-500">
                                <div class="text-center">
                                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600 mx-auto mb-2">
                                    </div>
                                    <p>Loading contacts...</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Chat Messages Section -->
                    <div id="chatSection" class="flex-grow flex flex-col min-h-0 w-full sm:w-2/3">
                        <div class="bg-gray-50 border-b border-gray-200 p-3 flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full mr-3 bg-gray-300 flex items-center justify-center">
                                    <span class="text-gray-600 font-semibold" id="activeChatInitial">?</span>
                                </div>
                                <div>
                                    <p class="font-semibold text-lg text-gray-800" id="activeChatName">Select a contact</p>
                                    <p class="text-sm text-gray-500" id="activeChatRole"></p>
                                </div>
                            </div>
                            <button id="backToContactsBtn" class="sm:hidden text-purple-600 font-medium hover:underline hidden">
                                Back
                            </button>
                        </div>

                        <div class="flex-grow p-4 overflow-y-auto min-h-0" id="chatMessagesContainer">
                            <div class="flex items-center justify-center h-full text-gray-500">
                                <div class="text-center">
                                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                                        </path>
                                    </svg>
                                    <p>Select a contact to start chatting</p>
                                </div>
                            </div>
                        </div>

                        <!-- Message Input -->
                        <div class="border-t border-gray-200 p-3 flex-shrink-0">
                            <div id="imagePreview" class="mb-2 hidden">
                                <div class="relative inline-block">
                                    <img id="previewImg" class="max-w-32 max-h-32 rounded-lg border">
                                    <button id="removeImageBtn"
                                        class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600">×</button>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <input type="file" id="imageInput" accept="image/*" class="hidden">
                                <button id="imageBtn" disabled
                                    class="p-2 text-gray-500 hover:text-purple-600 focus:outline-none disabled:text-gray-300 disabled:cursor-not-allowed mr-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                </button>
                                <input type="text" id="messageInput" placeholder="Type a message..." disabled
                                    class="flex-grow p-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring-1 focus:ring-purple-500 disabled:bg-gray-100">
                                <button id="sendMessageBtn" disabled
                                    class="bg-purple-600 text-white p-2 rounded-r-md hover:bg-purple-700 transition-colors duration-300 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 disabled:bg-gray-400 disabled:cursor-not-allowed">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Click Outside Overlay -->
            <div id="chatOverlay" class="fixed inset-0 bg-black/30 z-[9997] hidden"></div>

            <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
            <script>
                // Global variables
                let currentChatUserId = null;
                let allContacts = [];
                let selectedImage = null;

                document.addEventListener('DOMContentLoaded', () => {
                    const chatWindow = document.getElementById('chatWindow');
                    const openChatBtn = document.getElementById('openChatBtn');
                    const closeChatBtn = document.getElementById('closeChatBtn');
                    const chatOverlay = document.getElementById('chatOverlay');
                    const contactsSection = document.getElementById('contactsSection');
                    const chatSection = document.getElementById('chatSection');
                    const backToContactsBtn = document.getElementById('backToContactsBtn');

                    let isChatOpen = false;

                    // Function to open chat
                    const openChat = () => {
                        chatWindow.classList.remove('translate-y-full', 'opacity-0', 'invisible');
                        chatWindow.classList.add('translate-y-0', 'opacity-100', 'visible');
                        openChatBtn.classList.add('hidden');
                        chatOverlay.classList.remove('hidden');
                        isChatOpen = true;

                        // Set initial mobile state
                        if (window.innerWidth <= 640) {
                            contactsSection.classList.remove('hidden');
                            chatSection.classList.add('hidden');
                            backToContactsBtn.classList.add('hidden');
                        }

                        // Load contacts when opening the chat
                        if (typeof loadContacts === 'function') {
                            loadContacts();
                        }
                    };

                    // Function to close chat
                    const closeChat = () => {
                        chatWindow.classList.add('translate-y-full', 'opacity-0', 'invisible');
                        chatWindow.classList.remove('translate-y-0', 'opacity-100', 'visible');
                        openChatBtn.classList.remove('hidden');
                        chatOverlay.classList.add('hidden');
                        isChatOpen = false;
                    };

                    // === Toggle chat visibility ===
                    openChatBtn.addEventListener('click', openChat);
                    closeChatBtn.addEventListener('click', closeChat);

                    // === Escape key to close ===
                    document.addEventListener('keydown', (e) => {
                        if (e.key === 'Escape' && isChatOpen) {
                            closeChat();
                        }
                    });

                    // === Click outside to close ===
                    chatOverlay.addEventListener('click', closeChat);

                    // Prevent chat window from closing when clicking inside it
                    chatWindow.addEventListener('click', (e) => {
                        e.stopPropagation();
                    });

                    /* === Mobile tab switching === */
                    function switchToChatView() {
                        if (window.innerWidth <= 640) {
                            contactsSection.classList.add('hidden');
                            chatSection.classList.remove('hidden');
                            backToContactsBtn.classList.remove('hidden');
                        }
                    }

                    function switchToContactsView() {
                        if (window.innerWidth <= 640) {
                            chatSection.classList.add('hidden');
                            contactsSection.classList.remove('hidden');
                            backToContactsBtn.classList.add('hidden');

                            // Reset active chat state
                            currentChatUserId = null;
                            activeChatName.textContent = 'Select a contact';
                            activeChatRole.textContent = '';
                            activeChatInitial.textContent = '?';
                            chatMessagesContainer.innerHTML = `
                                                        <div class="flex items-center justify-center h-full text-gray-500">
                                                            <div class="text-center">
                                                                <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                        d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                                                                    </path>
                                                                </svg>
                                                                <p>Select a contact to start chatting</p>
                                                            </div>
                                                        </div>
                                                    `;

                            // Disable message input
                            messageInput.disabled = true;
                            sendMessageBtn.disabled = true;
                            imageBtn.disabled = true;
                        }
                    }

                    // Back to contacts button event listener
                    backToContactsBtn.addEventListener('click', switchToContactsView);

                    // Handle window resize
                    window.addEventListener('resize', () => {
                        if (window.innerWidth > 640) {
                            // On larger screens, show both sections
                            contactsSection.classList.remove('hidden');
                            chatSection.classList.remove('hidden');
                            backToContactsBtn.classList.add('hidden');
                        } else {
                            // On mobile, maintain current view state
                            if (currentChatUserId) {
                                // If in a chat, show chat view
                                contactsSection.classList.add('hidden');
                                chatSection.classList.remove('hidden');
                                backToContactsBtn.classList.remove('hidden');
                            } else {
                                // If no chat selected, show contacts
                                contactsSection.classList.remove('hidden');
                                chatSection.classList.add('hidden');
                                backToContactsBtn.classList.add('hidden');
                            }
                        }
                    });

                    // === Keep chat within viewport (in case of resizing or zoom changes) ===
                    const keepInViewport = () => {
                        const rect = chatWindow.getBoundingClientRect();
                        const margin = 10;

                        if (rect.right > window.innerWidth - margin) {
                            chatWindow.style.right = `${margin}px`;
                        }
                        if (rect.bottom > window.innerHeight - margin) {
                            chatWindow.style.bottom = `${margin}px`;
                        }
                    };

                    window.addEventListener('resize', keepInViewport);
                    new ResizeObserver(keepInViewport).observe(chatWindow);
                });

                document.addEventListener('DOMContentLoaded', () => {
                    // Initialize Pusher
                    const pusherKey = '{{ config('broadcasting.connections.pusher.key') ?? env('PUSHER_APP_KEY') }}';
                    const pusherCluster = '{{ config('broadcasting.connections.pusher.options.cluster') ?? env('PUSHER_APP_CLUSTER') }}';

                    if (pusherKey && pusherCluster) {
                        const pusher = new Pusher(pusherKey, {
                            cluster: pusherCluster,
                            encrypted: true
                        });

                        // Subscribe to user's private channel
                        const channel = pusher.subscribe('private-chat.{{ auth()->id() }}');

                        // Listen for new messages
                        channel.bind('message.sent', function (data) {
                            if (currentChatUserId === data.sender_id) {
                                appendMessage(data, false);
                            }
                            updateUnreadCount();
                            loadContacts(); // Refresh contacts to update latest message
                        });
                    } else {
                        console.warn('Pusher not configured. Real-time messaging will not work.');
                    }

                    // DOM elements
                    const contactsList = document.getElementById('contactsList');
                    const activeChatName = document.getElementById('activeChatName');
                    const activeChatRole = document.getElementById('activeChatRole');
                    const activeChatInitial = document.getElementById('activeChatInitial');
                    const chatMessagesContainer = document.getElementById('chatMessagesContainer');
                    const messageInput = document.getElementById('messageInput');
                    const sendMessageBtn = document.getElementById('sendMessageBtn');
                    const searchContacts = document.getElementById('searchContacts');
                    const unreadBadge = document.getElementById('unreadBadge');
                    const imageInput = document.getElementById('imageInput');
                    const imageBtn = document.getElementById('imageBtn');
                    const imagePreview = document.getElementById('imagePreview');
                    const previewImg = document.getElementById('previewImg');
                    const removeImageBtn = document.getElementById('removeImageBtn');

                    // Event listeners for message sending
                    sendMessageBtn.addEventListener('click', sendMessage);
                    messageInput.addEventListener('keypress', (e) => {
                        if (e.key === 'Enter') {
                            sendMessage();
                        }
                    });

                    // Search functionality
                    searchContacts.addEventListener('input', (e) => {
                        const searchTerm = e.target.value.toLowerCase();
                        filterContacts(searchTerm);
                    });

                    // Image upload functionality
                    imageBtn.addEventListener('click', () => {
                        imageInput.click();
                    });

                    imageInput.addEventListener('change', (e) => {
                        const file = e.target.files[0];
                        if (file) {
                            selectedImage = file;
                            const reader = new FileReader();
                            reader.onload = (e) => {
                                previewImg.src = e.target.result;
                                imagePreview.classList.remove('hidden');
                            };
                            reader.readAsDataURL(file);
                        }
                    });

                    removeImageBtn.addEventListener('click', () => {
                        selectedImage = null;
                        imageInput.value = '';
                        imagePreview.classList.add('hidden');
                    });

                    // Load contacts function
                    window.loadContacts = async function () {
                        try {
                            const response = await fetch('/chat/partners');
                            const partners = await response.json();

                            allContacts = partners;
                            renderContacts(allContacts);
                        } catch (error) {
                            console.error('Error loading contacts:', error);
                            contactsList.innerHTML = '<div class="p-4 text-red-500 text-center">Error loading contacts</div>';
                        }
                    }

                    // Render contacts function
                    function renderContacts(contacts) {
                        if (contacts.length === 0) {
                            const userRole = '{{ auth()->user()->role ?? "" }}';
                            const isAdmin = userRole === 'Admin' || userRole === 'SuperAdmin';

                            contactsList.innerHTML = `
                                                        <div class="p-4 text-gray-500 text-center">
                                                            <p>No conversations yet</p>
                                                            <p class="text-sm mt-2">
                                                                ${isAdmin
                                    ? 'Users will appear here when they start conversations with you'
                                    : 'Start a conversation by sending an inquiry or clicking "Chat with Shop" on a shop page'
                                }
                                                            </p>
                                                        </div>
                                                    `;
                            return;
                        }

                        contactsList.innerHTML = contacts.map(contact => `
                                                    <div class="contact-item flex items-center p-3 cursor-pointer hover:bg-gray-100 transition-colors duration-150"
                                                         data-user-id="${contact.id}">
                                                        <div class="w-10 h-10 rounded-full mr-3 bg-purple-100 flex items-center justify-center">
                                                            <span class="text-purple-600 font-semibold">${contact.name.charAt(0).toUpperCase()}</span>
                                                        </div>
                                                        <div class="flex-grow">
                                                            <div class="flex items-center">
                                                                <p class="font-semibold text-gray-800">${contact.name}</p>
                                                                ${contact.role === 'Admin' || contact.role === 'SuperAdmin' ? '<span class="ml-2 px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded">Admin</span>' : ''}
                                                            </div>
                                                            <p class="text-sm text-gray-500 break-words overflow-hidden" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">${contact.latest_message || 'No messages yet'}</p>
                                                        </div>
                                                        <div class="text-right">
                                                            ${contact.unread_count > 0 ? `<span class="bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center mb-1">${contact.unread_count}</span>` : ''}
                                                            <span class="text-xs text-gray-400">${contact.latest_message_time ? formatTime(contact.latest_message_time) : ''}</span>
                                                        </div>
                                                    </div>
                                                `).join('');

                        // Add click listeners to contact items
                        document.querySelectorAll('.contact-item').forEach(item => {
                            item.addEventListener('click', () => selectContact(item));
                        });
                    }

                    // Filter contacts function
                    function filterContacts(searchTerm) {
                        const filteredContacts = allContacts.filter(contact =>
                            contact.name.toLowerCase().includes(searchTerm) ||
                            contact.email.toLowerCase().includes(searchTerm)
                        );
                        renderContacts(filteredContacts);
                    }

                    // Select contact function
                    window.selectContact = async function (contactElement) {
                        // Remove active class from all contacts
                        document.querySelectorAll('.contact-item').forEach(item => {
                            item.classList.remove('active-chat');
                        });

                        // Add active class to selected contact
                        contactElement.classList.add('active-chat');

                        const userId = contactElement.dataset.userId;
                        const userName = contactElement.querySelector('p.font-semibold').textContent;
                        const userRole = contactElement.querySelector('.bg-blue-100') ? 'Admin' : 'User';

                        currentChatUserId = parseInt(userId);
                        activeChatName.textContent = userName;
                        activeChatRole.textContent = userRole;
                        activeChatInitial.textContent = userName.charAt(0).toUpperCase();

                        // Switch to chat view on mobile
                        if (window.innerWidth <= 640) {
                            document.getElementById('contactsSection').classList.add('hidden');
                            document.getElementById('chatSection').classList.remove('hidden');
                            document.getElementById('backToContactsBtn').classList.remove('hidden');
                        }

                        // Enable message input
                        messageInput.disabled = false;
                        sendMessageBtn.disabled = false;
                        imageBtn.disabled = false;

                        // Load conversation
                        await loadConversation(userId);
                    }

                    // Load conversation function
                    async function loadConversation(userId) {
                        try {
                            chatMessagesContainer.innerHTML = '<div class="flex justify-center p-4"><div class="animate-spin rounded-full h-6 w-6 border-b-2 border-purple-600"></div></div>';

                            const response = await fetch(`/chat/conversation/${userId}`);
                            const data = await response.json();

                            chatMessagesContainer.innerHTML = '';

                            if (data.messages.length === 0) {
                                chatMessagesContainer.innerHTML = `
                                                            <div class="flex items-center justify-center h-full text-gray-500">
                                                                <div class="text-center">
                                                                    <p>No messages yet. Start the conversation!</p>
                                                                </div>
                                                            </div>
                                                        `;
                            } else {
                                data.messages.forEach(message => {
                                    appendMessage(message, message.sender_id === data.current_user_id);
                                });
                            }

                            scrollToBottom();
                        } catch (error) {
                            console.error('Error loading conversation:', error);
                            chatMessagesContainer.innerHTML = '<div class="p-4 text-red-500 text-center">Error loading messages</div>';
                        }
                    }

                    // Append message function
                    function appendMessage(message, isSent) {
                        const messageDiv = document.createElement('div');
                        messageDiv.className = `flex ${isSent ? 'justify-end' : 'justify-start'} mb-4`;

                        const time = new Date(message.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

                        let messageContent = '';

                        if (message.message_type === 'image' && message.image_path) {
                            const imageUrl = `/uploads/${message.image_path}`;
                            messageContent = `
                                                        <div class="mb-2">
                                                            <img src="${imageUrl}" alt="Shared image" class="max-w-64 max-h-64 rounded-lg cursor-pointer" onclick="window.open('${imageUrl}', '_blank')">
                                                        </div>
                                                    `;
                            if (message.message && message.message.trim()) {
                                messageContent += `<p class="whitespace-pre-wrap break-words">${escapeHtml(message.message)}</p>`;
                            }
                        } else if (message.message_type === 'inquiry' && message.image_path) {
                            const imageUrl = `/uploads/${message.image_path}`;
                            messageContent = `
                                                        <div class="mb-2">
                                                            <img src="${imageUrl}" alt="Product image" class="max-w-48 max-h-48 rounded-lg">
                                                        </div>
                                                        <p class="whitespace-pre-wrap break-words">${escapeHtml(message.message)}</p>
                                                    `;
                        } else {
                            messageContent = `<p class="whitespace-pre-wrap break-words">${escapeHtml(message.message)}</p>`;
                        }

                        messageDiv.innerHTML = `
                                                    <div class="${isSent ? 'bg-purple-500 text-white' : 'bg-gray-200 text-gray-800'} p-3 rounded-lg max-w-[75%] break-words overflow-wrap-anywhere inline-block w-fit">
                                                        ${messageContent}
                                                        <span class="block text-xs ${isSent ? 'text-purple-100' : 'text-gray-500'} mt-1 ${isSent ? 'text-right' : 'text-left'}">${time}</span>
                                                    </div>
                                                `;

                        chatMessagesContainer.appendChild(messageDiv);
                        scrollToBottom();
                    }

                    // Send message function
                    async function sendMessage() {
                        if (!currentChatUserId || (!messageInput.value.trim() && !selectedImage)) return;

                        const message = messageInput.value.trim();
                        messageInput.value = '';

                        try {
                            const formData = new FormData();
                            formData.append('receiver_id', currentChatUserId);

                            if (message) {
                                formData.append('message', message);
                            }

                            if (selectedImage) {
                                formData.append('image', selectedImage);
                                formData.append('message_type', 'image');
                            }

                            const response = await fetch('/chat/send', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                },
                                body: formData
                            });

                            const data = await response.json();

                            if (response.ok) {
                                appendMessage(data.message, true);
                                loadContacts(); // Refresh contacts to update latest message

                                // Clear image preview
                                if (selectedImage) {
                                    selectedImage = null;
                                    imageInput.value = '';
                                    imagePreview.classList.add('hidden');
                                }
                            } else {
                                console.error('Error sending message:', data);
                                if (typeof showToast === 'function') {
                                    showToast('Failed to send message. Please try again.', 'error');
                                } else {
                                    alert('Failed to send message. Please try again.');
                                }
                            }
                        } catch (error) {
                            console.error('Error sending message:', error);
                            if (typeof showToast === 'function') {
                                showToast('Failed to send message. Please try again.', 'error');
                            } else {
                                alert('Failed to send message. Please try again.');
                            }
                        }
                    }

                    // Update unread count function
                    async function updateUnreadCount() {
                        try {
                            const response = await fetch('/chat/unread-count');
                            const data = await response.json();

                            if (data.unread_count > 0) {
                                unreadBadge.textContent = data.unread_count;
                                unreadBadge.classList.remove('hidden');
                                unreadBadge.classList.add('flex');
                            } else {
                                unreadBadge.classList.add('hidden');
                                unreadBadge.classList.remove('flex');
                            }
                        } catch (error) {
                            console.error('Error updating unread count:', error);
                        }
                    }

                    // Utility functions
                    function scrollToBottom() {
                        chatMessagesContainer.scrollTop = chatMessagesContainer.scrollHeight;
                    }

                    function formatTime(timestamp) {
                        const date = new Date(timestamp);
                        const now = new Date();
                        const diffInHours = (now - date) / (1000 * 60 * 60);

                        if (diffInHours < 24) {
                            return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                        } else if (diffInHours < 48) {
                            return 'Yesterday';
                        } else {
                            return date.toLocaleDateString();
                        }
                    }

                    function escapeHtml(text) {
                        const div = document.createElement('div');
                        div.textContent = text;
                        return div.innerHTML;
                    }

                    // Initialize
                    updateUnreadCount();
                });
            </script>

            <style>
                /* Custom style for the active chat item in the contact list */
                .contact-item.active-chat {
                    background-color: #f3e8ff;
                    border-left: 4px solid #8b5cf6;
                }

                /* Ensure proper scrolling and text wrapping */
                #chatMessagesContainer {
                    scrollbar-width: thin;
                    scrollbar-color: #d1d5db #f9fafb;
                }

                #chatMessagesContainer::-webkit-scrollbar {
                    width: 6px;
                }

                #chatMessagesContainer::-webkit-scrollbar-track {
                    background: #f9fafb;
                    border-radius: 3px;
                }

                #chatMessagesContainer::-webkit-scrollbar-thumb {
                    background: #d1d5db;
                    border-radius: 3px;
                }

                #chatMessagesContainer::-webkit-scrollbar-thumb:hover {
                    background: #9ca3af;
                }

                /* Ensure message bubbles don't overflow and size properly */
                .message-bubble {
                    word-wrap: break-word;
                    word-break: break-word;
                    overflow-wrap: anywhere;
                    hyphens: auto;
                }

                /* Contact item styling */
                .contact-item {
                    min-height: 60px;
                }

                .contact-item .flex-grow {
                    min-width: 0;
                }

                /* Responsive layout */
                @media (max-width: 1024px) {
                    #chatWindow {
                        width: 90vw !important;
                        height: 80vh !important;
                        right: 5vw;
                        bottom: 5vh;
                    }
                }

                /* Mobile-specific styles */
                @media (max-width: 640px) {
                    #chatWindow {
                        width: 100vw !important;
                        height: 100vh !important;
                        bottom: 0;
                        right: 0;
                        border-radius: 0 !important;
                        max-width: 100vw !important;
                        max-height: 100vh !important;
                    }

                    #contactsSection {
                        width: 100% !important;
                    }

                    #chatSection {
                        width: 100% !important;
                    }

                    #backToContactsBtn {
                        display: inline-block !important;
                    }
                }

                /* Ensure proper transitions */
                #contactsSection,
                #chatSection {
                    transition: all 0.3s ease;
                }

                /* Back button styling */
                #backToContactsBtn {
                    background: none;
                    border: none;
                    cursor: pointer;
                    padding: 8px 12px;
                    border-radius: 6px;
                    transition: background-color 0.2s;
                }

                #backToContactsBtn:hover {
                    background-color: rgba(147, 51, 234, 0.1);
                }

                #chatWindow {
                    resize: both;
                    overflow: hidden;
                    min-width: 400px;
                    min-height: 400px;
                    max-width: 95vw;
                    max-height: 90vh;
                }

                .contact-item.active-chat {
                    background-color: #f3e8ff;
                    border-left: 4px solid #8b5cf6;
                }

                #chatMessagesContainer::-webkit-scrollbar {
                    width: 6px;
                }

                #chatMessagesContainer::-webkit-scrollbar-thumb {
                    background: #d1d5db;
                    border-radius: 3px;
                }

                #chatMessagesContainer::-webkit-scrollbar-thumb:hover {
                    background: #9ca3af;
                }

                /* Overlay styling */
                #chatOverlay {
                    backdrop-filter: blur(1px);
                }
            </style>
        </div>
    @endif
@endauth