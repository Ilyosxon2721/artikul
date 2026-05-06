<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContractResource;
use App\Http\Resources\ProposalResource;
use App\Models\Proposal;
use App\Models\Task;
use App\Notifications\NewProposalNotification;
use App\Services\Contracts\ProposalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use RuntimeException;

class ProposalsController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        return ProposalResource::collection(
            Proposal::query()
                ->where('seller_id', $request->user()->id)
                ->latest()
                ->paginate(20),
        );
    }

    public function store(Task $task, Request $request, ProposalService $service): ProposalResource
    {
        $data = $request->validate([
            'cover_letter' => ['required', 'string', 'min:30', 'max:2000'],
            'proposed_amount' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable', 'in:UZS,RUB,KZT,USD'],
            'rate_type' => ['nullable', 'in:fixed,hourly'],
            'proposed_deadline_days' => ['nullable', 'integer', 'min:1', 'max:365'],
            'portfolio_item_ids' => ['nullable', 'array'],
        ]);

        try {
            $proposal = $service->create($task, $request->user(), $data);
        } catch (RuntimeException $e) {
            abort(422, $e->getMessage());
        }

        $task->buyer?->notify(new NewProposalNotification($proposal));

        return new ProposalResource($proposal);
    }

    public function destroy(Proposal $proposal, Request $request, ProposalService $service): JsonResponse
    {
        abort_unless($proposal->seller_id === $request->user()->id, 403);

        try {
            $service->withdraw($proposal);
        } catch (RuntimeException $e) {
            abort(422, $e->getMessage());
        }

        return response()->json(['ok' => true]);
    }

    public function accept(Proposal $proposal, Request $request, ProposalService $service): ContractResource
    {
        abort_unless($proposal->task?->buyer_id === $request->user()->id, 403);

        try {
            $contract = $service->accept($proposal);
        } catch (RuntimeException $e) {
            abort(422, $e->getMessage());
        }

        return new ContractResource($contract);
    }
}
