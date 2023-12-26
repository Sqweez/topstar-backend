<?php

namespace App\Console\Commands\Export;

use App\Models\Client;
use Carbon\Carbon;
use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExportClientsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'export:clients';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export clients to XLSX';

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
     * @throws Exception
     */
    public function handle()
    {
        $template = IOFactory::load('excel/Импорт_клиенты.xlsx');
        $currentSheet = $template->getActiveSheet();
        $mappedClients = [];
        $clients = Client::query()
            ->withTrashed()
            ->with('registrar:id,name')
            ->chunk(100, function ($clients) use (&$mappedClients) {
                $clients->each(function (Client $client) use (&$mappedClients) {
                    $mappedClients[] = [
                        'id' => $client->id,
                        'phone' => $client->phone,
                        'client_fio' => $client->name,
                        'create_date' => Carbon::parse($client->created_at)->format('d.m.Y'),
                        'birth_date' => Carbon::parse($client->birth_date)->format('d.m.Y'),
                        'sex' => $client->gender === 'F' ? 'женский' : 'мужской',
                        'email' => '',
                        'passport_number' => '',
                        'passport_info' => '',
                        'passport_date' => '',
                        'address' => '',
                        'ad_source' => '',
                        'comment' => $client->description,
                        'tags' => '',
                        'manager' => $client->registrar->name,
                        'deposit' => $client->balance . "",
                        'is_archive' => !is_null($client->deleted_at) ? 1 : 0,
                        'card' => $client->cached_pass . "",
                    ];
                });
            });

        $currentSheet->fromArray($mappedClients, null, 'A3', true);

        foreach (range('A', 'R') as $letter) {
            $currentSheet->getColumnDimension($letter)->setAutoSize(true);
        }

        $excelWriter = new Xlsx($template);
        $fileName = 'Импорт_клиенты.xlsx';
        $path = "storage/excel/";
        \File::ensureDirectoryExists($path);
        $fullPath =  $path . $fileName;
        $excelWriter->save($fullPath);
    }
}
