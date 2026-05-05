<?php

declare(strict_types=1);

namespace App\Services\Verification;

use App\Enums\VerificationStatus;
use App\Models\User;
use App\Models\Verification;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class VerificationService
{
    /**
     * @param  array<int, UploadedFile>  $marketplaceScreenshots
     */
    public function submit(
        User $user,
        UploadedFile $idDocument,
        UploadedFile $selfie,
        array $marketplaceScreenshots,
        ?UploadedFile $recommendation,
        ?string $passportNumber = null,
    ): Verification {
        $passportHash = $passportNumber !== null && $passportNumber !== ''
            ? hash('sha256', mb_strtolower(preg_replace('/\s+/', '', $passportNumber) ?? ''))
            : null;

        if ($passportHash !== null && Verification::query()
            ->where('passport_number_hash', $passportHash)
            ->where('user_id', '!=', $user->id)
            ->exists()
        ) {
            throw new RuntimeException('This passport is already registered with a different account.');
        }

        return DB::transaction(function () use ($user, $idDocument, $selfie, $marketplaceScreenshots, $recommendation, $passportHash): Verification {
            $idPath = $this->storeSecure($idDocument, $user->id, 'id-document');
            $selfiePath = $this->storeSecure($selfie, $user->id, 'selfie');
            $recommendationPath = $recommendation !== null ? $this->storeSecure($recommendation, $user->id, 'recommendation') : null;

            $screenshots = [];
            foreach ($marketplaceScreenshots as $screenshot) {
                $screenshots[] = $this->storeSecure($screenshot, $user->id, 'marketplaces');
            }

            return Verification::create([
                'user_id' => $user->id,
                'status' => VerificationStatus::Pending,
                'id_document_path' => $idPath,
                'id_selfie_path' => $selfiePath,
                'marketplace_screenshots' => $screenshots,
                'recommendation_path' => $recommendationPath,
                'passport_number_hash' => $passportHash,
            ]);
        });
    }

    private function storeSecure(UploadedFile $file, int $userId, string $bucket): string
    {
        return $file->store("verifications/{$userId}/{$bucket}", 'local');
    }

    public function downloadUrl(string $path): string
    {
        return Storage::disk('local')->path($path);
    }
}
