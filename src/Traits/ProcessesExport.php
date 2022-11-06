<?php

namespace ArtMin96\FilamentJet\Traits;

use ArtMin96\FilamentJet\Jobs\CreatePersonalDataExportJob;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;

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
        $this->exportProgress = $this->exportBatch->progress();
    }
}
