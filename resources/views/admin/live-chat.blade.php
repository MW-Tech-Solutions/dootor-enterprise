@extends('layouts.dashboard')

@section('title', 'Live Support Chat — DOOTOR ENTERPRISES')

@section('styles')
<style>
.lc-kpi-card    { background: #fff; border-radius: 14px; padding: 14px 18px; border: 1px solid #eef2f6; box-shadow: 0 2px 10px rgba(0,0,0,.03); }
.lc-wrap        { display: grid; grid-template-columns: 340px 1fr; height: calc(100vh - 230px); min-height: 520px; border-radius: 18px; overflow: hidden; border: 1px solid #e2e8f0; box-shadow: 0 18px 50px rgba(0,0,0,.08); background: #fff; }
.lc-sidebar     { background: #0d1b14; display: flex; flex-direction: column; border-right: 1px solid rgba(255,255,255,.08); color: #fff; }
.lc-sidebar-hd  { padding: 16px 18px 12px; border-bottom: 1px solid rgba(255,255,255,.08); }
.lc-tabs        { display: flex; gap: 6px; margin-top: 10px; }
.lc-tab-btn     { flex: 1; background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.1); color: rgba(255,255,255,.7); border-radius: 8px; padding: 6px 8px; font-size: 11.5px; font-weight: 600; cursor: pointer; transition: all .2s; }
.lc-tab-btn.active { background: #D4AF37; color: #0d1b14; border-color: #D4AF37; }
.lc-queue-list  { flex: 1; overflow-y: auto; padding: 12px; }
.lc-chat-panel  { background: #f8fafc; display: flex; flex-direction: column; min-width: 0; }
.lc-chat-hd     { background: #fff; padding: 14px 20px; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
.lc-messages    { flex: 1; overflow-y: auto; padding: 20px; display: flex; flex-direction: column; gap: 10px; }
.lc-canned-bar  { background: #fff; border-top: 1px solid #f1f5f9; padding: 8px 16px; display: flex; gap: 6px; overflow-x: auto; white-space: nowrap; }
.lc-canned-chip { background: #f1f5f9; border: 1px solid #e2e8f0; color: #334155; border-radius: 20px; padding: 4px 12px; font-size: 11.5px; font-weight: 500; cursor: pointer; transition: all .15s; }
.lc-canned-chip:hover { background: #004225; color: #fff; border-color: #004225; }
.lc-input-area  { background: #fff; border-top: 1px solid #e2e8f0; padding: 12px 16px; display: flex; gap: 10px; align-items: flex-end; }
.lc-input-area textarea { flex: 1; resize: none; border: 1.5px solid #e2e8f0; border-radius: 12px; padding: 10px 14px; font-size: 14px; outline: none; transition: border-color .2s; }
.lc-input-area textarea:focus { border-color: #004225; }

.queue-card     { background: rgba(255,255,255,.05); border: 1px solid rgba(255,255,255,.09); border-radius: 12px; padding: 12px 14px; cursor: pointer; transition: all .2s; margin-bottom: 8px; }
.queue-card:hover, .queue-card.active { background: rgba(212,175,55,.14); border-color: rgba(212,175,55,.5); }
.queue-card .q-num { width: 28px; height: 28px; background: #D4AF37; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 800; color: #0d1b14; flex-shrink: 0; }

.msg-bubble     { max-width: 72%; padding: 10px 14px; border-radius: 16px; font-size: 13.5px; line-height: 1.5; word-break: break-word; }
.msg-client     { background: #ffffff; color: #1e293b; border: 1px solid #e2e8f0; border-bottom-left-radius: 4px; align-self: flex-start; box-shadow: 0 1px 3px rgba(0,0,0,.04); }
.msg-agent      { background: #004225; color: #fff; border-bottom-right-radius: 4px; align-self: flex-end; box-shadow: 0 2px 6px rgba(0,66,37,.2); }
.msg-system     { background: rgba(212,175,55,.14); color: #854d0e; border-radius: 10px; align-self: center; font-size: 12px; font-style: italic; text-align: center; padding: 6px 14px; max-width: 85%; }
.msg-time       { font-size: 10.5px; opacity: .65; margin-top: 3px; }
.msg-wrap       { display: flex; flex-direction: column; }
.msg-wrap.agent { align-items: flex-end; }

.badge-wait     { background: rgba(234,179,8,.18); color: #fde047; border: 1px solid rgba(234,179,8,.35); }
.badge-active   { background: rgba(34,197,94,.18); color: #86efac; border: 1px solid rgba(34,197,94,.35); }
.pulse-dot      { width: 8px; height: 8px; border-radius: 50%; background: #22c55e; animation: lcPulse 1.5s ease-in-out infinite; display: inline-block; }
@keyframes lcPulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.5;transform:scale(1.4)} }

.lc-empty       { display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; color: #64748b; gap: 10px; padding: 30px; text-align: center; }

.lc-queue-list::-webkit-scrollbar, .lc-messages::-webkit-scrollbar { width: 5px; }
.lc-queue-list::-webkit-scrollbar-thumb { background: rgba(255,255,255,.15); border-radius: 4px; }
.lc-messages::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }

@media (max-width: 991px) {
    .lc-wrap { grid-template-columns: 1fr; height: auto; }
    .lc-sidebar { height: 280px; }
    .lc-chat-panel { height: 520px; }
}
</style>
@endsection

@section('content')
<div class="container-fluid px-3 px-md-4 py-4">
    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h4 class="fw-bold mb-0 text-dark">
                <i class="bi bi-chat-dots-fill text-success me-2"></i>Live Support Chat Desk
            </h4>
            <p class="text-secondary small mb-0 mt-1">Manage incoming client queue, accept sessions, and respond in real time.</p>
        </div>
        <div class="d-flex gap-2 align-items-center">
            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-2 rounded-pill fw-semibold">
                <i class="bi bi-clock-history me-1"></i>
                <span id="waitingCount">{{ $waiting->count() }}</span> Waiting
            </span>
            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill fw-semibold">
                <span class="pulse-dot me-1"></span>
                <span id="activeCount">{{ $allActive }}</span> Active
            </span>
        </div>
    </div>

    <!-- KPI Row -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="lc-kpi-card">
                <div class="text-secondary extra-small fw-bold text-uppercase">In Queue</div>
                <div class="h4 fw-bold text-dark mb-0 mt-1" id="kpiWaiting">{{ $waiting->count() }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="lc-kpi-card">
                <div class="text-secondary extra-small fw-bold text-uppercase">Your Active Chats</div>
                <div class="h4 fw-bold text-success mb-0 mt-1">{{ $active->count() }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="lc-kpi-card">
                <div class="text-secondary extra-small fw-bold text-uppercase">All Active Agents</div>
                <div class="h4 fw-bold text-primary mb-0 mt-1">{{ $allActive }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="lc-kpi-card">
                <div class="text-secondary extra-small fw-bold text-uppercase">Closed Today</div>
                <div class="h4 fw-bold text-secondary mb-0 mt-1">{{ $closedToday ?? 0 }}</div>
            </div>
        </div>
    </div>

    <div class="lc-wrap">
        <!-- ── LEFT SIDEBAR: Queue & Active ───────────────────── -->
        <div class="lc-sidebar">
            <div class="lc-sidebar-hd">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-white fw-bold small text-uppercase" style="letter-spacing:.8px;">
                        <i class="bi bi-people-fill text-warning me-1"></i>Support Queue
                    </span>
                    <button type="button" class="btn btn-sm text-white-50 p-0 border-0" onclick="location.reload()" title="Refresh Queue">
                        <i class="bi bi-arrow-clockwise"></i>
                    </button>
                </div>
                <div class="mt-2">
                    <input type="text" id="queueSearch" class="form-control form-control-sm bg-dark text-white border-secondary rounded-pill px-3" placeholder="Search client name..." oninput="filterQueue()">
                </div>
                <div class="lc-tabs">
                    <button type="button" class="lc-tab-btn active" onclick="switchTab('waiting', this)">Waiting ({{ $waiting->count() }})</button>
                    <button type="button" class="lc-tab-btn" onclick="switchTab('active', this)">Active ({{ $active->count() }})</button>
                    <button type="button" class="lc-tab-btn" onclick="switchTab('closed', this)">Closed ({{ isset($recentClosed) ? $recentClosed->count() : 0 }})</button>
                </div>
            </div>

            <div class="lc-queue-list" id="queueList">
                <!-- Waiting Tab -->
                <div id="tab-waiting">
                    @forelse($waiting as $chat)
                    <div class="queue-card" data-name="{{ strtolower($chat->client?->name ?? '') }}" id="qcard-{{ $chat->id }}" onclick="loadChat({{ $chat->id }}, 'waiting')">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <div class="q-num">{{ $chat->queue_position }}</div>
                            <div class="flex-grow-1 overflow-hidden">
                                <div class="text-white fw-semibold small text-truncate">{{ $chat->client?->name ?? 'Client #' . $chat->client_id }}</div>
                                <div class="text-white-50 text-truncate" style="font-size:11px;">{{ $chat->subject ?? 'General Enquiry' }}</div>
                            </div>
                            <span class="badge badge-wait" style="font-size:10px;">Waiting</span>
                        </div>
                        <div class="text-white-50 d-flex justify-content-between mt-1" style="font-size:11px;">
                            <span><i class="bi bi-clock me-1"></i>{{ $chat->created_at->diffForHumans() }}</span>
                            <span class="text-truncate" style="max-width:130px;">{{ $chat->client?->email }}</span>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5 text-white-50 small">
                        <i class="bi bi-check-circle-fill text-success d-block mb-2 fs-3"></i>
                        <div class="fw-semibold text-white">No clients waiting</div>
                        <div style="font-size:11.5px;">New requests will appear here automatically.</div>
                    </div>
                    @endforelse
                </div>

                <!-- Active Tab -->
                <div id="tab-active" class="d-none">
                    @forelse($active as $chat)
                    <div class="queue-card" data-name="{{ strtolower($chat->client?->name ?? '') }}" id="qcard-{{ $chat->id }}" onclick="loadChat({{ $chat->id }}, 'active')">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <div class="q-num" style="background:#22c55e;color:#fff;">
                                <i class="bi bi-chat-fill" style="font-size:11px;"></i>
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <div class="text-white fw-semibold small text-truncate">{{ $chat->client?->name ?? 'Client' }}</div>
                                <div class="text-white-50 text-truncate" style="font-size:11px;">{{ $chat->latestMessage?->message ?? $chat->subject }}</div>
                            </div>
                            <span class="badge badge-active" style="font-size:10px;"><span class="pulse-dot me-1"></span>Active</span>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5 text-white-50 small">
                        <i class="bi bi-chat-square-text d-block mb-2 fs-3"></i>
                        No active chats assigned to you.
                    </div>
                    @endforelse
                </div>

                <!-- Closed Tab -->
                <div id="tab-closed" class="d-none">
                    @forelse($recentClosed ?? [] as $chat)
                    <div class="queue-card" data-name="{{ strtolower($chat->client?->name ?? '') }}" id="qcard-{{ $chat->id }}" onclick="loadChat({{ $chat->id }}, 'closed')">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <div class="q-num" style="background:#64748b;color:#fff;">
                                <i class="bi bi-archive" style="font-size:11px;"></i>
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <div class="text-white fw-semibold small text-truncate">{{ $chat->client?->name ?? 'Client' }}</div>
                                <div class="text-white-50 text-truncate" style="font-size:11px;">Closed {{ $chat->closed_at?->diffForHumans() ?? $chat->updated_at->diffForHumans() }}</div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5 text-white-50 small">
                        No recently closed sessions.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- ── RIGHT PANEL: Chat ─────────────────────── -->
        <div class="lc-chat-panel" id="chatPanel">
            <div class="lc-empty" id="chatEmpty">
                <div class="rounded-circle bg-success bg-opacity-10 d-flex align-items-center justify-content-center mb-2" style="width:72px;height:72px;">
                    <i class="bi bi-chat-square-dots-fill text-success fs-2"></i>
                </div>
                <h6 class="fw-bold text-dark mb-1">Select a chat from the queue</h6>
                <p class="small text-secondary mb-0" style="max-width:340px;">New incoming client support requests appear in the queue automatically. Click any client on the left to preview and accept their chat.</p>
            </div>

            <div id="chatActive" class="d-none d-flex flex-column h-100">
                <div class="lc-chat-hd">
                    <div class="rounded-circle bg-success d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0" style="width:40px;height:40px;font-size:15px;" id="chatAvatar">?</div>
                    <div class="flex-grow-1 min-w-0">
                        <div class="fw-bold text-dark small text-truncate" id="chatClientName">—</div>
                        <div class="text-muted text-truncate" style="font-size:11.5px;" id="chatStatus">Waiting for agent</div>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-success rounded-pill px-3 fw-bold d-none" id="acceptBtn" onclick="acceptChat()">
                            <i class="bi bi-check-lg me-1"></i>Accept Chat
                        </button>
                        <button class="btn btn-sm btn-outline-danger rounded-pill px-3 d-none" id="closeBtn" onclick="closeChat()">
                            <i class="bi bi-x-lg me-1"></i>Close Session
                        </button>
                    </div>
                </div>

                <div class="lc-messages" id="messagesArea"></div>

                <div class="lc-canned-bar d-none" id="cannedBar">
                    <button type="button" class="lc-canned-chip" onclick="useCanned('Hello! Welcome to DOOTOR Enterprises Live Support. How may I assist you today?')">👋 Welcome</button>
                    <button type="button" class="lc-canned-chip" onclick="useCanned('Could you please share your application reference number (e.g., DTR-...) so I can check your status?')">🔎 Ask Reference #</button>
                    <button type="button" class="lc-canned-chip" onclick="useCanned('Please ensure your passport photograph meets ICAO 9303 standards (plain light background, minimum 380x380px, clear front-facing view).')">📷 ICAO Photo Tip</button>
                    <button type="button" class="lc-canned-chip" onclick="useCanned('Your request is currently being processed by our consular liaison team. We will notify you immediately upon completion.')">⏳ Processing Update</button>
                </div>

                <div class="lc-input-area d-none" id="inputArea">
                    <textarea id="agentInput" rows="1" placeholder="Type your reply to the client…" onkeydown="handleKey(event)" oninput="autoResize(this)"></textarea>
                    <button class="btn fw-bold rounded-circle d-flex align-items-center justify-content-center" onclick="agentSend()"
                        style="width:42px;height:42px;background:#004225;color:#D4AF37;border:none;flex-shrink:0;">
                        <i class="bi bi-send-fill"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let activeChatId   = null;
let activeChatType = null;
let lastMsgId      = 0;
let pollTimer      = null;

function switchTab(tab, btn) {
    document.querySelectorAll('.lc-tab-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    ['waiting', 'active', 'closed'].forEach(t => {
        const el = document.getElementById('tab-' + t);
        if (el) el.classList.toggle('d-none', t !== tab);
    });
}

function filterQueue() {
    const q = (document.getElementById('queueSearch').value || '').toLowerCase();
    document.querySelectorAll('.queue-card').forEach(c => {
        const name = c.getAttribute('data-name') || '';
        c.style.display = name.includes(q) ? '' : 'none';
    });
}

function loadChat(chatId, type) {
    activeChatId   = chatId;
    activeChatType = type;
    lastMsgId      = 0;

    document.getElementById('chatEmpty').classList.add('d-none');
    document.getElementById('chatActive').classList.remove('d-none');
    document.getElementById('messagesArea').innerHTML = '';

    document.querySelectorAll('.queue-card').forEach(c => c.classList.remove('active'));
    const card = document.getElementById('qcard-' + chatId);
    if (card) card.classList.add('active');

    const clientName = card ? (card.querySelector('.fw-semibold')?.textContent.trim() || 'Client') : 'Client';
    document.getElementById('chatClientName').textContent = clientName;
    document.getElementById('chatAvatar').textContent = clientName.charAt(0).toUpperCase();

    const acceptBtn = document.getElementById('acceptBtn');
    const closeBtn  = document.getElementById('closeBtn');
    const inputArea = document.getElementById('inputArea');
    const cannedBar = document.getElementById('cannedBar');

    acceptBtn.classList.toggle('d-none', type !== 'waiting');
    closeBtn.classList.toggle('d-none', type !== 'active');
    inputArea.classList.toggle('d-none', type !== 'active');
    cannedBar.classList.toggle('d-none', type !== 'active');

    if (type === 'waiting') {
        document.getElementById('chatStatus').textContent = 'Waiting in queue — Click Accept Chat to respond';
    } else if (type === 'active') {
        document.getElementById('chatStatus').innerHTML = '<span class="pulse-dot me-1"></span>Active Session';
    } else {
        document.getElementById('chatStatus').textContent = 'Closed Session (Read Only)';
    }

    fetchMessages();
    clearInterval(pollTimer);
    if (type !== 'closed') {
        pollTimer = setInterval(fetchMessages, 3000);
    }
}

function fetchMessages() {
    if (!activeChatId) return;
    fetch(`{{ url('/admin/live-chat') }}/${activeChatId}/messages?after=${lastMsgId}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        const area = document.getElementById('messagesArea');
        (data.messages || []).forEach(m => {
            if (m.id > lastMsgId) lastMsgId = m.id;
            const wrap = document.createElement('div');
            wrap.className = 'msg-wrap' + (m.sender_type === 'agent' ? ' agent' : '');
            const bubble = document.createElement('div');
            bubble.className = 'msg-bubble msg-' + m.sender_type;
            bubble.textContent = m.message;
            wrap.appendChild(bubble);
            if (m.sender_type !== 'system') {
                const time = document.createElement('div');
                time.className = 'msg-time text-muted';
                time.textContent = m.sender_name + ' · ' + m.time;
                wrap.appendChild(time);
            }
            area.appendChild(wrap);
        });
        if ((data.messages || []).length) area.scrollTop = area.scrollHeight;
    });
}

function acceptChat() {
    if (!activeChatId) return;
    fetch(`{{ url('/admin/live-chat') }}/${activeChatId}/accept`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(() => {
        activeChatType = 'active';
        document.getElementById('acceptBtn').classList.add('d-none');
        document.getElementById('closeBtn').classList.remove('d-none');
        document.getElementById('inputArea').classList.remove('d-none');
        document.getElementById('cannedBar').classList.remove('d-none');
        document.getElementById('chatStatus').innerHTML = '<span class="pulse-dot me-1"></span>Active Session';
        fetchMessages();
    });
}

function useCanned(text) {
    const inp = document.getElementById('agentInput');
    inp.value = text;
    inp.focus();
    autoResize(inp);
}

function agentSend() {
    const input = document.getElementById('agentInput');
    const msg   = input.value.trim();
    if (!msg || !activeChatId) return;
    input.value = '';
    input.style.height = '';

    fetch(`{{ url('/admin/live-chat') }}/${activeChatId}/message`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ message: msg })
    }).then(() => fetchMessages());
}

function closeChat() {
    if (!activeChatId) return;
    if (!confirm('Close this support session?')) return;

    fetch(`{{ url('/admin/live-chat') }}/${activeChatId}/close`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' }
    }).then(() => {
        clearInterval(pollTimer);
        location.reload();
    });
}

function pollQueue() {
    fetch(`{{ route('admin.live-chat.queue') }}`, { headers: { 'Accept': 'application/json' } })
    .then(r => r.json())
    .then(data => {
        document.getElementById('waitingCount').textContent = data.waiting_count;
        document.getElementById('kpiWaiting').textContent   = data.waiting_count;
        document.getElementById('activeCount').textContent  = data.active_count;
    });
}

function handleKey(e) {
    if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); agentSend(); }
}
function autoResize(el) { el.style.height = 'auto'; el.style.height = Math.min(el.scrollHeight, 120) + 'px'; }

document.addEventListener('DOMContentLoaded', () => {
    if ('Notification' in window && Notification.permission === 'default') {
        Notification.requestPermission();
    }
    setInterval(pollQueue, 5000);
});
</script>
@endsection
