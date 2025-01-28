<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Http\Request;

class ChatMessageController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        ChatMessage::create($request->toArray());

        $receiver = User::find($request->user_id);
        $sender = User::find($request->from);

        broadcast(new MessageSent($receiver, $sender, $request->message));

        return response()->json(['status' => true, 'data' => 'Mensaje enviado exitosamente']);
    }

    /**
     * Get the messages for the user along with messages count.
     */
    public function getUnreadMessages(Request $request,)
    {
        $messages = ChatMessage::with('from')->where('user_id', $request->user_id)
            ->get();
        return response()->json(['status' => true, 'data' => []]);
    }
    public function getChatMessages(Request $request, User $from)
    {
        $user = $request->user();
        $messages = ChatMessage::select('id', 'from', 'user_id', 'message', 'created_at')
            ->where([['user_id', '=', $user->id], ['from', '=', $from->id]])
            ->orWhere([['user_id', '=', $from->id], ['from', '=', $user->id]])
            ->orderBy('created_at', 'ASC')
            ->get();
        return response()->json(['status' => true, 'data' => $messages]);
    }
}
