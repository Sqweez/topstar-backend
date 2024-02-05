<?php

namespace App\Console\Commands\Export;

use App\Models\Client;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ExportClientPhoto extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'export:photo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        ini_set('memory_limit', '512M');
        Client::query()
            ->has('avatar')
            ->with('avatar')
            ->with('registrar:id,name')
            ->withTrashed()
            ->chunk(100, function ($clients) {
                $mappedClients = $clients->map(function (Client $client) {
                    $this->line($client->name);
                    $this->line($client->id . '.' . explode('.', $client->avatar->first()->file_name)[1]);
                    return [
                        $client->id,
                        $client->id . '.' . explode('.', $client->avatar->first()->file_name)[1]
                    ];
                })->toArray();
            });

        return 0;
    }
}
