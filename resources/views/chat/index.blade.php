<x-app-layout>
    <div x-data="chatApp()" x-init="initChat()" class="h-[calc(100vh-65px)] flex overflow-hidden bg-gray-900 text-gray-100">
        <!-- Sidebar -->
        <div class="w-80 flex-shrink-0 border-r border-gray-700 bg-gray-800 flex flex-col transition-all duration-300">
            <div class="p-4 border-b border-gray-700 flex justify-between items-center bg-gray-800/50 backdrop-blur-md">
                <h2 class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-red-500 to-orange-500">
                    SalsaChat
                </h2>
                <button @click="showGroupModal = true" class="p-2 rounded-full hover:bg-gray-700 transition-colors text-red-400" title="Buat Grup Baru">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto p-3 space-y-6">
                <!-- Groups Section -->
                <div>
                    <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3 px-2">Grup Saya</h3>
                    <div class="space-y-1">
                        <template x-for="group in groups" :key="group.id">
                            <button @click="openChat('group', group)" 
                                :class="{'bg-gray-700 border-l-4 border-red-500 text-white': activeChatType === 'group' && activeChatId === group.id, 'text-gray-300 hover:bg-gray-700/50': !(activeChatType === 'group' && activeChatId === group.id)}"
                                class="w-full text-left px-3 py-2 rounded-md transition-all flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-orange-500 to-red-600 flex items-center justify-center text-sm font-bold text-white shadow-lg">
                                    <span x-text="group.name.charAt(0).toUpperCase()"></span>
                                </div>
                                <span class="font-medium truncate" x-text="group.name"></span>
                            </button>
                        </template>
                        <div x-show="groups.length === 0" class="text-sm text-gray-500 px-2 italic">Belum ada grup.</div>
                    </div>
                </div>

                <!-- Users Section -->
                <div>
                    <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3 px-2">Private Message</h3>
                    <div class="space-y-1">
                        <template x-for="user in users" :key="user.id">
                            <button @click="openChat('private', user)" 
                                :class="{'bg-gray-700 border-l-4 border-red-500 text-white': activeChatType === 'private' && activeChatId === user.id, 'text-gray-300 hover:bg-gray-700/50': !(activeChatType === 'private' && activeChatId === user.id)}"
                                class="w-full text-left px-3 py-2 rounded-md transition-all flex items-center gap-3 relative group">
                                <div class="relative">
                                    <div class="w-8 h-8 rounded-full bg-gray-600 flex items-center justify-center text-sm font-bold text-white">
                                        <span x-text="user.name.charAt(0).toUpperCase()"></span>
                                    </div>
                                    <div class="absolute bottom-0 right-0 w-3 h-3 rounded-full border-2 border-gray-800 transition-colors duration-300" 
                                         :class="isOnline(user.id) ? 'bg-green-500' : 'bg-gray-400'"></div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="font-medium truncate" x-text="user.name"></div>
                                    <div class="text-xs text-gray-400 truncate" x-text="isOnline(user.id) ? 'Online' : 'Offline'"></div>
                                </div>
                            </button>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Chat Area -->
        <div class="flex-1 flex flex-col bg-gray-900 relative">
            <template x-if="!activeChatId">
                <div class="flex-1 flex flex-col items-center justify-center text-gray-500 p-8 text-center">
                    <div class="w-24 h-24 mb-6 rounded-full bg-gray-800 flex items-center justify-center shadow-inner">
                        <svg class="w-12 h-12 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-300 mb-2">Selamat Datang di SalsaChat</h2>
                    <p class="max-w-md">Pilih percakapan dari sidebar kiri atau buat grup baru untuk mulai mengobrol.</p>
                </div>
            </template>

            <template x-if="activeChatId">
                <div class="flex-1 flex flex-col h-full">
                    <!-- Chat Header -->
                    <div class="h-16 px-6 border-b border-gray-800 bg-gray-900/80 backdrop-blur-md flex items-center shadow-sm z-10">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold shadow-md"
                                 :class="activeChatType === 'group' ? 'bg-gradient-to-br from-orange-500 to-red-600' : 'bg-gray-700'">
                                <span x-text="activeChatName.charAt(0).toUpperCase()"></span>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-100" x-text="activeChatName"></h3>
                                <p class="text-xs text-gray-400" x-text="activeChatType === 'group' ? 'Group Chat' : (isOnline(activeChatId) ? 'Online' : 'Offline')"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Messages List -->
                    <div class="flex-1 overflow-y-auto p-6 space-y-4" id="messages-container">
                        <template x-for="message in messages" :key="message.id">
                            <div class="flex" :class="message.sender_id === {{ auth()->id() }} ? 'justify-end' : 'justify-start'">
                                <div class="max-w-[75%] flex flex-col" :class="message.sender_id === {{ auth()->id() }} ? 'items-end' : 'items-start'">
                                    <span x-show="message.sender_id !== {{ auth()->id() }} && activeChatType === 'group'" 
                                          class="text-xs text-gray-400 ml-1 mb-1" x-text="message.sender?.name"></span>
                                    <div class="px-4 py-2 rounded-2xl shadow-sm"
                                         :class="message.sender_id === {{ auth()->id() }} ? 'bg-gradient-to-r from-red-600 to-orange-500 text-white rounded-br-none' : 'bg-gray-800 text-gray-100 rounded-bl-none border border-gray-700'">
                                        <p class="whitespace-pre-wrap break-words" x-text="message.message"></p>
                                    </div>
                                    <span class="text-[10px] text-gray-500 mt-1" x-text="formatTime(message.created_at)"></span>
                                </div>
                            </div>
                        </template>
                        <!-- Typings indicator placeholder -->
                        <div x-show="isTyping" class="flex justify-start items-center gap-2 text-gray-500 text-sm mt-2">
                            <span class="flex gap-1">
                                <span class="w-1.5 h-1.5 bg-gray-500 rounded-full animate-bounce"></span>
                                <span class="w-1.5 h-1.5 bg-gray-500 rounded-full animate-bounce" style="animation-delay: 0.2s"></span>
                                <span class="w-1.5 h-1.5 bg-gray-500 rounded-full animate-bounce" style="animation-delay: 0.4s"></span>
                            </span>
                            seseorang sedang mengetik...
                        </div>
                    </div>

                    <!-- Input Area -->
                    <div class="p-4 bg-gray-900 border-t border-gray-800">
                        <form @submit.prevent="sendMessage" class="flex gap-2">
                            <input type="text" x-model="newMessage" 
                                class="flex-1 bg-gray-800 border border-gray-700 rounded-full px-6 py-3 text-gray-100 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all"
                                placeholder="Ketik pesan..." required autocomplete="off">
                            <button type="submit" :disabled="!newMessage.trim() || isSending"
                                class="w-12 h-12 rounded-full bg-red-600 hover:bg-red-500 flex items-center justify-center text-white transition-all disabled:opacity-50 disabled:cursor-not-allowed shadow-lg shadow-red-900/50">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transform rotate-90" viewBox="0 0 20 20" fill="currentColor">
                                  <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </template>
        </div>

        <!-- Create Group Modal -->
        <div x-show="showGroupModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm" style="display: none;">
            <div @click.away="showGroupModal = false" class="bg-gray-800 w-full max-w-md rounded-2xl shadow-2xl border border-gray-700 overflow-hidden transform transition-all">
                <div class="px-6 py-4 border-b border-gray-700 flex justify-between items-center bg-gray-800/50">
                    <h3 class="text-lg font-bold text-white">Buat Grup Baru</h3>
                    <button @click="showGroupModal = false" class="text-gray-400 hover:text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <form @submit.prevent="createGroup" class="p-6">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-300 mb-2">Nama Grup</label>
                        <input type="text" x-model="newGroupName" required
                            class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-300 mb-2">Undang Anggota</label>
                        <div class="max-h-48 overflow-y-auto bg-gray-900 border border-gray-700 rounded-lg p-2 space-y-1">
                            <template x-for="user in users" :key="user.id">
                                <label class="flex items-center p-2 hover:bg-gray-800 rounded cursor-pointer transition-colors">
                                    <input type="checkbox" :value="user.id" x-model="selectedGroupUsers" class="rounded border-gray-600 bg-gray-700 text-red-500 focus:ring-red-500 focus:ring-offset-gray-900">
                                    <span class="ml-3 text-gray-300" x-text="user.name"></span>
                                </label>
                            </template>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" @click="showGroupModal = false" class="px-4 py-2 text-gray-400 hover:text-white transition-colors">Batal</button>
                        <button type="submit" :disabled="isCreatingGroup || !newGroupName.trim()" 
                                class="px-6 py-2 bg-gradient-to-r from-red-600 to-orange-500 hover:from-red-500 hover:to-orange-400 text-white rounded-lg font-medium shadow-lg shadow-red-900/20 disabled:opacity-50 transition-all">
                            Buat Grup
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('chatApp', () => ({
                users: @json($users),
                groups: @json($groups),
                messages: [],
                onlineUsers: [],
                
                activeChatType: null, // 'private' or 'group'
                activeChatId: null,
                activeChatName: '',
                
                newMessage: '',
                isSending: false,
                isTyping: false,
                
                showGroupModal: false,
                newGroupName: '',
                selectedGroupUsers: [],
                isCreatingGroup: false,
                
                currentUserId: {{ auth()->id() }},
                
                initChat() {
                    // Listen to global online presence channel
                    window.Echo.join('chat.online')
                        .here((users) => {
                            this.onlineUsers = users;
                        })
                        .joining((user) => {
                            if (!this.onlineUsers.find(u => u.id === user.id)) {
                                this.onlineUsers.push(user);
                            }
                        })
                        .leaving((user) => {
                            this.onlineUsers = this.onlineUsers.filter(u => u.id !== user.id);
                        });

                    // Listen to private messages sent to me
                    window.Echo.private(`chat.private.${this.currentUserId}`)
                        .listen('.message.sent', (e) => {
                            // If we are currently chatting with the sender, append message
                            if (this.activeChatType === 'private' && this.activeChatId === e.message.sender_id) {
                                this.messages.push(e.message);
                                this.scrollToBottom();
                            } else {
                                // Notification can be added here
                            }
                        });

                    // Join all group channels
                    this.groups.forEach(group => {
                        this.listenToGroup(group.id);
                    });
                },

                listenToGroup(groupId) {
                    window.Echo.join(`chat.group.${groupId}`)
                        .listen('.message.sent', (e) => {
                            if (this.activeChatType === 'group' && this.activeChatId === groupId && e.message.sender_id !== this.currentUserId) {
                                this.messages.push(e.message);
                                this.scrollToBottom();
                            }
                        });
                },

                isOnline(userId) {
                    return this.onlineUsers.some(u => u.id === userId);
                },

                async openChat(type, entity) {
                    this.activeChatType = type;
                    this.activeChatId = entity.id;
                    this.activeChatName = entity.name;
                    this.messages = [];
                    
                    try {
                        const url = type === 'private' 
                            ? `/messages/private/${entity.id}` 
                            : `/messages/group/${entity.id}`;
                            
                        const response = await axios.get(url);
                        this.messages = response.data;
                        this.scrollToBottom();
                    } catch (error) {
                        console.error('Error fetching messages:', error);
                    }
                },

                async sendMessage() {
                    if (!this.newMessage.trim() || this.isSending) return;
                    
                    this.isSending = true;
                    const payload = { message: this.newMessage };
                    const url = this.activeChatType === 'private' ? '/messages/private' : '/messages/group';
                    
                    if (this.activeChatType === 'private') {
                        payload.receiver_id = this.activeChatId;
                    } else {
                        payload.group_id = this.activeChatId;
                    }

                    try {
                        const response = await axios.post(url, payload);
                        this.messages.push(response.data);
                        this.newMessage = '';
                        this.scrollToBottom();
                    } catch (error) {
                        console.error('Error sending message:', error);
                    } finally {
                        this.isSending = false;
                        this.$nextTick(() => {
                            this.$el.querySelector('input')?.focus();
                        });
                    }
                },

                async createGroup() {
                    if (!this.newGroupName.trim() || this.isCreatingGroup) return;
                    this.isCreatingGroup = true;
                    
                    try {
                        const response = await axios.post('/groups', {
                            name: this.newGroupName,
                            users: this.selectedGroupUsers
                        });
                        
                        this.groups.push(response.data);
                        this.listenToGroup(response.data.id);
                        
                        this.showGroupModal = false;
                        this.newGroupName = '';
                        this.selectedGroupUsers = [];
                        
                        // Open the newly created group chat
                        this.openChat('group', response.data);
                    } catch (error) {
                        console.error('Error creating group:', error);
                        alert('Gagal membuat grup.');
                    } finally {
                        this.isCreatingGroup = false;
                    }
                },

                scrollToBottom() {
                    this.$nextTick(() => {
                        const container = document.getElementById('messages-container');
                        if (container) {
                            container.scrollTop = container.scrollHeight;
                        }
                    });
                },

                formatTime(datetime) {
                    const date = new Date(datetime);
                    return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                }
            }));
        });
    </script>
    @endpush
</x-app-layout>
