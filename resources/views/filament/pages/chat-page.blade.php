{{-- <x-filament-panels::page>
    <div class="fi-section-content-ctn">
        <x-filament::section>
            <div
                style="display: flex; height: 600px; background: white; border-radius: 0.5rem; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); border: 1px solid #e5e7eb;">
                <!-- Contacts Sidebar -->
                <div style="width: 33.333333%; border-right: 1px solid #e5e7eb; overflow-y: auto;">
                    <div style="padding: 1rem; background-color: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                        <x-filament::input.wrapper>
                            <x-filament::input type="text" id="searchContacts" placeholder="Search users..."
                                style="width: 100%;" />
                        </x-filament::input.wrapper>
                    </div>

                    <div id="contactsList">
                        <!-- Contacts will be loaded dynamically -->
                        <div
                            style="display: flex; align-items: center; justify-content: center; padding: 2rem; color: #6b7280;">
                            <div style="text-align: center;">
                                <div
                                    style="width: 2rem; height: 2rem; border: 2px solid #3b82f6; border-top: 2px solid transparent; border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto 0.5rem;">
                                </div>
                                <p>Loading users...</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Chat Area -->
                <div style="flex-grow: 1; display: flex; flex-direction: column;">
                    <div style="background-color: #f9fafb; border-bottom: 1px solid #e5e7eb; padding: 1rem; display: flex; align-items: center;"
                        id="chatHeader">
                        <div
                            style="width: 2.5rem; height: 2.5rem; border-radius: 50%; margin-right: 0.75rem; background-color: #d1d5db; display: flex; align-items: center; justify-content: center;">
                            <span style="color: #4b5563; font-weight: 600;" id="activeChatInitial">?</span>
                        </div>
                        <div>
                            <p style="font-weight: 600; font-size: 1.125rem; color: #1f2937;" id="activeChatName">Select
                                a user to chat</p>
                            <p style="font-size: 0.875rem; color: #6b7280;" id="activeChatRole"></p>
                        </div>
                    </div>

                    <div style="flex-grow: 1; padding: 1rem; overflow-y: auto; min-height: 0; max-height: 400px;"
                        id="chatMessagesContainer">
                        <div
                            style="display: flex; align-items: center; justify-content: center; height: 100%; color: #6b7280;">
                            <div style="text-align: center;">
                                <svg style="width: 4rem; height: 4rem; margin: 0 auto 1rem; color: #d1d5db;" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                                    </path>
                                </svg>
                                <p>Select a user to start chatting</p>
                            </div>
                        </div>
                    </div>

                    <div style="border-top: 1px solid #e5e7eb; padding: 1rem; display: flex; align-items: center;"
                        id="messageInputArea">
                        <x-filament::input.wrapper style="flex-grow: 1; margin-right: 0.5rem;">
                            <x-filament::input type="text" id="messageInput" placeholder="Type a message..." disabled
                                style="width: 100%;" />
                        </x-filament::input.wrapper>
                        <x-filament::button id="sendMessageBtn" disabled color="primary" style="padding: 0.5rem;">
                            <svg style="width: 1.5rem; height: 1.5rem;" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </x-filament::button>
                    </div>
                </div>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>

@push('scripts')
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

            // Subscribe to admin's private channel
            const channel = pusher.subscribe('private-chat.{{ auth()->id() }}');

            // Listen for new messages
            channel.bind('message.sent', function (data) {
                if (currentChatUserId === data.sender_id) {
                    appendMessage(data, false);
                }
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

        let currentChatUserId = null;
        let allContacts = [];

        // Event listeners
        sendMessageBtn.addEventListener('click', sendMessage);
        messageInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });

        searchContacts.addEventListener('input', (e) => {
            const searchTerm = e.target.value.toLowerCase();
            filterContacts(searchTerm);
        });

        // Load contacts function
        async function loadContacts() {
            try {
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

                renderContacts(allContacts);
            } catch (error) {
                console.error('Error loading contacts:', error);
                contactsList.innerHTML = '<div style="padding: 1rem; color: #ef4444; text-align: center;">Error loading users</div>';
            }
        }

        // Render contacts function
        function renderContacts(contacts) {
            if (contacts.length === 0) {
                contactsList.innerHTML = '<div style="padding: 1rem; color: #6b7280; text-align: center;">No users available</div>';
                return;
            }

            contactsList.innerHTML = contacts.map(contact => `
                                                                                <div class="contact-item" data-user-id="${contact.id}" style="display: flex; align-items: center;">
                                                                                    <div style="width: 2.5rem; height: 2.5rem; border-radius: 50%; margin-right: 0.75rem; background-color: #dbeafe; display: flex; align-items: center; justify-content: center;">
                                                                                        <span style="color: #2563eb; font-weight: 600;">${contact.name.charAt(0).toUpperCase()}</span>
                                                                                    </div>
                                                                                    <div style="flex-grow: 1;">
                                                                                        <div style="display: flex; align-items: center;">
                                                                                            <p style="font-weight: 600; color: #1f2937; margin: 0;">${contact.name}</p>
                                                                                            ${contact.role === 'User' ? '<span style="margin-left: 0.5rem; padding: 0.25rem 0.5rem; background-color: #dcfce7; color: #166534; font-size: 0.75rem; border-radius: 0.25rem;">User</span>' : ''}
                                                                                        </div>
                                                                                        <p style="font-size: 0.875rem; color: #6b7280; margin: 0; word-wrap: break-word; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">${contact.latest_message || 'No messages yet'}</p>
                                                                                    </div>
                                                                                    <div style="text-align: right;">
                                                                                        ${contact.unread_count > 0 ? `<span style="background-color: #ef4444; color: white; font-size: 0.75rem; border-radius: 50%; width: 1.25rem; height: 1.25rem; display: flex; align-items: center; justify-content: center; margin-bottom: 0.25rem;">${contact.unread_count}</span>` : ''}
                                                                                        <span style="font-size: 0.75rem; color: #9ca3af;">${contact.latest_message_time ? formatTime(contact.latest_message_time) : ''}</span>
                                                                                    </div>
                                                                                </div>
                                                                                                        `).join('');

            // Add click listeners to contact items
            document.querySelectorAll('.contact-item').forEach(item => {
                item.addEventListener('click', () => {
                    console.log('Contact clicked:', item.dataset.userId);
                    selectContact(item);
                });
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
            // Find the name in the contact element - it's the first <p> tag with font-weight: 600
            const nameElement = contactElement.querySelector('p[style*="font-weight: 600"]');
            const userName = nameElement ? nameElement.textContent : 'Unknown User';

            // Check if there's a "User" badge to determine role
            const userBadge = contactElement.querySelector('span[style*="background-color: #dcfce7"]');
            const userRole = userBadge ? 'User' : 'Admin';

            currentChatUserId = parseInt(userId);
            activeChatName.textContent = userName;
            activeChatRole.textContent = userRole;
            activeChatInitial.textContent = userName.charAt(0).toUpperCase();

            // Enable message input
            messageInput.disabled = false;
            sendMessageBtn.disabled = false;
            sendMessageBtn.removeAttribute('disabled');

            console.log('Contact selected:', userName, 'ID:', userId);

            // Load conversation
            await loadConversation(userId);
        }

        // Load conversation function
        async function loadConversation(userId) {
            try {
                chatMessagesContainer.innerHTML = '<div class="flex justify-center p-4"><div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div></div>';

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
                chatMessagesContainer.innerHTML = '<div style="padding: 1rem; color: #ef4444; text-align: center;">Error loading messages</div>';
            }
        }

        // Append message function
        function appendMessage(message, isSent) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `chat-message ${isSent ? 'sent' : 'received'}`;

            const time = new Date(message.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

            messageDiv.innerHTML = `
                                                                                    <div class="message-bubble ${isSent ? 'sent' : 'received'}">
                                                                                        <p style="margin: 0;">${escapeHtml(message.message)}</p>
                                                                                        <span class="message-time ${isSent ? 'sent' : 'received'}">${time}</span>
                                                                                    </div>
                                                                                `;

            chatMessagesContainer.appendChild(messageDiv);
            scrollToBottom();
        }

        // Send message function
        async function sendMessage() {
            console.log('Send message called. Current chat user ID:', currentChatUserId);
            console.log('Message input value:', messageInput.value);

            if (!currentChatUserId || !messageInput.value.trim()) {
                console.log('Cannot send message: missing user ID or empty message');
                return;
            }

            const message = messageInput.value.trim();
            messageInput.value = '';

            console.log('Sending message:', message, 'to user:', currentChatUserId);

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
        loadContacts();
    });
</script>
@endpush

@push('styles')
<style>
    .contact-item.active-chat {
        background-color: #eff6ff !important;
        border-left: 4px solid #3b82f6 !important;
    }

    .contact-item {
        padding: 0.75rem;
        cursor: pointer;
        transition: background-color 0.15s ease-in-out;
        border-bottom: 1px solid #f3f4f6;
        min-height: 60px;
    }

    .contact-item:hover {
        background-color: #f9fafb;
    }

    @keyframes spin {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }

    .chat-message {
        margin-bottom: 1rem;
    }

    .chat-message.sent {
        display: flex;
        justify-content: flex-end;
    }

    .chat-message.received {
        display: flex;
        justify-content: flex-start;
    }

    .message-bubble {
        max-width: 75%;
        width: fit-content;
        padding: 0.75rem;
        border-radius: 0.5rem;
        word-wrap: break-word;
        word-break: break-word;
        overflow-wrap: anywhere;
        white-space: pre-wrap;
        display: inline-block;
    }

    .message-bubble.sent {
        background-color: #3b82f6;
        color: white;
    }

    .message-bubble.received {
        background-color: #f3f4f6;
        color: #1f2937;
    }

    .message-time {
        font-size: 0.75rem;
        margin-top: 0.25rem;
        display: block;
    }

    .message-time.sent {
        color: rgba(255, 255, 255, 0.7);
        text-align: right;
    }

    .message-time.received {
        color: #6b7280;
        text-align: left;
    }

    /* Scrollbar styling for better UX */
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
</style>
@endpush --}}

<x-filament-panels::page>
    {{-- <div class="fi-section-content-ctn">
        <x-filament::section> --}}
            <div
                style="display: flex; height: 600px; background: white; border-radius: 0.5rem; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); border: 1px solid #e5e7eb;">
                <!-- Contacts Sidebar -->
                <div style="width: 33.333333%; border-right: 1px solid #e5e7eb; overflow-y: auto;">
                    <div style="padding: 1rem; background-color: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                        <x-filament::input.wrapper>
                            <x-filament::input type="text" id="searchContacts" placeholder="Search users..."
                                style="width: 100%;" />
                        </x-filament::input.wrapper>
                    </div>

                    <div id="contactsList">
                        <!-- Contacts will be loaded dynamically -->
                        <div
                            style="display: flex; align-items: center; justify-content: center; padding: 2rem; color: #6b7280;">
                            <div style="text-align: center;">
                                <div
                                    style="width: 2rem; height: 2rem; border: 2px solid #3b82f6; border-top: 2px solid transparent; border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto 0.5rem;">
                                </div>
                                <p>Loading users...</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Chat Area -->
                <div style="flex-grow: 1; display: flex; flex-direction: column;">
                    <div style="background-color: #f9fafb; border-bottom: 1px solid #e5e7eb; padding: 1rem; display: flex; align-items: center;"
                        id="chatHeader">
                        <div
                            style="width: 2.5rem; height: 2.5rem; border-radius: 50%; margin-right: 0.75rem; background-color: #d1d5db; display: flex; align-items: center; justify-content: center;">
                            <span style="color: #4b5563; font-weight: 600;" id="activeChatInitial">?</span>
                        </div>
                        <div>
                            <p style="font-weight: 600; font-size: 1.125rem; color: #1f2937;" id="activeChatName">Select
                                a user to chat</p>
                            <p style="font-size: 0.875rem; color: #6b7280;" id="activeChatRole"></p>
                        </div>
                    </div>

                    <div style="flex-grow: 1; padding: 1rem; overflow-y: auto; min-height: 0; max-height: 400px;"
                        id="chatMessagesContainer">
                        <div
                            style="display: flex; align-items: center; justify-content: center; height: 100%; color: #6b7280;">
                            <div style="text-align: center;">
                                <svg style="width: 4rem; height: 4rem; margin: 0 auto 1rem; color: #d1d5db;" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                                    </path>
                                </svg>
                                <p>Select a user to start chatting</p>
                            </div>
                        </div>
                    </div>

                    <div style="border-top: 1px solid #e5e7eb; padding: 1rem; display: flex; align-items: center;"
                        id="messageInputArea">
                        <x-filament::input.wrapper style="flex-grow: 1; margin-right: 0.5rem;">
                            <x-filament::input type="text" id="messageInput" placeholder="Type a message..." disabled
                                style="width: 100%;" />
                        </x-filament::input.wrapper>
                        <x-filament::button id="sendMessageBtn" disabled color="primary" style="padding: 0.5rem;">
                            <svg style="width: 1.5rem; height: 1.5rem;" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </x-filament::button>
                    </div>
                </div>
            </div>
            {{--
        </x-filament::section>
    </div> --}}
</x-filament-panels::page>

@push('scripts')
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

                // Subscribe to admin's private channel
                const channel = pusher.subscribe('private-chat.{{ auth()->id() }}');

                // Listen for new messages
                channel.bind('message.sent', function (data) {
                    if (currentChatUserId === data.sender_id) {
                        appendMessage(data, false);
                    }
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

            let currentChatUserId = null;
            let allContacts = [];

            // Event listeners
            sendMessageBtn.addEventListener('click', sendMessage);
            messageInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    sendMessage();
                }
            });

            searchContacts.addEventListener('input', (e) => {
                const searchTerm = e.target.value.toLowerCase();
                filterContacts(searchTerm);
            });

            // Load contacts function
            async function loadContacts() {
                try {
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

                    renderContacts(allContacts);
                } catch (error) {
                    console.error('Error loading contacts:', error);
                    contactsList.innerHTML = '<div style="padding: 1rem; color: #ef4444; text-align: center;">Error loading users</div>';
                }
            }

            // Render contacts function
            function renderContacts(contacts) {
                if (contacts.length === 0) {
                    contactsList.innerHTML = '<div style="padding: 1rem; color: #6b7280; text-align: center;">No users available</div>';
                    return;
                }

                contactsList.innerHTML = contacts.map(contact => `
                                <div class="contact-item" data-user-id="${contact.id}" style="display: flex; align-items: center;">
                                    <div style="width: 2.5rem; height: 2.5rem; border-radius: 50%; margin-right: 0.75rem; background-color: #dbeafe; display: flex; align-items: center; justify-content: center;">
                                        <span style="color: #2563eb; font-weight: 600;">${contact.name.charAt(0).toUpperCase()}</span>
                                    </div>
                                    <div style="flex-grow: 1;">
                                        <div style="display: flex; align-items: center;">
                                            <p style="font-weight: 600; color: #1f2937; margin: 0;">${contact.name}</p>
                                            ${contact.role === 'User' ? '<span style="margin-left: 0.5rem; padding: 0.25rem 0.5rem; background-color: #dcfce7; color: #166534; font-size: 0.75rem; border-radius: 0.25rem;">User</span>' : ''}
                                        </div>
                                        <p style="font-size: 0.875rem; color: #6b7280; margin: 0; word-wrap: break-word; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">${contact.latest_message || 'No messages yet'}</p>
                                    </div>
                                    <div style="text-align: right;">
                                        ${contact.unread_count > 0 ? `<span style="background-color: #ef4444; color: white; font-size: 0.75rem; border-radius: 50%; width: 1.25rem; height: 1.25rem; display: flex; align-items: center; justify-content: center; margin-bottom: 0.25rem;">${contact.unread_count}</span>` : ''}
                                        <span style="font-size: 0.75rem; color: #9ca3af;">${contact.latest_message_time ? formatTime(contact.latest_message_time) : ''}</span>
                                    </div>
                                </div>
                            `).join('');

                // Add click listeners to contact items
                document.querySelectorAll('.contact-item').forEach(item => {
                    item.addEventListener('click', () => {
                        console.log('Contact clicked:', item.dataset.userId);
                        selectContact(item);
                    });
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
                // Find the name in the contact element - it's the first <p> tag with font-weight: 600
                const nameElement = contactElement.querySelector('p[style*="font-weight: 600"]');
                const userName = nameElement ? nameElement.textContent : 'Unknown User';

                // Check if there's a "User" badge to determine role
                const userBadge = contactElement.querySelector('span[style*="background-color: #dcfce7"]');
                const userRole = userBadge ? 'User' : 'Admin';

                currentChatUserId = parseInt(userId);
                activeChatName.textContent = userName;
                activeChatRole.textContent = userRole;
                activeChatInitial.textContent = userName.charAt(0).toUpperCase();

                // Enable message input
                messageInput.disabled = false;
                sendMessageBtn.disabled = false;
                sendMessageBtn.removeAttribute('disabled');

                console.log('Contact selected:', userName, 'ID:', userId);

                // Load conversation
                await loadConversation(userId);
            }

            // Load conversation function
            async function loadConversation(userId) {
                try {
                    chatMessagesContainer.innerHTML = '<div class="flex justify-center p-4"><div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div></div>';

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
                    chatMessagesContainer.innerHTML = '<div style="padding: 1rem; color: #ef4444; text-align: center;">Error loading messages</div>';
                }
            }

            // IMPROVED: Fixed appendMessage function with proper bubble sizing
            function appendMessage(message, isSent) {
                const messageDiv = document.createElement('div');
                messageDiv.className = `chat-message ${isSent ? 'sent' : 'received'}`;

                const time = new Date(message.created_at).toLocaleTimeString([], {
                    hour: '2-digit',
                    minute: '2-digit'
                });

                // Create bubble container
                const bubbleContainer = document.createElement('div');
                bubbleContainer.className = `message-bubble ${isSent ? 'sent' : 'received'}`;

                // Add message content
                const messageContent = document.createElement('p');
                messageContent.style.margin = '0';
                messageContent.style.lineHeight = '1.4';
                messageContent.textContent = message.message;

                // Add timestamp
                const timeSpan = document.createElement('span');
                timeSpan.className = `message-time ${isSent ? 'sent' : 'received'}`;
                timeSpan.textContent = time;

                // Append elements
                bubbleContainer.appendChild(messageContent);
                bubbleContainer.appendChild(timeSpan);
                messageDiv.appendChild(bubbleContainer);

                chatMessagesContainer.appendChild(messageDiv);
                scrollToBottom();
            }

            // Send message function
            async function sendMessage() {
                console.log('Send message called. Current chat user ID:', currentChatUserId);
                console.log('Message input value:', messageInput.value);

                if (!currentChatUserId || !messageInput.value.trim()) {
                    console.log('Cannot send message: missing user ID or empty message');
                    return;
                }

                const message = messageInput.value.trim();
                messageInput.value = '';

                console.log('Sending message:', message, 'to user:', currentChatUserId);

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
            loadContacts();
        });
    </script>
@endpush

@push('styles')
    <style>
        /* IMPROVED: Fixed Chat Bubble Styles */
        .contact-item.active-chat {
            background-color: #eff6ff !important;
            border-left: 4px solid #3b82f6 !important;
        }

        .contact-item {
            padding: 0.75rem;
            cursor: pointer;
            transition: background-color 0.15s ease-in-out;
            border-bottom: 1px solid #f3f4f6;
            min-height: 60px;
        }

        .contact-item:hover {
            background-color: #f9fafb;
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        /* IMPROVED: Fixed Chat Message Container */
        .chat-message {
            margin-bottom: 1rem;
            display: flex;
            align-items: flex-end;
            gap: 0.5rem;
        }

        .chat-message.sent {
            justify-content: flex-end;
        }

        .chat-message.received {
            justify-content: flex-start;
        }

        /* IMPROVED: Fixed Message Bubble - Key Changes Here */
        .message-bubble {
            /* FIXED: Remove fixed width constraints that cause issues */
            max-width: min(75%, 400px);
            /* Cap at 400px for very long messages */
            min-width: fit-content;
            /* Allow natural shrinking */
            width: auto;
            /* Let content determine width naturally */

            padding: 0.75rem 1rem;
            border-radius: 1rem;

            /* Text handling */
            word-wrap: break-word;
            word-break: break-word;
            overflow-wrap: break-word;
            white-space: pre-wrap;

            /* FIXED: Remove problematic display property */
            position: relative;

            /* Smooth transitions */
            transition: all 0.2s ease;
        }

        .message-bubble.sent {
            background-color: #3b82f6;
            color: white;
            border-bottom-right-radius: 0.25rem;
            /* Tail effect */
        }

        .message-bubble.received {
            background-color: #f3f4f6;
            color: #1f2937;
            border-bottom-left-radius: 0.25rem;
            /* Tail effect */
        }

        /* IMPROVED: Better time positioning */
        .message-time {
            font-size: 0.75rem;
            margin-top: 0.25rem;
            display: block;
            line-height: 1.2;
        }

        .message-time.sent {
            color: rgba(255, 255, 255, 0.7);
            text-align: right;
        }

        .message-time.received {
            color: #6b7280;
            text-align: left;
        }

        /* IMPROVED: Scrollbar styling for better UX */
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

        /* IMPROVED: Additional responsive improvements */
        @media (max-width: 768px) {
            .message-bubble {
                max-width: 85%;
                /* Slightly larger on mobile */
                padding: 0.625rem 0.875rem;
                /* Slightly smaller padding on mobile */
            }
        }

        /* IMPROVED: Message bubble hover effects for better UX */
        .message-bubble:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .message-bubble.sent:hover {
            background-color: #2563eb;
        }

        .message-bubble.received:hover {
            background-color: #e5e7eb;
        }

        /* IMPROVED: Loading animation improvements */
        .animate-spin {
            animation: spin 1s linear infinite;
        }

        /* IMPROVED: Flex utilities for loading states */
        .flex {
            display: flex;
        }

        .justify-center {
            justify-content: center;
        }

        .items-center {
            align-items: center;
        }

        .p-4 {
            padding: 1rem;
        }

        .h-6 {
            height: 1.5rem;
        }

        .w-6 {
            width: 1.5rem;
        }

        .border-b-2 {
            border-bottom-width: 2px;
        }

        .border-blue-600 {
            border-color: #2563eb;
        }

        .rounded-full {
            border-radius: 9999px;
        }

        .text-gray-500 {
            color: #6b7280;
        }

        .text-center {
            text-align: center;
        }

        .h-full {
            height: 100%;
        }
    </style>
@endpush