<?php

namespace ArtMin96\FilamentJet\Traits;

use ArtMin96\FilamentJet\Features;
use Illuminate\Support\Facades\Storage;

trait HasProfilePhoto
{
    public function getFilamentAvatarUrl(): ?string
    {
        return Features::managesProfilePhotos() ? $this->profile_photo_url : null;
    }

    /**
     * Update the user's profile photo.
     *
     * @param string|null $photo
     */
    public function updateProfilePhoto(null|string $photo): void
    {
        tap($this->profile_photo_path, function ($previous) use ($photo) {
            $this->forceFill([
                'profile_photo_path' => $photo,
            ])->save();

            if ($previous && ! $photo) {
                Storage::disk($this->profilePhotoDisk())->delete($previous);
            }
        });
    }

    /**
     * Delete the user's profile photo.
     */
    public function deleteProfilePhoto(): void
    {
        if (! Features::managesProfilePhotos()) {
            return;
        }

        if (is_null($this->profile_photo_path)) {
            return;
        }

        Storage::disk($this->profilePhotoDisk())->delete($this->profile_photo_path);

        $this->forceFill([
            'profile_photo_path' => null,
        ])->save();
    }

    /**
     * Get the URL to the user's profile photo.
     */
    public function getProfilePhotoUrlAttribute(): string
    {
        return $this->profile_photo_path
            ? Storage::disk($this->profilePhotoDisk())->url($this->profile_photo_path)
            : $this->defaultProfilePhotoUrl();
    }

    /**
     * Get the default profile photo URL if no profile photo has been uploaded.
     */
    protected function defaultProfilePhotoUrl(): string
    {
        $name = trim(collect(explode(' ', $this->name))->map(function ($segment) {
            return mb_substr($segment, 0, 1);
        })->join(' '));

        return 'https://ui-avatars.com/api/?name='.urlencode($name).'&color=7F9CF5&background=EBF4FF';
    }

    /**
     * Get the disk that profile photos should be stored on.
     */
    public function profilePhotoDisk(): string
    {
        return isset($_ENV['VAPOR_ARTIFACT_NAME']) ? 's3' : config('filament-jet.profile_photo_disk', 'public');
    }

    /**
     * Get the directory that profile photos should be stored on.
     */
    public function profilePhotoDirectory(): string
    {
        return config('filament-jet.profile_photo_directory', 'profile-photos');
    }
}
