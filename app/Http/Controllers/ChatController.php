<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Group;
use App\Models\Message;
use App\Events\MessageSent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index()
    {
        $users = User::where('id', '!=', Auth::id())->get();
        $groups = Auth::user()->groupMemberships()->get();

        return view('chat.index', compact('users', 'groups'));
    }

    public function fetchPrivateMessages($userId)
    {
        $messages = Message::where(function($query) use ($userId) {
                $query->where('sender_id', Auth::id())
                      ->where('receiver_id', $userId);
            })
            ->orWhere(function($query) use ($userId) {
                $query->where('sender_id', $userId)
                      ->where('receiver_id', Auth::id());
            })
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }

    public function fetchGroupMessages($groupId)
    {
        // Ensure user is in the group
        if (!Auth::user()->groupMemberships()->where('group_id', $groupId)->exists()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $messages = Message::where('group_id', $groupId)
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }

    public function sendPrivateMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string',
        ]);

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
        ]);

        broadcast(new MessageSent($message));

        return response()->json($message->load('sender'));
    }

    public function sendGroupMessage(Request $request)
    {
        $request->validate([
            'group_id' => 'required|exists:groups,id',
            'message' => 'required|string',
        ]);

        // Ensure user is in the group
        if (!Auth::user()->groupMemberships()->where('group_id', $request->group_id)->exists()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $message = Message::create([
            'sender_id' => Auth::id(),
            'group_id' => $request->group_id,
            'message' => $request->message,
        ]);

        broadcast(new MessageSent($message));

        return response()->json($message->load('sender'));
    }

    public function createGroup(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'users' => 'required|array',
            'users.*' => 'exists:users,id'
        ]);

        $group = Group::create([
            'name' => $request->name,
            'created_by' => Auth::id(),
        ]);

        // Add creator and selected users
        $userIds = collect($request->users)->push(Auth::id())->unique()->toArray();
        $group->members()->attach($userIds);

        return response()->json($group);
    }
}
