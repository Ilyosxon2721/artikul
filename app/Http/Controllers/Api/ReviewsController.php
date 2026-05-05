<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Services\Reviews\ReviewService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;

class ReviewsController extends Controller
{
    public function store(Contract $contract, Request $request, ReviewService $service): JsonResponse
    {
        $userId = $request->user()->id;
        abort_unless(in_array($userId, [$contract->buyer_id, $contract->seller_id], true), 403);

        $data = $request->validate([
            'rating_overall' => ['required', 'integer', 'between:1,5'],
            'rating_quality' => ['nullable', 'integer', 'between:1,5'],
            'rating_communication' => ['nullable', 'integer', 'between:1,5'],
            'rating_deadline' => ['nullable', 'integer', 'between:1,5'],
            'rating_professionalism' => ['nullable', 'integer', 'between:1,5'],
            'rating_clarity' => ['nullable', 'integer', 'between:1,5'],
            'rating_payment_timeliness' => ['nullable', 'integer', 'between:1,5'],
            'comment' => ['required', 'string', 'min:30', 'max:1500'],
            'would_work_again' => ['nullable', 'in:yes,no,maybe'],
        ]);

        try {
            $review = $service->leave($contract, $request->user(), $data);
        } catch (RuntimeException $e) {
            abort(422, $e->getMessage());
        }

        return response()->json([
            'id' => $review->id,
            'is_visible' => $review->is_visible,
        ], 201);
    }
}
