<?php

namespace Ryanroydev\AutogenerateRequestRule\Commands;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
class AutogenerateRequestRule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ryanroydev:autogenerate-request-rule {Controller}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command generates a custom form request class for a specified controller. The generated request will automatically include validation rules based on the fields provided in the form input. ';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $controller = $this->argument('Controller');

        $controllerPath = app_path('Http/Controllers/'.$controller.".php");

        $codeString = file_get_contents($controllerPath);

        // Split the string into an array of lines
        $lines = explode("\n", $codeString);

        // Filter lines containing 'return view'
        $viewLines = array_filter($lines, function($line) {
            return strpos($line, 'return view') !== false;
        });

        foreach( $viewLines as  $view){
             $path = resource_path('views/product/index.blade.php');
             $content = File::get($path);
            //$html = View::make('product.index')->render();
        }
      
        echo $content;exit;
        Artisan::call('make:request CustomRequest');
        $output = Artisan::output();
        $this->info($output);
    }
}
