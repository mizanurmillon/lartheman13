<?php

namespace App\Http\Controllers\Api\Chat;

use App\Traits\ApiResponse;
use App\Models\Conversation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GetConversationController extends Controller
{
    use ApiResponse;
    
    public function __invoke()
    {
        $user = auth()->user();

        if (!$user) {
            return $this->error([], 'Unauthorized', 401);
        }

        $name = request()->query('name') ?? null;

        $user = auth()->user();

        $conversations = Conversation::query()
            ->with([
                'participants' => function ($query) use ($user, $name) {
                    $query->where('participant_id', '!=', $user->id)
                        ->where('participant_type', get_class($user))
                        ->with(['participant' => function ($q) use ($name) {
                            $q->select('id', 'name', 'avatar');
                        }])
                        ->take(3);
                },
                'lastMessage',
                'group'
            ])
            ->whereHas('participants', function ($query) use ($user) {
                $query->where('participant_type', get_class($user))
                    ->where('participant_id', $user->id);
            })
            ->latest('updated_at')
            ->paginate(15);

        $response = [
            'total_conversations' => $conversations->count(),
            'self' => $user->only(['id', 'name', 'avatar']),
            'conversations' => $conversations,
        ];

        return $this->success($response, 'Conversations fetched successfully.', 200);
    }
}
