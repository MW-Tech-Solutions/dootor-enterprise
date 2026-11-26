<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\LiveChat;
use App\Models\LiveChatMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LiveChatController extends Controller
{
    // ─── CLIENT ───────────────────────────────────────────────────────────────

    /**
     * Start or return an existing open chat for the authenticated client.
     */
    public function start(Request $request)
    {
        $request->validate(['subject' => 'nullable|string|max:255']);

        $client = Auth::user();

        // If there's already an open chat, return it
        $existing = LiveChat::where('client_id', $client->id)
            ->whereIn('status', ['waiting', 'active'])
            ->latest()
            ->first();

        if ($existing) {
            return response()->json([
                'chat_id'        => $existing->id,
                'status'         => $existing->status,
                'queue_position' => $existing->queue_position,
                'message'        => 'Reconnected to your existing chat session.',
            ]);
        }

        // Calculate queue position
        $queueCount = LiveChat::where('status', 'waiting')->count();

        $chat = LiveChat::create([
            'client_id'      => $client->id,
            'status'         => 'waiting',
            'queue_position' => $queueCount + 1,
            'subject'        => $request->subject ?? 'General Enquiry',
        ]);

        // System message
        LiveChatMessage::create([
            'chat_id'     => $chat->id,
            'sender_id'   => $client->id,
            'sender_type' => 'system',
            'message'     => '👋 Hello ' . $client->name . '! You are #' . $chat->queue_position . ' in the queue. A support agent will be with you shortly.',
        ]);

        return response()->json([
            'chat_id'        => $chat->id,
            'status'         => 'waiting',
            'queue_position' => $chat->queue_position,
            'message'        => 'Chat started. You are #' . $chat->queue_position . ' in queue.',
        ]);
    }

    /**
     * Client sends a message.
     */
    public function sendMessage(Request $request, $chatId)
    {
        $request->validate(['message' => 'required|string|max:2000']);

        $client = Auth::user();
        $chat   = LiveChat::where('id', $chatId)->where('client_id', $client->id)->firstOrFail();

        if ($chat->status === 'closed') {
            return response()->json(['error' => 'This chat session has been closed.'], 422);
        }

        $msg = LiveChatMessage::create([
            'chat_id'     => $chat->id,
            'sender_id'   => $client->id,
            'sender_type' => 'client',
            'message'     => $request->message,
        ]);

        return response()->json(['message' => $msg]);
    }

    /**
     * Client polls for new messages.
     */
    public function getMessages(Request $request, $chatId)
    {
        $client = Auth::user();
        $chat   = LiveChat::where('id', $chatId)->where('client_id', $client->id)->firstOrFail();

        $after    = $request->query('after', 0);
        $messages = LiveChatMessage::where('chat_id', $chatId)
            ->where('id', '>', $after)
            ->get()
            ->map(fn($m) => [
                'id'          => $m->id,
                'sender_type' => $m->sender_type,
                'sender_name' => $m->sender->name ?? 'Support',
                'message'     => $m->message,
                'time'        => $m->created_at->format('H:i'),
            ]);

        // Mark agent messages as read
        LiveChatMessage::where('chat_id', $chatId)
            ->where('sender_type', 'agent')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'messages'       => $messages,
            'status'         => $chat->status,
            'queue_position' => $chat->queue_position,
            'agent_name'     => $chat->agent?->name,
        ]);
    }

    /**
     * Client closes / ends the chat.
     */
    public function close(Request $request, $chatId)
    {
        $client = Auth::user();
        $chat   = LiveChat::where('id', $chatId)->where('client_id', $client->id)->firstOrFail();

        $chat->update(['status' => 'closed', 'closed_at' => now()]);

        // Rebalance queue
        LiveChat::where('status', 'waiting')->orderBy('created_at')
            ->each(function ($c, $i) { $c->update(['queue_position' => $i + 1]); });

        return response()->json(['message' => 'Chat closed. Thank you for contacting DOOTOR ENTERPRISES.']);
    }

    // ─── AGENT / ADMIN ────────────────────────────────────────────────────────

    /**
     * Agent dashboard — shows waiting + active chats.
     */
    public function agentDashboard()
    {
        $waiting = LiveChat::with(['client', 'latestMessage'])
            ->where('status', 'waiting')
            ->orderBy('queue_position')
            ->get();

        $active = LiveChat::with(['client', 'agent', 'latestMessage'])
            ->where('status', 'active')
            ->where('agent_id', Auth::id())
            ->latest()
            ->get();

        $allActive = LiveChat::with(['client', 'agent'])
            ->where('status', 'active')
            ->count();

        $closedToday = LiveChat::where('status', 'closed')
            ->whereDate('closed_at', today())
            ->count();

        $recentClosed = LiveChat::with(['client'])
            ->where('status', 'closed')
            ->latest('closed_at')
            ->take(15)
            ->get();

        return view('admin.live-chat', compact('waiting', 'active', 'allActive', 'closedToday', 'recentClosed'));
    }

    /**
     * Agent accepts a waiting chat.
     */
    public function accept(Request $request, $chatId)
    {
        $chat = LiveChat::where('id', $chatId)->where('status', 'waiting')->firstOrFail();

        $chat->update([
            'agent_id'    => Auth::id(),
            'status'      => 'active',
            'accepted_at' => now(),
        ]);

        LiveChatMessage::create([
            'chat_id'     => $chat->id,
            'sender_id'   => Auth::id(),
            'sender_type' => 'system',
            'message'     => '✅ Agent ' . Auth::user()->name . ' has joined the chat. How can we help you today?',
        ]);

        // Rebalance queue
        LiveChat::where('status', 'waiting')->orderBy('created_at')
            ->each(function ($c, $i) { $c->update(['queue_position' => $i + 1]); });

        return response()->json(['message' => 'Chat accepted.', 'chat_id' => $chat->id]);
    }

    /**
     * Agent sends a message.
     */
    public function agentSendMessage(Request $request, $chatId)
    {
        $request->validate(['message' => 'required|string|max:2000']);

        $chat = LiveChat::where('id', $chatId)->where('agent_id', Auth::id())->firstOrFail();

        if ($chat->status === 'closed') {
            return response()->json(['error' => 'Chat is closed.'], 422);
        }

        $msg = LiveChatMessage::create([
            'chat_id'     => $chat->id,
            'sender_id'   => Auth::id(),
            'sender_type' => 'agent',
            'message'     => $request->message,
        ]);

        return response()->json(['message' => $msg]);
    }

    /**
     * Agent fetches messages for a chat.
     */
    public function agentGetMessages(Request $request, $chatId)
    {
        // Agents can view waiting chats too (before accepting)
        $chat = LiveChat::findOrFail($chatId);

        $after    = $request->query('after', 0);
        $messages = LiveChatMessage::where('chat_id', $chatId)
            ->where('id', '>', $after)
            ->get()
            ->map(fn($m) => [
                'id'          => $m->id,
                'sender_type' => $m->sender_type,
                'sender_name' => $m->sender->name ?? 'Unknown',
                'message'     => $m->message,
                'time'        => $m->created_at->format('H:i'),
            ]);

        // Mark client messages as read
        LiveChatMessage::where('chat_id', $chatId)
            ->where('sender_type', 'client')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'messages'    => $messages,
            'status'      => $chat->status,
            'client_name' => $chat->client?->name,
        ]);
    }

    /**
     * Agent closes a chat.
     */
    public function agentClose(Request $request, $chatId)
    {
        $chat = LiveChat::findOrFail($chatId);
        $chat->update(['status' => 'closed', 'closed_at' => now()]);

        LiveChatMessage::create([
            'chat_id'     => $chat->id,
            'sender_id'   => Auth::id(),
            'sender_type' => 'system',
            'message'     => '🔒 This support session has been closed by the agent. Thank you for contacting DOOTOR ENTERPRISES.',
        ]);

        return response()->json(['message' => 'Chat closed.']);
    }

    /**
     * JSON: queue status (for polling).
     */
    public function queueStatus()
    {
        $waiting = LiveChat::where('status', 'waiting')->count();
        $active  = LiveChat::where('status', 'active')->count();

        $queue = LiveChat::with(['client'])
            ->where('status', 'waiting')
            ->orderBy('queue_position')
            ->get()
            ->map(fn($c) => [
                'id'             => $c->id,
                'client_name'    => $c->client?->name ?? 'Anonymous',
                'queue_position' => $c->queue_position,
                'subject'        => $c->subject,
                'waiting_since'  => $c->created_at->diffForHumans(),
            ]);

        return response()->json([
            'waiting_count' => $waiting,
            'active_count'  => $active,
            'queue'         => $queue,
        ]);
    }
}
