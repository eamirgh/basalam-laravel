<?php

declare(strict_types=1);

namespace Eamirgh\Basalam\Services;

use Eamirgh\Basalam\DTOs\UserData;
use Eamirgh\Basalam\Http\ApiClient;

class UserService
{
    public function __construct(
        protected ApiClient $client,
        protected ApiClient $coreClient,
    ) {
    }

    /**
     * Get currently authenticated user and associated vendor
     */
    public function me(): UserData
    {
        $response = $this->client->get('/v1/users/me');

        return UserData::fromArray($response);
    }

    /**
     * Send mobile verification OTP code
     *
     * @return array<string, mixed>
     */
    public function requestMobileVerification(int|string $userId, string $mobile): array
    {
        return $this->client->post("/v1/users/{$userId}/mobile-verification-requests", [
            'mobile' => $mobile,
        ]);
    }

    /**
     * Confirm mobile verification code
     *
     * @return array<string, mixed>
     */
    public function confirmMobileVerification(int|string $userId, string $code): array
    {
        return $this->client->post("/v1/users/{$userId}/mobile-verification-confirmations", [
            'code' => $code,
        ]);
    }

    /**
     * Get bank accounts for user
     *
     * @return array<string, mixed>
     */
    public function getBankAccounts(int|string $userId): array
    {
        return $this->coreClient->get("/v3/users/{$userId}/bank-information");
    }

    /**
     * Add bank account for user
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function addBankAccount(int|string $userId, array $data): array
    {
        return $this->coreClient->post("/v3/users/{$userId}/bank-information", $data);
    }
}
