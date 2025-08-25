<?php

namespace App\Http\Controllers\Api\Chat;

use App\Models\User;
use App\Models\Message;
use App\Traits\ApiResponse;
use App\Models\Conversation;
use Illuminate\Http\Request;
use App\Events\MessageSentEvent;
use App\Events\ConversationEvent;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class SendMessageController extends Controller
{
    use ApiResponse;

    /**
     * Send a message to a specific user.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request)
    {
        // Validate the request
        if ($this->validate($request) !== true) {
            return $this->validate($request);
        }

        $user = auth()->user();
        if (!$user) {
            return $this->error([], 'Unauthorized', 401);
        }

        $receiver_id = null;
        if ($request->receiver_id) {
            $receiver = User::find($request->receiver_id);
            if (!$receiver) {
                return $this->error([], 'Receiver not found', 404);
            }
            $receiver_id = $receiver->id;
        }

        $conversation_id = $request->get('conversation_id');
        $message = $request->get('message');
        $reply_to_message_id = $request->get('reply_to_message_id');
        $conversation = null;
        $messageType = null;
        
        if ($message) {
            $messageType = 'text';
        }
        
        // Conversation logic
        $conversation = $this->getConversation($user, $receiver_id, $conversation_id);
        
        if (!$conversation) {
            return $this->error([], 'Conversation not found', 404);
        } else {
            if ($conversation->type == 'private' && !$receiver_id) {
                return $this->error([], 'Receiver ID is required for private conversations', 422);
            }
        }

        // Create the message
        if ($message) {
            $messageData = [
                'sender_id' => $user->id,
                'receiver_id' => $receiver_id,
                'conversation_id' => $conversation->id,
                'message' => $message,
                'message_type' => $messageType,
                'reply_to_message_id' => $reply_to_message_id ? $reply_to_message_id : null,
            ];
        }
        
        $messageSend = Message::create($messageData);
        $conversation->touch();
        
        $messageSend->load(['parentMessage', 'sender:id,name,avatar', 'receiver:id,name,avatar']);

        // return ($messageSend);
        # Broadcast the message
        broadcast(new MessageSentEvent($messageSend));
        

        # Broadcast the Conversation and Unread Message Count
        broadcast(new ConversationEvent($messageSend->sender_id, $messageSend->receiver_id, $messageSend))->toOthers();

        return $this->success([
            'message' => $messageSend,
            'conversation' => $conversation,
        ], 'Message sent successfully', 201);
    }

    /**
     * Validate the request data.
     *
     * @param Request $request
     * @return bool|\Illuminate\Http\JsonResponse
     */
    private function validate($request)
    {
        $validator = Validator::make($request->all(), [
            'receiver_id' => ['nullable', 'required_without:conversation_id', 'integer'],
            'conversation_id' => ['nullable', 'required_without:receiver_id', 'integer'],
            'message' => ['string', 'required_without:file', 'max:1000'],
            'reply_to_message_id' => ['nullable', 'exists:messages,id'],
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), $validator->errors()->first(), 422);
        } else {
            return true;
        }
    }

    /**
     * Get or create a conversation based on the provided parameters.
     *
     * @param User $user
     * @param int|null $receiver_id
     * @param int|null $conversation_id
     * @return Conversation|null
     */
    private function getConversation(User $user, $receiver_id = null, $conversation_id = null)
    {
        if ($conversation_id) {
            $conversation = Conversation::where('id', $conversation_id)
                ->whereHas('participants', function ($query) use ($user) {
                    $query->where('participant_id', $user->id)
                        ->where('participant_type', User::class);
                })
                ->where('type', 'group')
                ->first();
                
                // dd($conversation);
                if (!$conversation) {
                    return false;
                } else {
                    return $conversation;
                }
            } elseif ($receiver_id) {
                $receiver = User::find($receiver_id);
                if (!$receiver) {
                    return $this->error([], 'Receiver not found', 404);
                }
                
                if ($receiver->id === $user->id) {
                    $conversation = Conversation::whereHas('participants', function ($q) use ($user) {
                        $q->where('participant_id', $user->id)
                        ->where('participant_type', User::class);
                    })
                    ->where('type', 'self')
                    ->first();
                    
                if (!$conversation) {
                    $conversation = Conversation::create([
                        'type' => 'self',
                    ]);

                    $conversation->participants()->createMany([
                        [
                            'participant_id' => $user->id,
                            'participant_type' => User::class,
                        ],
                    ]);
                    return $conversation;
                } else {
                    return $conversation;
                }
            }

            $conversation = Conversation::whereHas('participants', function ($q) use ($user) {
                $q->where('participant_id', $user->id)
                    ->where('participant_type', User::class);
            })
                ->whereHas('participants', function ($q) use ($receiver) {
                    $q->where('participant_id', $receiver->id)
                        ->where('participant_type', User::class);
                })
                ->where('type', 'private')
                ->first();

            if (!$conversation) {
                $conversation = Conversation::create([
                    'type' => 'private',
                ]);

                $conversation->participants()->createMany([
                    [
                        'participant_id' => $receiver->id,
                        'participant_type' => User::class,
                    ],
                    [
                        'participant_id' => $user->id,
                        'participant_type' => User::class,
                    ],
                ]);
                return $conversation;
            } else {
                return $conversation;
            }
        } else {
            return $this->error([], 'Either receiver_id or conversation_id is required', 422);
        }
    }
}
