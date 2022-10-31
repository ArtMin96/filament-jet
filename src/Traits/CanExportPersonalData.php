<?php

namespace ArtMin96\FilamentJet\Traits;

use Illuminate\Support\Str;
use Spatie\PersonalDataExport\PersonalDataSelection;

trait CanExportPersonalData
{
    public function personalDataExportName(): string
    {
        $userName = Str::slug($this->name);

        return "personal-data-{$userName}.zip";
    }

    public function selectPersonalData(PersonalDataSelection $personalData): void
    {
        $personalData
            ->add('user.json', ['name' => $this->name, 'email' => $this->email])
            ->addFile(storage_path("app/{$this->profilePhotoDisk()}/{$this->profile_photo_path}"));
    }
}
