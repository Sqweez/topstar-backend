<?php

namespace App\Console\Commands\Utils;

use App\Http\Services\PassService;
use Carbon\Carbon;
use GuzzleHttp\Exception\GuzzleException;
use http\Client;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileCannotBeAdded;

class ImportClients extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:clients';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Импортируем клиентов из старой базы';

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
     * @return void
     * @throws GuzzleException
     * @throws FileCannotBeAdded
     */
    public function handle()
    {
        \App\Models\Client::truncate();
        $file = new Filesystem();
        $file->cleanDirectory('storage/app/public');

        collect($this->getClients())->each(function ($client) {
            $this->line('Клиент: ' . $client->name);
            $_client = \App\Models\Client::create([
                'id' => $client->id,
                'name' => $client->name,
                'phone' => mask_phone_old($client->phone),
                'birth_date' => Carbon::parse($client->datarozhd),
                'description' => $client->comment,
                'balance' => $client->balans,
                'club_id' => str_replace('club', '', $client->club),
                'user_id' => null,
                'created_at' => Carbon::parse($client->datarega),
                'gender' => intval($client->pol) === 1 ? 'M' : 'F'
            ]);

            $pass = PassService::createPass($client->cardid);
            $_client->pass()->save($pass);
            if ($client->photo) {
                $this->line($client->photo);
                try {
                    $_client
                        ->addMediaFromUrl(sprintf("http://top-star.kz/photos/%s", $client->photo))
                        ->toMediaCollection(\App\Models\Client::MEDIA_AVATAR);;
                } catch (\Exception $exception) {
                    \Log::error($exception->getMessage());
                }
            }
        });
    }

    private function getClients() {
        $client = new \GuzzleHttp\Client();
        $response = $client->get('http://top-star.kz/export/export_clients.php');
        return json_decode($response->getBody());
    }
}
