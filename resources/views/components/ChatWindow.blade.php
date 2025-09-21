@auth
    <div>
        <button id="openChatBtn"
            class="fixed bottom-6 right-6 bg-purple-600 text-white p-4 rounded-full shadow-lg
                                                                                   hover:bg-purple-700 transition-all duration-300 z-50 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">
            <span class="text-lg font-semibold">Chat</span>
            <span id="unreadBadge"
                class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-6 w-6 items-center justify-center hidden">0</span>
        </button>

        <div id="chatWindow"
            class="fixed bottom-6 right-6 w-[700px] h-96 bg-white rounded-lg shadow-xl border border-gray-200
                                                                                   flex flex-col transform translate-y-full opacity-0 invisible transition-all duration-300 ease-in-out z-40 max-h-96 min-h-96">

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

            <div class="flex flex-grow min-h-0">

                <div class="w-1/3 border-r border-gray-200 overflow-y-auto">
                    <div class="p-3 bg-gray-50 border-b border-gray-200">
                        <input type="text" id="searchContacts" placeholder="Search contacts..."
                            class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500">
                    </div>

                    <div id="contactsList">
                        <!-- Contacts will be loaded dynamically -->
                        <div class="flex items-center justify-center p-8 text-gray-500">
                            <div class="text-center">
                                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600 mx-auto mb-2">
                                </div>
                                <p>Loading contacts...</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex-grow flex flex-col min-h-0">
                    <div class="bg-gray-50 border-b border-gray-200 p-3 flex items-center" id="chatHeader">
                        <div class="w-10 h-10 rounded-full mr-3 bg-gray-300 flex items-center justify-center">
                            <span class="text-gray-600 font-semibold" id="activeChatInitial">?</span>
                        </div>
                        <div>
                            <p class="font-semibold text-lg text-gray-800" id="activeChatName">Select a contact</p>
                            <p class="text-sm text-gray-500" id="activeChatRole"></p>
                        </div>
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

                    <div class="border-t border-gray-200 p-3 flex items-center flex-shrink-0" id="messageInputArea">
                        <input type="text" id="messageInput" placeholder="Type a message..." disabled
                            class="flex-grow p-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring-1 focus:ring-purple-500 disabled:bg-gray-100">
                        <button id="sendMessageBtn" disabled
                            class="bg-purple-600 text-white p-2 rounded-r-md hover:bg-purple-700 transition-colors duration-300 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 disabled:bg-gray-400 disabled:cursor-not-allowed">
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

        <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
        <script>
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
                const openChatBtn = document.getElementById('openChatBtn');
                const chatWindow = document.getElementById('chatWindow');
                const closeChatBtn = document.getElementById('closeChatBtn');
                const contactsList = document.getElementById('contactsList');
                const activeChatName = document.getElementById('activeChatName');
                const activeChatRole = document.getElementById('activeChatRole');
                const activeChatInitial = document.getElementById('activeChatInitial');
                const chatMessagesContainer = document.getElementById('chatMessagesContainer');
                const messageInput = document.getElementById('messageInput');
                const sendMessageBtn = document.getElementById('sendMessageBtn');
                const searchContacts = document.getElementById('searchContacts');
                const unreadBadge = document.getElementById('unreadBadge');

                let currentChatUserId = null;
                let allContacts = [];

                // Chat window toggle functions
                const showChatButton = () => {
                    openChatBtn.classList.remove('opacity-0', 'invisible');
                    openChatBtn.classList.add('opacity-100', 'visible');
                };

                const hideChatButton = () => {
                    openChatBtn.classList.remove('opacity-100', 'visible');
                    openChatBtn.classList.add('opacity-0', 'invisible');
                };

                // Event listeners
                openChatBtn.addEventListener('click', () => {
                    chatWindow.classList.remove('translate-y-full', 'opacity-0', 'invisible');
                    chatWindow.classList.add('translate-y-0', 'opacity-100', 'visible');
                    hideChatButton();
                    loadContacts();
                    updateUnreadCount();
                });

                closeChatBtn.addEventListener('click', () => {
                    chatWindow.classList.remove('translate-y-0', 'opacity-100', 'visible');
                    chatWindow.classList.add('translate-y-full', 'opacity-0', 'invisible');
                    showChatButton();
                });

                // Send message functionality
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

                // Load contacts function
                async function loadContacts() {
                    try {
                        const userRole = '{{ auth()->user()->role ?? "" }}';
                        let response;

                        if (userRole === 'Admin' || userRole === 'SuperAdmin') {
                            // For admins, load conversation partners and all users
                            const [partnersResponse, usersResponse] = await Promise.all([
                                fetch('/chat/partners'),
                                fetch('/chat/users')
                            ]);
                            const partners = await partnersResponse.json();
                            const users = await usersResponse.json();

                            // Merge and deduplicate
                            const partnerIds = partners.map(p => p.id);
                            const additionalUsers = users.filter(u => !partnerIds.includes(u.id));
                            allContacts = [...partners, ...additionalUsers.map(u => ({ ...u, latest_message: null, unread_count: 0 }))];
                        } else {
                            // For regular users, load conversation partners and admins
                            const [partnersResponse, adminsResponse] = await Promise.all([
                                fetch('/chat/partners'),
                                fetch('/chat/admins')
                            ]);
                            const partners = await partnersResponse.json();
                            const admins = await adminsResponse.json();

                            // Merge and deduplicate
                            const partnerIds = partners.map(p => p.id);
                            const additionalAdmins = admins.filter(a => !partnerIds.includes(a.id));
                            allContacts = [...partners, ...additionalAdmins.map(a => ({ ...a, latest_message: null, unread_count: 0 }))];
                        }

                        renderContacts(allContacts);
                    } catch (error) {
                        console.error('Error loading contacts:', error);
                        contactsList.innerHTML = '<div class="p-4 text-red-500 text-center">Error loading contacts</div>';
                    }
                }

                // Render contacts function
                function renderContacts(contacts) {
                    if (contacts.length === 0) {
                        contactsList.innerHTML = `
                                                                                            <div class="p-4 text-gray-500 text-center">
                                                                                                <p>No contacts available</p>
                                                                                                ${!'{{ auth()->user()->role ?? "" }}'.includes('Admin') ? '<p class="text-sm mt-2">Start a conversation with an admin!</p>' : ''}
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
                async function selectContact(contactElement) {
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

                    // Enable message input
                    messageInput.disabled = false;
                    sendMessageBtn.disabled = false;

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

                    messageDiv.innerHTML = `
                                                                                        <div class="${isSent ? 'bg-purple-500 text-white' : 'bg-gray-200 text-gray-800'} p-3 rounded-lg max-w-[75%] break-words overflow-wrap-anywhere inline-block w-fit">
                                                                                            <p class="whitespace-pre-wrap break-words">${escapeHtml(message.message)}</p>
                                                                                            <span class="block text-xs ${isSent ? 'text-purple-100' : 'text-gray-500'} mt-1 ${isSent ? 'text-right' : 'text-left'}">${time}</span>
                                                                                        </div>
                                                                                    `;

                    chatMessagesContainer.appendChild(messageDiv);
                    scrollToBottom();
                }

                // Send message function
                async function sendMessage() {
                    if (!currentChatUserId || !messageInput.value.trim()) return;

                    const message = messageInput.value.trim();
                    messageInput.value = '';

                    try {
                        const response = await fetch('/chat/send', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                receiver_id: currentChatUserId,
                                message: message
                            })
                        });

                        const data = await response.json();

                        if (response.ok) {
                            appendMessage(data.message, true);
                            loadContacts(); // Refresh contacts to update latest message
                        } else {
                            console.error('Error sending message:', data);
                            alert('Failed to send message. Please try again.');
                        }
                    } catch (error) {
                        console.error('Error sending message:', error);
                        alert('Failed to send message. Please try again.');
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
                /* purple-50 */
                border-left: 4px solid #8b5cf6;
                /* purple-500 */
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
        </style>
    </div>
@endauth