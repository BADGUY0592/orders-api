<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\Helper;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Models\Products;

class ImportData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import {--customers : Import Customers} {--products : Import Products}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will help you to import the data from csv';

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
        $options = $this->options();
        if($options['customers'] == true) {

            $users = Helper::readCsv(public_path("/csv/customers.csv"));
            $user_count = count($users);
            $imported = $not_imported = $already_existed =0;

            $bar = $this->output->createProgressBar(($user_count - 1));
            print("\nStarted Users Import\n");
            $bar->start();

            for($i = 1; $i < $user_count; $i++) {
                $user = User::where('email',$users[$i][2])->first();
                if($user) {
                    $already_existed++;
                } else {
                    try {
                        User::create([
                            'email' => $users[$i][2],
                            'job_title' => $users[$i][1],
                            'name' => $users[$i][3],
                            'password' => Hash::make('password'),
                            'registered_since' => date("Y-m-d", strtotime(substr($users[$i][4], strpos($users[$i][4], ',') + 1))),
                            'mobile_number' => $users[$i][5]
                        ]);
                        $imported++;
                    } catch (\Throwable $th) {
                        $not_imported++;
                    }
                }

                $bar->advance();
            }

            $bar->finish();
            print("\nUsers Import Complete\n");
            Log::info("Users imported. {'Total':". ($user_count - 1) .",'Imported':". $imported .",'Not Imported':". $not_imported .",'Already Existed':". $already_existed ."}");
        }

        if($options['products'] == true) {

            $products = Helper::readCsv(public_path("/csv/products.csv"));
            $product_count = count($products);
            $imported = $not_imported = $already_existed =0;

            $bar = $this->output->createProgressBar(($product_count - 1));
            print("\nStarted Products Import\n");
            $bar->start();

            for($i = 1; $i < $product_count; $i++) {
                $product = Products::where('id',$products[$i][0])->first();
                if($product) {
                    $already_existed++;
                } else {
                    try {
                        Products::create([
                            'id' => $products[$i][0],
                            'name' => $products[$i][1],
                            'price' => $products[$i][2]
                        ]);
                        $imported++;
                    } catch (\Throwable $th) {
                        $not_imported++;
                    }
                }

                $bar->advance();
            }

            $bar->finish();
            print("\nProducts Import Complete\n");
            Log::info("Products imported. {'Total':". ($product_count - 1) .",'Imported':". $imported .",'Not Imported':". $not_imported .",'Already Existed':". $already_existed ."}");
        }
    }
}
