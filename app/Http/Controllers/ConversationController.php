<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
//use http\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConversationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        return view('conversation.index');
    }

    public function getConversations()
    {
        $conversations = auth()->user()->conversations()
            ->with(['firstUser', 'secondUser'])
            ->orderByDesc('updated_at')
            ->get();

        $userID = auth()->id();

        $conversations = $conversations->map(function ($conversation) use ($userID) {
            $otherUser = $conversation->first_user_id == $userID ? $conversation->secondUser : $conversation->firstUser;
            $notification = Message::where('conversation_id', $conversation->id)
                                    ->where('receiver_id', auth()->id())
                                    ->where('is_read', false)
                                    ->count();

            return [
                'id' => $conversation->id,
                'user' =>  $otherUser,
                'last_updated' => $conversation->updated_at,
                'notification' => $notification,
            ];
        });

        return response()->json($conversations);
    }

    public function checkOrCreate(Request $request)
    {
        $userID = auth()->id();
        $otherUserID = $request->input('user_id');

        $conversation = Conversation::where(function ($query) use ($userID, $otherUserID) {
            $query->where('first_user_id', $userID)
                  ->where('second_user_id', $otherUserID);
        })
        ->orWhere(function ($query) use ($userID, $otherUserID) {
            $query->where('first_user_id', $otherUserID)
                  ->where('second_user_id', $userID);
        })
        ->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'first_user_id' => $userID,
                'second_user_id' => $otherUserID,
            ]);
        }
        return response()->json($conversation);
    }

    public function show($id)
    {
        $userId = auth()->id();
        $conversation = Conversation::findOrFail($id);
        if ($conversation->first_user_id == $userId) {
            $friendName = $conversation->secondUser->name;
        } else {
            $friendName = $conversation->firstUser->name;
        }

        return view('conversation.show', compact('conversation', 'friendName', 'userId'));
    }

    public function getMessages($conversationId)
    {
        $conversation = Conversation::findOrFail($conversationId);
        $messages = $conversation->messages()->with('user')->orderBy('created_at', 'asc')->get();
        return response()->json($messages);
    }

    public function sendMessage(Request $request, $conversationId)
    {
        $request->validate([
            'content' => 'required|max:1000',
        ]);

        $conversation = Conversation::findOrFail($conversationId);

        $message = Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => auth()->id() == $conversation->first_user_id
                ? $conversation->second_user_id
                : $conversation->first_user_id,
            'conversation_id' => $conversationId,
            'content' => $request->input('content'),
        ]);

        event(new \App\Events\MessageSent($message));

        return response()->json($message);
    }

    public function changeStatus($conversationId) {
         Message::where('conversation_id', $conversationId)
                ->where('receiver_id', auth()->id())
                ->where('is_read', false)
                ->update(['is_read' => true]);
    }
}
