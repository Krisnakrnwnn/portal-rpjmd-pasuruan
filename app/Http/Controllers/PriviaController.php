<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\ConversationMessage;
use App\Services\AI\Exceptions\AIProviderException;
use App\Services\PriviaConversationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PriviaController extends Controller
{
    public function index(?int $conversation = null)
    {
        if ($conversation !== null) {
            $this->ownedConversation($conversation);
        }

        return view('privia.index', ['activeConversationId' => $conversation]);
    }

    public function conversations(Request $request): JsonResponse
    {
        $items = $request->user()->conversations()
            ->latest('updated_at')
            ->get(['id', 'title', 'created_at', 'updated_at']);

        return response()->json(['conversations' => $items]);
    }

    public function show(Request $request, int $conversation): JsonResponse
    {
        $conversationModel = $this->ownedConversation($conversation);

        return response()->json([
            'conversation' => $conversationModel->only(['id', 'title', 'created_at', 'updated_at']),
            'messages' => $conversationModel->messages()
                ->oldest('id')
                ->get(['id', 'role', 'content', 'metadata', 'created_at']),
        ]);
    }

    public function storeMessage(Request $request, PriviaConversationService $service): JsonResponse
    {
        $data = $request->validate([
            'message' => ['required', 'string', 'max:10000'],
            'conversation_id' => ['nullable', 'integer'],
        ]);
        $conversation = isset($data['conversation_id'])
            ? $this->ownedConversation((int) $data['conversation_id'])
            : $request->user()->conversations()->create([
                'title' => Str::limit(trim($data['message']), 70, '…'),
            ]);

        try {
            $result = $service->send($request->user(), $conversation, $data['message']);
        } catch (AIProviderException $exception) {
            return response()->json([
                'message' => $exception->previewMessage(),
                'conversation_id' => $conversation?->id,
                'user_message_id' => $conversation?->messages()->latest('id')->where('role', 'user')->value('id'),
            ], $exception->previewStatus());
        } catch (\Throwable) {
            report(new \RuntimeException('PRivIA conversation generation failed.'));

            return response()->json([
                'message' => 'Gagal mendapatkan jawaban. Silakan coba lagi.',
                'conversation_id' => $conversation?->id,
                'user_message_id' => $conversation?->messages()->latest('id')->where('role', 'user')->value('id'),
            ], 502);
        }

        return $this->messageResponse($result);
    }

    public function retryMessage(Request $request, PriviaConversationService $service): JsonResponse
    {
        $data = $request->validate(['message_id' => ['required', 'integer']]);
        $message = ConversationMessage::query()
            ->whereKey($data['message_id'])
            ->where('role', 'user')
            ->whereHas('conversation', fn ($query) => $query->where('user_id', $request->user()->id))
            ->firstOrFail();

        try {
            return $this->messageResponse($service->retry($message));
        } catch (AIProviderException $exception) {
            return response()->json(['message' => $exception->previewMessage()], $exception->previewStatus());
        } catch (\Throwable) {
            report(new \RuntimeException('PRivIA conversation retry failed.'));

            return response()->json(['message' => 'Gagal mendapatkan jawaban. Silakan coba lagi.'], 502);
        }
    }

    public function rename(Request $request, int $conversation): JsonResponse
    {
        $data = $request->validate(['title' => ['required', 'string', 'max:120']]);
        $model = $this->ownedConversation($conversation);
        $model->update(['title' => trim($data['title'])]);

        return response()->json(['conversation' => $model->only(['id', 'title', 'updated_at'])]);
    }

    public function destroy(Request $request, int $conversation): JsonResponse
    {
        $this->ownedConversation($conversation)->delete();

        return response()->json(['success' => true]);
    }

    private function ownedConversation(int $id): Conversation
    {
        return request()->user()->conversations()->whereKey($id)->firstOrFail();
    }

    private function messageResponse(array $result): JsonResponse
    {
        $assistant = $result['assistant'];

        return response()->json([
            'conversation' => $result['conversation']->only(['id', 'title', 'created_at', 'updated_at']),
            'user_message' => $result['userMessage']->only(['id', 'role', 'content', 'created_at']),
            'assistant_message' => $assistant->only(['id', 'role', 'content', 'metadata', 'created_at']),
        ]);
    }
}
