/**
 * Real-time Message Handler using Pusher directly
 * Handles incoming messages via Pusher WebSockets
 */

(function() {
    'use strict';

    // Wait for Pusher to be initialized
    function initRealtimeMessages() {
        // Check if Pusher is available
        if (typeof window.Pusher === 'undefined') {
            console.warn('Pusher is not initialized. Retrying in 500ms...');
            setTimeout(initRealtimeMessages, 500);
            return;
        }

        // Get current user ID and thread ID from the page
        const currentUserId = window.currentUserId || null;
        const currentThreadId = window.currentThreadId || null;
        const currentUserRole = window.currentUserRole || null;
        const pusherKey = window.pusherKey || '1ffd600355cbf525961b';
        const pusherCluster = window.pusherCluster || 'us2';

        if (!currentUserId || !currentThreadId) {
            console.warn('Current user ID or thread ID not found. Real-time messaging may not work.');
            return;
        }

        // Initialize Pusher connection with Laravel authentication
        const pusher = new Pusher(pusherKey, {
            cluster: pusherCluster,
            encrypted: true,
            authEndpoint: '/broadcasting/auth',
            auth: {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                    'Accept': 'application/json'
                }
            }
        });

        // Format date for display
        function formatDate(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
            const messageDate = new Date(date.getFullYear(), date.getMonth(), date.getDate());

            if (messageDate.getTime() === today.getTime()) {
                // Today - show time only
                return date.toLocaleTimeString('en-US', { 
                    hour: 'numeric', 
                    minute: '2-digit',
                    hour12: true 
                });
            } else {
                // Other days - show date and time
                return date.toLocaleDateString('en-US', { 
                    month: 'short', 
                    day: 'numeric', 
                    year: 'numeric' 
                }) + ' ' + date.toLocaleTimeString('en-US', { 
                    hour: 'numeric', 
                    minute: '2-digit',
                    hour12: true 
                });
            }
        }

        // Get user image URL
        function getUserImage(userInfo) {
            if (!userInfo) return '/storage/assets/uploads/user-images/thumbnail.png';
            
            try {
                const info = typeof userInfo === 'string' ? JSON.parse(userInfo) : userInfo;
                if (info.photo) {
                    return '/storage/assets/uploads/user-images/' + info.photo;
                }
            } catch (e) {
                console.error('Error parsing user information:', e);
            }
            
            return '/storage/assets/uploads/user-images/thumbnail.png';
        }

        // Create message HTML element
        function createMessageElement(messageData, isOwnMessage) {
            const messageDiv = document.createElement('div');
            messageDiv.className = isOwnMessage ? 'chat-message-right pb-3' : 'chat-message-left pb-3';
            messageDiv.setAttribute('data-message-id', messageData.id);
            messageDiv.style.display = 'block';

            if (isOwnMessage) {
                messageDiv.innerHTML = `
                    <div class="flex-shrink-1">
                        ${escapeHtml(messageData.message)}
                    </div>
                    <div class="text-muted small text-nowrap mt-2">
                        ${formatDate(messageData.created_at)}
                    </div>
                `;
            } else {
                const userImage = getUserImage(messageData.sender.user_information);
                messageDiv.innerHTML = `
                    <div>
                        <img src="${userImage}" class="rounded-circle mr-1" alt="${escapeHtml(messageData.sender.name)}" width="40" height="40">
                        <div class="text-muted small text-nowrap mt-2">
                            ${formatDate(messageData.created_at)}
                        </div>
                    </div>
                    <div class="msg_text_body">
                        <div class="flex-shrink-1 bg-light">
                            <div class="font-weight-bold"></div>
                            ${escapeHtml(messageData.message)}
                        </div>
                        <div class="text-muted small text-nowrap mt-2">
                            ${formatDate(messageData.created_at)}
                        </div>
                    </div>
                `;
            }

            return messageDiv;
        }

        // Escape HTML to prevent XSS
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Scroll chat to bottom
        function scrollToBottom() {
            const chatMessagesBody = document.getElementById('chat_messages_body');
            if (chatMessagesBody) {
                chatMessagesBody.scrollTop = chatMessagesBody.scrollHeight;
            }
        }

        // Add message to chat
        function addMessageToChat(messageData) {
            const chatMessagesBody = document.getElementById('chat_messages_body');
            if (!chatMessagesBody) return;

            const isOwnMessage = parseInt(messageData.sender_id) === parseInt(currentUserId);
            
            // Only add if it belongs to current thread
            if (parseInt(messageData.message_thrade) !== parseInt(currentThreadId)) {
                return;
            }

            // Check if message already exists (prevent duplicates)
            const existingMessage = chatMessagesBody.querySelector(`[data-message-id="${messageData.id}"]`);
            if (existingMessage) {
                return;
            }

            const messageElement = createMessageElement(messageData, isOwnMessage);
            chatMessagesBody.appendChild(messageElement);
            
            // Scroll to bottom
            setTimeout(scrollToBottom, 100);

            // Play notification sound (optional)
            playNotificationSound();
        }

        // Play notification sound
        function playNotificationSound() {
            // You can add a notification sound here if needed
            // const audio = new Audio('/assets/sounds/notification.mp3');
            // audio.play().catch(e => console.log('Could not play sound:', e));
        }

        // Update unread count in sidebar (if on message list page)
        function updateUnreadCount(threadId) {
            // This would update the unread count badge in the sidebar
            const threadLink = document.querySelector(`a[href*="all-message/${threadId}"]`);
            if (threadLink) {
                const badge = threadLink.querySelector('.badge');
                if (badge) {
                    const currentCount = parseInt(badge.textContent) || 0;
                    badge.textContent = currentCount + 1;
                    badge.classList.remove('d-none');
                }
            }
        }

        // Subscribe to user's private channel for notifications
        const userChannel = pusher.subscribe('private-user.' + currentUserId);
        
        // Listen for message event (Laravel broadcasts with broadcastAs name)
        userChannel.bind('message.sent', function(data) {
            console.log('New message received on user channel:', data);
            
            // If user is not on the message thread page, update unread count
            if (parseInt(data.message_thrade) !== parseInt(currentThreadId)) {
                updateUnreadCount(data.message_thrade);
            }
        });
        
        // Also listen for the full class name (fallback)
        userChannel.bind('App\\Events\\MessageSent', function(data) {
            console.log('New message received on user channel (fallback):', data);
            if (parseInt(data.message_thrade) !== parseInt(currentThreadId)) {
                updateUnreadCount(data.message_thrade);
            }
        });

        // Subscribe to message thread channel
        if (currentThreadId) {
            const threadChannel = pusher.subscribe('private-message-thread.' + currentThreadId);
            
            // Listen for message event
            threadChannel.bind('message.sent', function(data) {
                console.log('New message received on thread channel:', data);
                addMessageToChat(data);
            });
            
            // Also listen for the full class name (fallback)
            threadChannel.bind('App\\Events\\MessageSent', function(data) {
                console.log('New message received on thread channel (fallback):', data);
                addMessageToChat(data);
            });
        }

        // Handle connection status
        pusher.connection.bind('connected', () => {
            console.log('Connected to Pusher for real-time messaging');
        });

        pusher.connection.bind('disconnected', () => {
            console.warn('Disconnected from Pusher');
        });

        pusher.connection.bind('error', (err) => {
            console.error('Pusher connection error:', err);
        });

        // Store pusher instance globally for debugging
        window.pusherInstance = pusher;
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initRealtimeMessages);
    } else {
        initRealtimeMessages();
    }

})();

