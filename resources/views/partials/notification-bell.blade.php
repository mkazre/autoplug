<div class="relative" x-data="notifBell()" x-init="init()">
    <button @click="toggle()" class="relative p-2 text-gray-500 hover:text-gray-700 focus:outline-none" aria-label="Notifications">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
        <span x-show="unread > 0" x-cloak x-text="unread > 9 ? '9+' : unread" class="absolute top-0 right-0 bg-red-500 text-white text-[10px] font-semibold leading-none min-w-[16px] h-4 px-1 rounded-full flex items-center justify-center"></span>
    </button>

    <div x-show="open" x-cloak @click.outside="open = false"
         class="absolute right-0 mt-2 w-80 max-w-[90vw] bg-white rounded-xl shadow-lg border border-gray-100 z-50">
        <div class="flex items-center justify-between px-4 py-2.5 border-b border-gray-100">
            <span class="text-sm font-semibold text-gray-800">Notifications</span>
            <button @click="markAll()" x-show="unread > 0" class="text-xs text-violet-600 hover:underline">Mark all read</button>
        </div>
        <div class="max-h-96 overflow-y-auto">
            <template x-if="items.length === 0">
                <p class="px-4 py-8 text-sm text-gray-400 text-center">You're all caught up.</p>
            </template>
            <template x-for="n in items" :key="n.id">
                <a :href="n.url" class="flex items-start gap-2 px-4 py-3 border-b border-gray-50 hover:bg-gray-50" :class="!n.read ? 'bg-violet-50/40' : ''">
                    <span class="mt-1.5 w-2 h-2 rounded-full shrink-0" :class="!n.read ? 'bg-violet-500' : 'bg-gray-200'"></span>
                    <span class="min-w-0">
                        <span class="block text-sm text-gray-800" x-text="n.title"></span>
                        <span class="block text-xs text-gray-500 truncate" x-show="n.body" x-text="n.body"></span>
                        <span class="block text-[11px] text-gray-400 mt-0.5" x-text="n.ago"></span>
                    </span>
                </a>
            </template>
        </div>
    </div>

    <audio x-ref="chime" src="/sounds/notify.mp3" preload="auto"></audio>
</div>

<style>[x-cloak]{display:none!important;}</style>

<script>
    function notifBell() {
        return {
            open: false,
            unread: 0,
            items: [],
            lastUnread: null,
            init() {
                this.load(true);
                setInterval(() => this.load(false), 25000);
            },
            toggle() {
                this.open = ! this.open;
                if (this.open) this.load(false);
            },
            load(first) {
                fetch('{{ route('notifications.index') }}', { headers: { 'Accept': 'application/json' } })
                    .then(r => r.json())
                    .then(d => {
                        if (! first && this.lastUnread !== null && d.unread > this.lastUnread) this.play();
                        this.unread = d.unread;
                        this.lastUnread = d.unread;
                        this.items = d.items;
                    })
                    .catch(() => {});
            },
            markAll() {
                fetch('{{ route('notifications.read-all') }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                }).then(() => {
                    this.unread = 0;
                    this.lastUnread = 0;
                    this.items = this.items.map(n => ({ ...n, read: true }));
                }).catch(() => {});
            },
            play() {
                try { this.$refs.chime.currentTime = 0; this.$refs.chime.play(); } catch (e) {}
            }
        }
    }
</script>
