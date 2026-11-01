<?php

declare(strict_types=1);

namespace Eamirgh\Basalam\Services;

use Eamirgh\Basalam\Http\ApiClient;

class FileUploadService
{
    public function __construct(
        protected ApiClient $client,
    ) {
    }

    /**
     * Upload a file to Basalam storage
     *
     * @param string|resource $file File path, raw contents, or resource
     * @param string $fileType e.g. 'product.photo'
     * @param string|null $filename File name
     * @return array<string, mixed>
     */
    public function upload(
        mixed $file,
        string $fileType = 'product.photo',
        ?string $filename = null,
    ): array {
        $contents = $file;
        $name = 'file';

        if (is_string($file) && file_exists($file)) {
            $contents = fopen($file, 'r');
            $filename ??= basename($file);
        }

        $filename ??= 'upload.jpg';

        return $this->client->attach(
            path: '/v1/files',
            name: $name,
            contents: $contents,
            filename: $filename,
            data: [
                'file_type' => $fileType,
            ],
        );
    }

    /**
     * Upload product photo and return photo ID
     *
     * @param string|resource $file
     */
    public function uploadProductPhoto(mixed $file, ?string $filename = null): int|string
    {
        $response = $this->upload($file, 'product.photo', $filename);

        return $response['id'] ?? 0;
    }
}
