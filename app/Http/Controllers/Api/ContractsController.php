<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContractResource;
use App\Models\Contract;
use App\Services\Contracts\ContractService;
use App\Services\Reviews\DisputeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use RuntimeException;

class ContractsController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $userId = $request->user()->id;

        return ContractResource::collection(
            Contract::query()
                ->where(fn ($q) => $q->where('buyer_id', $userId)->orWhere('seller_id', $userId))
                ->latest()
                ->paginate(20),
        );
    }

    public function show(Contract $contract, Request $request): ContractResource
    {
        $this->ensureParty($contract, $request);

        return new ContractResource($contract);
    }

    public function submit(Contract $contract, Request $request, ContractService $service): ContractResource
    {
        $this->run(fn () => $service->submit($contract, $request->user()));

        return new ContractResource($contract->fresh());
    }

    public function approve(Contract $contract, Request $request, ContractService $service): ContractResource
    {
        $this->run(fn () => $service->approve($contract, $request->user()));

        return new ContractResource($contract->fresh());
    }

    public function revision(Contract $contract, Request $request, ContractService $service): ContractResource
    {
        $this->run(fn () => $service->requestRevision($contract, $request->user()));

        return new ContractResource($contract->fresh());
    }

    public function dispute(Contract $contract, Request $request, DisputeService $service): JsonResponse
    {
        $this->ensureParty($contract, $request);

        $data = $request->validate([
            'reason_code' => ['required', 'in:quality,deadline,communication,scope,other'],
            'description' => ['required', 'string', 'min:30', 'max:2000'],
        ]);

        $this->run(fn () => $service->open($contract, $request->user(), $data));

        return response()->json(['ok' => true]);
    }

    private function ensureParty(Contract $contract, Request $request): void
    {
        $userId = $request->user()->id;
        abort_unless(in_array($userId, [$contract->buyer_id, $contract->seller_id], true), 403);
    }

    private function run(\Closure $action): void
    {
        try {
            $action();
        } catch (RuntimeException $e) {
            abort(422, $e->getMessage());
        }
    }
}
