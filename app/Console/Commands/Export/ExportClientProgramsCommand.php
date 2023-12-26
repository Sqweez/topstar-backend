<?php

namespace App\Console\Commands\Export;

use App\Models\ServiceSale;
use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExportClientProgramsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'export:clients-programs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export client programs';

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
    public function handle()
    {
        $template = IOFactory::load('excel/Импорт_услуги_клиентов.xlsx');
        $currentSheet = $template->getActiveSheet();

        $servicesW = ServiceSale::query()
            ->with(['sale' => function ($q) {
                return $q
                    ->where('club_id', 1)
                    ->with('client')
                    ->with('transaction')
                    ->with('user')
                    ->has('transaction');
            }])
            ->whereHas('service', function ($q) {
                return $q->where('service_type_id', 3)->where('club_id', 1);
            })
            ->with(['service' => function ($q) {
                return $q->where('service_type_id', 3)->where('club_id', 1);
            }])
            ->with('visits')
            ->with('penalties')
            ->get()
            ->filter(function (ServiceSale $sale) {
                return isset($sale->sale->client) && isset($sale->sale->transaction);
            })
            ->map(function (ServiceSale $sale) {
                return [
                    'id' => '',
                    'id2' => '',
                    'phone' => $sale->sale->client->phone,
                    'client_fio' => $sale->sale->client->name,
                    'contract_name' => $sale->service->name,
                    'create_date' => format_date($sale->created_at),
                    'payment_date' => format_date($sale->created_at),
                    'activation_date' => format_date($sale->activated_at),
                    'end_date' => format_date($sale->active_until),
                    'count' => 1,
                    'visits_left' => $sale->getRemainingVisitsAttribute(),
                    'price' => $sale->sale->transaction->amount * -1,
                    'amount_of_payments' => $sale->sale->transaction->amount * -1,
                    'payment_left' => 0,
                    'type_of_payment' => 'Наличные',
                    'manager' => $sale->sale->user->name,
                ];
            })
            ->toArray();
        $servicesA = ServiceSale::query()
            ->with(['sale' => function ($q) {
                return $q->where('club_id', [2, 3])->with('client')->with('transaction')->with('user')->has('transaction');
            }])
            ->whereHas('service', function ($q) {
                return $q->where('service_type_id', 3)->where('club_id', [2, 3]);
            })
            ->with(['service' => function ($q) {
                return $q->where('service_type_id', 3)->where('club_id', [2, 3]);
            }])
            ->with('visits')
            ->with('penalties')
            ->get()
            ->filter(function (ServiceSale $sale) {
                return isset($sale->sale->client) && isset($sale->sale->transaction);
            })
            ->map(function (ServiceSale $sale) {
                return [
                    'id' => '',
                    'id2' => '',
                    'phone' => $sale->sale->client->phone,
                    'client_fio' => $sale->sale->client->name,
                    'contract_name' => $sale->service->name,
                    'create_date' => format_date($sale->created_at),
                    'payment_date' => format_date($sale->created_at),
                    'activation_date' => format_date($sale->activated_at),
                    'end_date' => format_date($sale->active_until),
                    'count' => 1,
                    'visits_left' => $sale->getRemainingVisitsAttribute(),
                    'price' => $sale->sale->transaction->amount * -1,
                    'amount_of_payments' => $sale->sale->transaction->amount * -1,
                    'payment_left' => 0,
                    'type_of_payment' => 'Наличные',
                    'manager' => $sale->sale->user->name,
                ];
            })
            ->toArray();;

        $this->write($currentSheet, $template, 'АТРИУМ', $servicesA);
        $this->write($currentSheet, $template, 'СТУДИЯ', $servicesW);
    }

    private function write($sheet, $template, $name, $input)
    {
        $sheet->fromArray($input, null, 'A3', true);

        $excelWriter = new Xlsx($template);
        $fileName = 'Импорт_услуги_клиентов_' . $name . '.xlsx';
        $path = "storage/excel/";
        \File::ensureDirectoryExists($path);
        $fullPath =  $path . $fileName;
        $excelWriter->save($fullPath);
    }
}
