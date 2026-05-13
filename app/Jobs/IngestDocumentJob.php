<?php

namespace App\Jobs;

use App\Models\DocumentIngestion;
use App\Services\DocumentIngestor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class IngestDocumentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $ingestion;
    
    public $timeout = 3600; // 1 hour

    /**
     * Create a new job instance.
     */
    public function __construct(DocumentIngestion $ingestion)
    {
        $this->ingestion = $ingestion;
    }

    /**
     * Execute the job.
     */
    public function handle(DocumentIngestor $ingestor): void
    {
        $ingestor->ingest($this->ingestion);
    }
}
