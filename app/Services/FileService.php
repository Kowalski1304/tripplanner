<?php

namespace App\Services;

use App\Models\File;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileService
{
    private const AVATAR_DISK = 'private';
    private const AVATAR_DIRECTORY = 'avatars';
    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    private const MAX_FILE_SIZE = 10240; // 10MB

    public function handleAvatarUpload(User $user, ?UploadedFile $file): ?File
    {
        if (!$file) {
            return null;
        }

        $this->validateFile($file);

        // Удаляем старый аватар, если он существует
        $this->deleteUserAvatar($user);

        // Сохраняем новый файл
        $path = $this->storeFile($file);

        // Создаем запись в базе данных
        return $this->createFileRecord($user, $file, $path);
    }

    public function deleteUserAvatar(User $user): void
    {
        if ($user->file) {
            Storage::disk(self::AVATAR_DISK)->delete($user->file->path);
            $user->file->delete();
        }
    }

    private function validateFile(UploadedFile $file): void
    {
        if (!$file->isValid()) {
            throw new \InvalidArgumentException('Invalid file upload');
        }

        if ($file->getSize() > self::MAX_FILE_SIZE * 1024) {
            throw new \InvalidArgumentException('File size exceeds maximum allowed size of ' . self::MAX_FILE_SIZE . 'KB');
        }

        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, self::ALLOWED_EXTENSIONS)) {
            throw new \InvalidArgumentException('Invalid file type. Allowed types: ' . implode(', ', self::ALLOWED_EXTENSIONS));
        }
    }

    private function storeFile(UploadedFile $file): string
    {
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        return $file->storeAs(self::AVATAR_DIRECTORY, $filename, self::AVATAR_DISK);
    }

    private function createFileRecord(User $user, UploadedFile $file, string $path): File
    {
        return $user->file()->create([
            'name' => $file->getClientOriginalName(),
            'path' => $path,
            'type' => 'avatar',
            'size' => $file->getSize(),
        ]);
    }

    public function getAvatarUrl(?File $file): ?string
    {
        if (!$file || !Storage::disk(self::AVATAR_DISK)->exists($file->path)) {
            return null;
        }

        return route('private.file', ['path' => $file->path]);
    }
} 