<?php

namespace ArtMin96\FilamentJet\Traits;

use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Spatie\PersonalDataExport\Jobs\CreatePersonalDataExportJob;

trait ProcessesExport
{
    public $exportBatchId = null;

    public $exportProgress = 0;

    /**
     * @throws \Throwable
     */
    public function export()
    {
        $batch = Bus::batch(new CreatePersonalDataExportJob($this->user))
            ->name('export personal data')
            ->allowFailures()
            ->dispatch();

        $this->exportBatchId = $batch->id;
    }

    public function getExportBatchProperty(): ?Batch
    {
        if (! $this->exportBatchId) {
            return null;
        }

        return Bus::findBatch($this->exportBatchId);
    }

    public function updateExportProgress()
    {
        $this->exportProgress = $this->exportBatchId->progress();
    }
}
