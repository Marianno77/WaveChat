<?php

namespace App\Http\Controllers;

use App\Models\Friend;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FriendController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        return view('friend.index');
    }

    public function allUsers()
    {
        $friendsID = auth()->user()->friends()->pluck('friend_id')
                    ->merge(auth()->user()->friendOf()->pluck('user_id'));
        $users = User::whereNotIn('id', $friendsID)
                ->where('id', '!=', auth()->id())
                ->get();

        return response()->json($users);
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $friendID = $request->input('friend_id');

        $user->friends()->syncWithoutDetaching([$friendID => ['status' => 'pending']]);
        return response()->json(['success' => true, 'message' => 'Zaproszenie zostało wysłane']);
    }

    public function getFriends()
    {
        $user = auth()->user();
        $friends1 = $user->friends()->wherePivot('status','accepted')->get();
        $friends2 = $user->friendOf()->wherePivot('status','accepted')->get();
        $friends = $friends1->merge($friends2)->unique('id')->values();
        return response()->json($friends);
    }

    public function getInvitations()
    {
        $invitations = \DB::table('friends')
            ->join('users', 'users.id', '=', 'friends.user_id')
            ->where('friends.friend_id', auth()->id())
            ->where('friends.status', 'pending')
            ->select('users.id as user_id', 'users.name', 'friends.id as friendship_id', 'users.avatar')
            ->get();

        return response()->json($invitations);
    }

    public function getBlockList()
    {
        $blockList = \DB::table('friends')
            ->join('users', 'users.id', '=', 'friends.friend_id')
            ->where('friends.user_id', auth()->id())
            ->where('friends.status', 'blocked')
            ->select('users.id as user_id', 'users.name', 'friends.id as friendship_id', 'users.avatar')
            ->get();

        return response()->json($blockList);
    }

    public function acceptFriend(Request $request)
    {
        $friendId = $request->input('friend_id');

        $invitation = \DB::table('friends')
            ->where('id', $friendId)
            ->where('friend_id', auth()->id())
            ->where('status', 'pending')
            ->first();

        if (!$invitation) {
            return response()->json(['success' => false, 'message' => 'Zaproszenie nie istnieje lub już zostało zaakceptowane.'], 404);
        }

        \DB::table('friends')
            ->where('id', $friendId)
            ->update(['status' => 'accepted']);

        return response()->json(['success' => true, 'message' => 'Zaproszenie zostało zaakceptowane.']);
    }

    public function block(Request $request)
    {
        $friendshipId = $request->input('friendship_id');

        $friendship = \DB::table('friends')
            ->where('id', $friendshipId)
            ->first();

        if (!$friendship) {
            return response()->json(['success' => false, 'message' => 'Relacja nie istnieje.'], 404);
        }

        $authID = auth()->id();
        if ($friendship->user_id == $authID) {
            \DB::table('friends')->where('id', $friendshipId)->update(['status' => 'blocked']);
        } elseif ($friendship->friend_id == $authID) {
            \DB::table('friends')->insert([
                'user_id' => $authID,
                'friend_id' => $friendship->user_id,
                'status' => 'blocked',
            ]);
            \DB::table('friends')->where('id', $friendshipId)->delete();
        } else {
            return response()->json(['success' => false, 'message' => 'Nie masz dostępu do tej relacji.'], 403);
        }

        return response()->json(['success' => true, 'message' => 'Użytkownik został zablokowany.']);
    }

    public function delete(Request $request)
    {
        $friendshipId = $request->input('friendship_id');

        $friendship = \DB::table('friends')
            ->where('id', $friendshipId)
            ->first();

        if (!$friendship) {
            return response()->json(['success' => false, 'message' => 'Relacja nie istnieje.'], 404);
        }

        \DB::table('friends')
            ->where('id', $friendshipId)
            ->delete();

        return response()->json(['success' => true, 'message' => 'Znajomośc została usunięta']);
    }
}
