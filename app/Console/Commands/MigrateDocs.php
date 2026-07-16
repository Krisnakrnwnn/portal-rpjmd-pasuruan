<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MigrateDocs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:migrate-docs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $docs = \App\Models\PublicDocument::all();
        foreach ($docs as $doc) {
            if (!$doc->document_category_id) {
                $cat = \App\Models\DocumentCategory::where('name', $doc->category)
                    ->orWhere('slug', \Illuminate\Support\Str::slug($doc->category))
                    ->first();
                if ($cat) {
                    $doc->document_category_id = $cat->id;
                    $doc->save();
                    $this->info("Updated {$doc->title}");
                }
            }
        }
    }
}
