<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use App\Models\SupportTicket;
use App\Models\TicketMessage;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    /**
     * Display Support Tickets for Client.
     */
    public function index()
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            $tickets = SupportTicket::with(['user', 'serviceRequest', 'assignedStaff'])->latest()->get();
            return view('admin.support', compact('tickets'));
        }

        $tickets = SupportTicket::where('user_id', $user->id)->with(['serviceRequest'])->latest()->get();
        $requests = ServiceRequest::where('client_id', $user->id)->get();

        return view('client.support', compact('tickets', 'requests'));
    }

    /**
     * Store new support ticket.
     */
    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'category' => 'required|string',
            'message' => 'required|string',
            'service_request_id' => 'nullable|exists:service_requests,id',
        ]);

        $ticket = SupportTicket::create([
            'user_id' => auth()->id(),
            'service_request_id' => $request->service_request_id,
            'subject' => $request->subject,
            'category' => $request->category,
            'status' => 'Open',
            'priority' => 'Normal',
        ]);

        TicketMessage::create([
            'support_ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'message' => $request->message,
        ]);

        return back()->with('success', 'Support ticket ' . $ticket->ticket_number . ' created successfully!');
    }

    /**
     * View ticket details and messages.
     */
    public function show(SupportTicket $ticket)
    {
        $user = auth()->user();

        if (!$user->isAdmin() && $ticket->user_id !== $user->id) {
            abort(403, 'Unauthorized access to support ticket.');
        }

        $ticket->load(['user', 'serviceRequest', 'messages.user']);

        return view('support.show', compact('ticket'));
    }

    /**
     * Reply to support ticket.
     */
    public function reply(Request $request, SupportTicket $ticket)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        TicketMessage::create([
            'support_ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'message' => $request->message,
        ]);

        if (auth()->user()->isAdmin()) {
            $ticket->update(['status' => 'In Progress']);
        }

        return back()->with('success', 'Reply posted successfully.');
    }
}
