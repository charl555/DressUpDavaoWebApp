<x-filament-panels::page>
    <div class="fi-section-content-ctn">

        <div id="chatRoot"
            style="display: flex; height: 600px; background: white; border-radius: 0.5rem; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); border: 1px solid #e5e7eb; overflow: hidden;">

            <!-- Contacts Sidebar -->
            <div id="contactsSidebar"
                style="width: 33.333333%; border-right: 1px solid #e5e7eb; overflow-y: auto; background: white;">
                <div style="padding: 1rem; background-color: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                    <x-filament::input.wrapper>
                        <x-filament::input type="text" id="searchContacts" placeholder="Search users..."
                            style="width: 100%;" />
                    </x-filament::input.wrapper>
                </div>

                <div id="contactsList" style="min-height: 0;">
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
            <div id="chatArea" style="flex-grow: 1; display: flex; flex-direction: column; min-width: 0;">
                <div id="chatHeader"
                    style="background-color: #f9fafb; border-bottom: 1px solid #e5e7eb; padding: 1rem; display: flex; align-items: center; gap: 0.75rem;">
                    <!-- Back button (mobile only) -->
                    <button id="backToContactsBtn" aria-label="Back to contacts"
                        style="display:none; border: none; background: none; padding: 0.25rem 0.5rem; cursor: pointer;">
                        <!-- simple chevron -->
                        <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                            </path>
                        </svg>
                    </button>

                    <div
                        style="width: 2.5rem; height: 2.5rem; border-radius: 50%; margin-right: 0.25rem; background-color: #d1d5db; display: flex; align-items: center; justify-content: center;">
                        <span style="color: #4b5563; font-weight: 600;" id="activeChatInitial">?</span>
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        <p style="font-weight: 600; font-size: 1.125rem; color: #1f2937; margin:0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"
                            id="activeChatName">Select a user to chat</p>
                        {{-- <p style="font-size: 0.875rem; color: #6b7280;" id="activeChatRole"></p> --}}
                    </div>
                </div>

                <!-- Chat messages container -->
                <div id="chatMessagesContainer" style="flex-grow: 1; padding: 1rem; overflow-y: auto; min-height: 0;">
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

                <div id="messageInputArea" style="border-top: 1px solid #e5e7eb; padding: 1rem;">
                    <div id="imagePreview" style="margin-bottom: 0.5rem; display: none;">
                        <div style="position: relative; display: inline-block;">
                            <img id="previewImg"
                                style="max-width: 8rem; max-height: 8rem; border-radius: 0.5rem; border: 1px solid #d1d5db;">
                            <button id="removeImageBtn"
                                style="position: absolute; top: -0.5rem; right: -0.5rem; background-color: #ef4444; color: white; border-radius: 50%; width: 1.5rem; height: 1.5rem; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; border: none; cursor: pointer;">×</button>
                        </div>
                    </div>

                    <div style="display: flex; align-items: center;">
                        <input type="file" id="imageInput" accept="image/*" style="display: none;">
                        <button id="imageBtn"
                            style="padding: 0.5rem; color: #6b7280; border: none; background: none; cursor: pointer; margin-right: 0.5rem;"
                            title="Upload Image">
                            <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                        </button>
                        <button id="bookingBtn"
                            style="padding: 0.5rem; color:#8E24AA; border: none; background: none; cursor: pointer; margin-right: 0.5rem;"
                            title="Book Reservation">
                            <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                        </button>

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
        </div>

    </div>

    {{-- Booking Modal --}}
    <div id="bookingModal"
        style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); z-index: 1000;">
        <div
            style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; border-radius: 0.5rem; padding: 1.5rem; width: 90%; max-width: 500px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 style="font-size: 1.25rem; font-weight: 600; margin: 0;">Book Reservation</h3>
                <button id="closeBookingModal"
                    style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: ##8E24AA;">&times;</button>
            </div>

            <form id="bookingForm">
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-weight: 500; margin-bottom: 0.5rem;">Customer:</label>
                    <input type="text" id="customerName" readonly
                        style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.25rem; background-color: #f9fafb;">
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-weight: 500; margin-bottom: 0.5rem;">Product:</label>
                    <select id="productSelect"
                        style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.25rem;">
                        <option value="">Select a product...</option>
                    </select>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-weight: 500; margin-bottom: 0.5rem;">Reservation Date:</label>
                    <input type="date" id="bookingDate"
                        style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.25rem;">
                </div>


                <div style="display: flex; justify-content: flex-end; gap: 0.5rem;">
                    <button type="button" id="cancelBooking"
                        style="padding: 0.5rem 1rem; border: 1px solid #d1d5db; background: white; border-radius: 0.25rem; cursor: pointer;">
                        Cancel </button>
                    <button type="submit" id="bookReservationBtn"
                        style="padding: 0.5rem 1rem; background-color: #7f23fe; color: white; border: none; border-radius: 0.25rem; cursor: pointer; position: relative; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                        <span class="btn-text">Book Reservation</span>
                        <span class="loading hidden"
                            style="display: inline-block; width: 1rem; height: 1rem; border: 2px solid white; border-top: 2px solid transparent; border-radius: 50%;"></span>
                    </button>
                </div>
            </form>
        </div>
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
            const contactsSidebar = document.getElementById('contactsSidebar');
            const contactsList = document.getElementById('contactsList');
            const chatArea = document.getElementById('chatArea');
            const activeChatName = document.getElementById('activeChatName');
            const activeChatInitial = document.getElementById('activeChatInitial');
            const chatMessagesContainer = document.getElementById('chatMessagesContainer');
            const messageInput = document.getElementById('messageInput');
            const sendMessageBtn = document.getElementById('sendMessageBtn');
            const searchContacts = document.getElementById('searchContacts');
            const imageInput = document.getElementById('imageInput');
            const imageBtn = document.getElementById('imageBtn');
            const imagePreview = document.getElementById('imagePreview');
            const previewImg = document.getElementById('previewImg');
            const removeImageBtn = document.getElementById('removeImageBtn');
            const bookingBtn = document.getElementById('bookingBtn');
            const bookingModal = document.getElementById('bookingModal');
            const closeBookingModal = document.getElementById('closeBookingModal');
            const cancelBooking = document.getElementById('cancelBooking');
            const bookingForm = document.getElementById('bookingForm');
            const customerName = document.getElementById('customerName');
            const productSelect = document.getElementById('productSelect');
            const bookingDate = document.getElementById('bookingDate');
            const backToContactsBtn = document.getElementById('backToContactsBtn');

            let currentChatUserId = null;
            let allContacts = [];
            let selectedImage = null;
            let currentUserRole = '{{ auth()->user()->role ?? "User" }}'; // Get current user role

            // --- Mobile responsive behavior ---
            function isMobile() {
                return window.innerWidth <= 768;
            }

            function showContactsOnMobile() {
                contactsSidebar.style.display = '';
                chatArea.style.display = 'none';
                backToContactsBtn.style.display = 'none';
            }

            function showChatOnMobile() {
                contactsSidebar.style.display = 'none';
                chatArea.style.display = 'flex';
                backToContactsBtn.style.display = '';
            }

            function resetLayoutForViewport() {
                if (isMobile()) {
                    // Default to contacts list on mobile
                    showContactsOnMobile();
                } else {
                    // Desktop: both visible
                    contactsSidebar.style.display = '';
                    chatArea.style.display = 'flex';
                    backToContactsBtn.style.display = 'none';
                }
            }

            // Listen for resize to adjust layout
            window.addEventListener('resize', () => {
                resetLayoutForViewport();
            });

            // Back button behavior
            backToContactsBtn.addEventListener('click', () => {
                showContactsOnMobile();
            });

            // Event listeners for message sending and keyboard
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
                        imagePreview.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                }
            });

            removeImageBtn.addEventListener('click', () => {
                selectedImage = null;
                imageInput.value = '';
                imagePreview.style.display = 'none';
            });

            // Booking functionality
            bookingBtn.addEventListener('click', () => {
                if (currentChatUserId) {
                    openBookingModal();
                } else {
                    window.dispatchEvent(new CustomEvent('filament-notify', {
                        detail: {
                            status: 'warning',
                            message: 'Select a user first to book a reservation.',
                        }
                    }));
                }
            });

            closeBookingModal.addEventListener('click', () => {
                bookingModal.style.display = 'none';
            });

            cancelBooking.addEventListener('click', () => {
                bookingModal.style.display = 'none';
            });

            bookingForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                await createBooking();
            });


            // Load contacts function
            async function loadContacts() {
                try {
                    const response = await fetch('/chat/partners');
                    const partners = await response.json();

                    allContacts = partners;
                    renderContacts(allContacts);

                } catch (error) {
                    console.error('Error loading contacts:', error);
                    contactsList.innerHTML = '<div style="padding: 1rem; color: #ef4444; text-align: center;">Error loading users</div>';
                }
            }

            // Render contacts function
            function renderContacts(contacts) {
                if (!contacts || contacts.length === 0) {
                    contactsList.innerHTML = '<div style="padding: 1rem; color: #6b7280; text-align: center;"><p>No conversations yet</p><p style="font-size: 0.875rem; margin-top: 0.5rem;">Users will appear here when they start conversations with you</p></div>';
                    return;
                }

                contactsList.innerHTML = contacts.map(contact => `
                                                                                                                                        <div class="contact-item" data-user-id="${contact.id}" style="display: flex; align-items: center; padding: 0.75rem; cursor: pointer; transition: background-color 0.15s ease-in-out; border-bottom: 1px solid #f3f4f6;">
                                                                                                                                            <div style="width: 2.5rem; height: 2.5rem; border-radius: 50%; margin-right: 0.75rem; background-color: #dbeafe; display: flex; align-items: center; justify-content: center;">
                                                                                                                                                <span style="color: #2563eb; font-weight: 600;">${contact.name.charAt(0).toUpperCase()}</span>
                                                                                                                                            </div>
                                                                                                                                            <div style="flex-grow: 1; min-width: 0;">
                                                                                                                                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                                                                                                                                    <p style="font-weight: 600; color: #1f2937; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${contact.name}</p>
                                                                                                                                                    ${contact.role === 'User' ? '<span style="margin-left: 0.5rem; padding: 0.25rem 0.5rem; background-color: #dcfce7; color: #166534; font-size: 0.75rem; border-radius: 0.25rem;">User</span>' : ''}
                                                                                                                                                </div>
                                                                                                                                                <p style="font-size: 0.875rem; color: #6b7280; margin: 0; word-wrap: break-word; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">${contact.latest_message || 'No messages yet'}</p>
                                                                                                                                            </div>
                                                                                                                                            <div style="text-align: right; margin-left: 0.5rem;">
                                                                                                                                                ${contact.unread_count > 0 ? `<span style="background-color: #ef4444; color: white; font-size: 0.75rem; border-radius: 50%; width: 1.25rem; height: 1.25rem; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 0.25rem;">${contact.unread_count}</span>` : ''}
                                                                                                                                                <div style="font-size: 0.75rem; color: #9ca3af;">${contact.latest_message_time ? formatTime(contact.latest_message_time) : ''}</div>
                                                                                                                                            </div>
                                                                                                                                        </div>
                                                                                                                                    `).join('');

                // Add click listeners to contact items
                document.querySelectorAll('.contact-item').forEach(item => {
                    item.addEventListener('click', async () => {
                        // On mobile, switch to chat pane
                        if (isMobile()) {
                            showChatOnMobile();
                        }
                        await selectContact(item);
                    });
                });
            }

            // Filter contacts function
            function filterContacts(searchTerm) {
                const filteredContacts = allContacts.filter(contact =>
                    (contact.name && contact.name.toLowerCase().includes(searchTerm)) ||
                    (contact.email && contact.email.toLowerCase().includes(searchTerm))
                );
                renderContacts(filteredContacts);
            }

            // Select contact function
            async function selectContact(contactElement) {
                // Remove active class from all contacts
                document.querySelectorAll('.contact-item').forEach(item => {
                    item.style.backgroundColor = '';
                    item.style.borderLeft = '';
                });

                // Add active class to selected contact
                contactElement.style.backgroundColor = '#eff6ff';
                contactElement.style.borderLeft = '4px solid #3b82f6';

                const userId = contactElement.dataset.userId;
                // Find the name in the contact element - it's the first <p> tag with font-weight: 600
                const nameElement = contactElement.querySelector('p[style*="font-weight: 600"]');
                const userName = nameElement ? nameElement.textContent : 'Unknown User';

                // Check if there's a "User" badge to determine role
                const userBadge = contactElement.querySelector('span[style*="background-color: #dcfce7"]');
                const userRole = userBadge ? 'User' : 'Admin';

                currentChatUserId = parseInt(userId);
                activeChatName.textContent = userName;
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
                    chatMessagesContainer.innerHTML = '<div style="display: flex; justify-content: center; padding: 1rem;"><div style="animation: spin 1s linear infinite; border-radius: 50%; width: 1.5rem; height: 1.5rem; border: 2px solid #3b82f6; border-top: 2px solid transparent;"></div></div>';

                    const response = await fetch(`/chat/conversation/${userId}`);
                    const data = await response.json();

                    chatMessagesContainer.innerHTML = '';

                    if (!data.messages || data.messages.length === 0) {
                        chatMessagesContainer.innerHTML = `
                                                                                                                                                <div style="display: flex; align-items: center; justify-content: center; height: 100%; color: #6b7280;">
                                                                                                                                                    <div style="text-align: center;">
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

            // Append message
            function appendMessage(message, isSent) {
                const messageDiv = document.createElement('div');
                messageDiv.className = `chat-message ${isSent ? 'sent' : 'received'}`;
                messageDiv.style.marginBottom = '0.75rem';
                messageDiv.style.display = 'flex';
                messageDiv.style.alignItems = 'flex-end';
                messageDiv.style.gap = '0.5rem';

                if (isSent) {
                    messageDiv.style.justifyContent = 'flex-end';
                } else {
                    messageDiv.style.justifyContent = 'flex-start';
                }

                const time = new Date(message.created_at).toLocaleTimeString([], {
                    hour: '2-digit',
                    minute: '2-digit'
                });

                // Create bubble container
                const bubbleContainer = document.createElement('div');
                bubbleContainer.className = `message-bubble ${isSent ? 'sent' : 'received'}`;
                const isImageMessage = message.message_type === 'image' && message.image_path;
                const isInquiryMessage = message.message_type === 'inquiry' && message.image_path;

                // Set padding based on message type
                if (isImageMessage || isInquiryMessage) {
                    bubbleContainer.style.padding = '0.75rem';
                } else {
                    bubbleContainer.style.padding = '0.75rem 1rem';
                }

                // Fixed width styling - remove fit-content and use proper constraints
                bubbleContainer.style.maxWidth = 'min(70%, 400px)';
                bubbleContainer.style.minWidth = 'auto';
                bubbleContainer.style.borderRadius = '1.125rem';
                bubbleContainer.style.wordWrap = 'break-word';
                bubbleContainer.style.wordBreak = 'break-word';
                bubbleContainer.style.overflowWrap = 'break-word';
                bubbleContainer.style.whiteSpace = 'pre-wrap';
                bubbleContainer.style.display = 'block'; // Changed from inline-block to block
                bubbleContainer.style.position = 'relative';
                bubbleContainer.style.transition = 'all 0.2s ease';

                if (isSent) {
                    bubbleContainer.style.backgroundColor = '#3b82f6';
                    bubbleContainer.style.color = 'white';
                    bubbleContainer.style.borderBottomRightRadius = '0.375rem';
                    bubbleContainer.style.marginLeft = 'auto';
                } else {
                    bubbleContainer.style.backgroundColor = '#f3f4f6';
                    bubbleContainer.style.color = '#1f2937';
                    bubbleContainer.style.borderBottomLeftRadius = '0.375rem';
                    bubbleContainer.style.marginRight = 'auto';
                }

                let messageContent = '';

                if (isImageMessage) {
                    const imageUrl = `/storage/${message.image_path}`;
                    messageContent = `
                    <div style="margin-bottom: 0.5rem; text-align: center;">
                        <img src="${imageUrl}" alt="Shared image" style="max-width: 100%; max-height: 16rem; border-radius: 0.5rem; cursor: pointer; display: inline-block;" onclick="window.open('${imageUrl}', '_blank')">
                    </div>
                `;
                    if (message.message && message.message.trim()) {
                        messageContent += `<p style="margin: 0.5rem 0 0 0; line-height: 1.4; text-align: left;">${escapeHtml(message.message)}</p>`;
                    }
                } else if (isInquiryMessage) {
                    const imageUrl = `/storage/${message.image_path}`;
                    const isAdmin = currentUserRole === 'Admin' || currentUserRole === 'SuperAdmin';
                    const showBookingButton = isAdmin && !isSent && message.metadata;

                    messageContent = `
                    <div style="margin-bottom: 0.5rem; text-align: center;">
                        <img src="${imageUrl}" alt="Product image" style="max-width: 100%; max-height: 12rem; border-radius: 0.5rem; display: inline-block;">
                    </div>
                    <div style="text-align: left;">
                        <p style="margin: 0.5rem 0 0 0; line-height: 1.4;">${escapeHtml(message.message)}</p>
                        ${showBookingButton ? `
                            <div style="margin-top: 0.75rem; text-align: center;">
                                <button onclick="openBookingModalFromInquiry(${JSON.stringify(message.metadata).replace(/"/g, '&quot;')})"
                                    style="padding: 0.5rem 1rem; background-color: #7f23fe; color: white; border: none; border-radius: 0.375rem; font-size: 0.875rem; cursor: pointer; display: inline-flex; align-items: center; gap: 0.375rem; transition: background-color 0.2s; white-space: nowrap;"
                                    onmouseover="this.style.backgroundColor='#6b1db8'"
                                    onmouseout="this.style.backgroundColor='#7f23fe'">
                                    <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    Create Booking
                                </button>
                            </div>
                        ` : ''}
                    </div>
                `;
                } else {
                    messageContent = `<p style="margin: 0; line-height: 1.4;">${escapeHtml(message.message)}</p>`;
                }

                // Add timestamp
                const timeSpan = document.createElement('span');
                timeSpan.className = `message-time ${isSent ? 'sent' : 'received'}`;
                timeSpan.textContent = time;
                timeSpan.style.fontSize = '0.6875rem';
                timeSpan.style.marginTop = '0.5rem';
                timeSpan.style.display = 'block';
                timeSpan.style.lineHeight = '1.2';
                timeSpan.style.opacity = '0.8';

                if (isSent) {
                    timeSpan.style.color = 'rgba(255, 255, 255, 0.8)';
                    timeSpan.style.textAlign = 'right';
                } else {
                    timeSpan.style.color = '#6b7280';
                    timeSpan.style.textAlign = 'left';
                }

                bubbleContainer.innerHTML = messageContent;
                bubbleContainer.appendChild(timeSpan);
                messageDiv.appendChild(bubbleContainer);

                chatMessagesContainer.appendChild(messageDiv);
                scrollToBottom();
            }

            // Send message function
            async function sendMessage() {
                if (!currentChatUserId || (!messageInput.value.trim() && !selectedImage)) {
                    return;
                }

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
                            imagePreview.style.display = 'none';
                        }
                    } else {
                        console.error('Error sending message:', data);
                        window.dispatchEvent(new CustomEvent('filament-notify', {
                            detail: {
                                status: 'danger',
                                message: 'Failed to send message. Please try again.',
                            }
                        }));
                    }
                } catch (error) {
                    console.error('Error sending message:', error);
                    window.dispatchEvent(new CustomEvent('filament-notify', {
                        detail: {
                            status: 'danger',
                            message: 'Failed to send message. Please try again.',
                        }
                    }));
                }
            }

            // Booking functions
            async function openBookingModal() {
                try {
                    const currentContact = allContacts.find(contact => contact.id === currentChatUserId);
                    customerName.value = currentContact ? currentContact.name : 'Unknown User';

                    const response = await fetch('/chat/available-products');
                    const products = await response.json();

                    productSelect.innerHTML = '<option value="">Select a product...</option>';
                    products.forEach(product => {
                        const option = document.createElement('option');
                        option.value = product.product_id;
                        option.textContent = `${product.name} (${product.type}) - $${product.rental_price}`;
                        productSelect.appendChild(option);
                    });

                    const tomorrow = new Date();
                    tomorrow.setDate(tomorrow.getDate() + 1);
                    bookingDate.min = tomorrow.toISOString().split('T')[0];

                    bookingModal.style.display = 'block';
                } catch (error) {
                    console.error('Error opening booking modal:', error);
                    window.dispatchEvent(new CustomEvent('filament-notify', {
                        detail: {
                            status: 'danger',
                            message: 'Failed to load booking form. Please try again.',
                        }
                    }));
                }
            }

            // New function to open booking modal from inquiry with pre-filled data
            async function openBookingModalFromInquiry(inquiryMetadata) {
                try {
                    const currentContact = allContacts.find(contact => contact.id === currentChatUserId);
                    customerName.value = currentContact ? currentContact.name : 'Unknown User';

                    const response = await fetch('/chat/available-products');
                    const products = await response.json();

                    productSelect.innerHTML = '<option value="">Select a product...</option>';
                    products.forEach(product => {
                        const option = document.createElement('option');
                        option.value = product.product_id;
                        option.textContent = `${product.name} (${product.type}) - $${product.rental_price}`;

                        // Pre-select the product from inquiry if it matches
                        if (inquiryMetadata.product_id && product.product_id == inquiryMetadata.product_id) {
                            option.selected = true;
                        }

                        productSelect.appendChild(option);
                    });

                    // Pre-fill the rental date from inquiry
                    if (inquiryMetadata.rental_date) {
                        bookingDate.value = inquiryMetadata.rental_date;
                    }

                    const tomorrow = new Date();
                    tomorrow.setDate(tomorrow.getDate() + 1);
                    bookingDate.min = tomorrow.toISOString().split('T')[0];

                    bookingModal.style.display = 'block';

                    // Show success notification about pre-filled data
                    window.dispatchEvent(new CustomEvent('filament-notify', {
                        detail: {
                            status: 'info',
                            message: `Booking form pre-filled with inquiry details for "${inquiryMetadata.product_name}".`,
                        }
                    }));
                } catch (error) {
                    console.error('Error opening booking modal from inquiry:', error);
                    window.dispatchEvent(new CustomEvent('filament-notify', {
                        detail: {
                            status: 'danger',
                            message: 'Failed to load booking form. Please try again.',
                        }
                    }));
                }
            }

            // Make the function globally available
            window.openBookingModalFromInquiry = openBookingModalFromInquiry;

            function setButtonLoading(button, loadingText = 'Loading...') {
                const btnText = button.querySelector('.btn-text');
                const spinner = button.querySelector('.loading');
                button.disabled = true;
                btnText.textContent = loadingText;
                spinner.classList.remove('hidden');

                return function stopLoading(newText = 'Book Reservation') {
                    spinner.classList.add('hidden');
                    btnText.textContent = newText;
                    button.disabled = false;
                };
            }

            bookingForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                await createBooking();
            });

            // Updated createBooking function with toast notifications
            async function createBooking() {
                const productId = productSelect.value;
                const selectedDate = bookingDate.value;
                const bookBtn = document.getElementById('bookReservationBtn');

                const stopLoading = setButtonLoading(bookBtn, 'Booking...');

                if (!productId || !selectedDate) {
                    // Use Filament v4 notification
                    window.dispatchEvent(new CustomEvent('filament-notify', {
                        detail: {
                            status: 'warning',
                            message: 'Please select a product and date.',
                        }
                    }));
                    stopLoading();
                    return;
                }

                try {
                    const response = await fetch('/chat/create-booking', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            user_id: currentChatUserId,
                            product_id: productId,
                            booking_date: selectedDate
                        })
                    });

                    const data = await response.json();

                    if (response.ok) {
                        // Success notification
                        window.dispatchEvent(new CustomEvent('filament-notify', {
                            detail: {
                                status: 'success',
                                message: 'Booking created successfully!',
                            }
                        }));

                        bookingModal.style.display = 'none';
                        productSelect.value = '';
                        bookingDate.value = '';
                        loadConversation(currentChatUserId);
                    } else {
                        // Error notification
                        window.dispatchEvent(new CustomEvent('filament-notify', {
                            detail: {
                                status: 'danger',
                                message: data.error || 'Failed to create booking. Please try again.',
                            }
                        }));
                    }
                } catch (error) {
                    console.error('Error creating booking:', error);
                    window.dispatchEvent(new CustomEvent('filament-notify', {
                        detail: {
                            status: 'danger',
                            message: 'An error occurred while creating booking. Please try again.',
                        }
                    }));
                } finally {
                    stopLoading();
                }
            }

            // Utilities
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

            // Initialize layout + data
            resetLayoutForViewport();
            loadContacts();
        });
    </script>
@endpush

@push('styles')
    <style>
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

        /* Improved scroll styling for messages */
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

        /* Fixed message bubble styling */
        .message-bubble {
            box-sizing: border-box;
        }

        .message-bubble.sent {
            align-self: flex-end;
        }

        .message-bubble.received {
            align-self: flex-start;
        }

        /* Ensure buttons don't stretch */
        .message-bubble button {
            width: auto !important;
            max-width: none !important;
        }

        /* Message bubble responsiveness */
        @media (max-width: 768px) {
            .message-bubble {
                max-width: 85% !important;
                padding: 0.625rem 0.875rem !important;
            }

            /* Force contacts and chat to take full width when visible */
            #chatRoot {
                display: flex;
                flex-direction: column;
                height: 100%;
            }

            #contactsSidebar {
                width: 100% !important;
                height: 100%;
                border-right: none;
                border-bottom: 1px solid #e5e7eb;
            }

            #chatArea {
                width: 100% !important;
                height: 100%;
                display: none;
                /* toggled by JS */
                flex-direction: column;
            }

            /* Tidy chat header in mobile */
            #chatHeader {
                padding: 0.75rem 0.75rem;
            }

            #messageInputArea {
                padding: 0.75rem;
            }
        }

        /* Hover effects */
        .message-bubble:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .hidden {
            display: none !important;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .loading {
            animation: spin 1s linear infinite;
        }

        /* Fix for chat message alignment */
        .chat-message {
            width: 100%;
        }

        /* Ensure proper text alignment in message bubbles */
        .message-bubble p {
            text-align: left;
            margin: 0;
        }

        /* Fix button styling */
        .message-bubble button {
            display: inline-flex !important;
            width: auto !important;
            white-space: nowrap;
        }
    </style>
@endpush