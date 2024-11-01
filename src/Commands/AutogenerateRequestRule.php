<?php

namespace Ryanroydev\AutogenerateRequestRule\Commands;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Console\Command;


use Ryanroydev\AutogenerateRequestRule\Services\RequestRulesService;
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

    private RequestRulesService $request_rule_service;

    // Constructor with dependency injection
    public function __construct(RequestRulesService $service)
    {
        parent::__construct(); // Call parent constructor
   
        // Store the injected service
        $this->request_rule_service = $service;
   
          
    }

    public function handle()
    {
        $output = "";
        $controller = $this->argument('Controller');
        $controllerPath = $this->request_rule_service->getControllerPath($controller);

        //Validate if the controller file exists
        if (!class_exists($controllerPath)) {
            return $this->error("Controller Class does not exist at: $controllerPath");
        }

        $viewnames = $this->request_rule_service->getViewReturningMethods($controllerPath);
       

        foreach($viewnames as $action => $viewname){
            $bladeInputs = $this->request_rule_service->getBladeInputs($viewname);
            $CustomRequestName = str_replace('Controller','',$controller).ucfirst($action).'Request';
            $output = $this->request_rule_service->generateCustomRequest($CustomRequestName);
           

        }

        // foreach( $viewLines as  $view){
        //      $path = resource_path('views/product/index.blade.php');
        //      $content = File::get($path);
        //      echo $content;exit;
        //     //$html = View::make('product.index')->render();
        // }

        // foreach($methods as  $method){
        //     $method->invoke();
        // }
        
      
        $this->info($output);
    }
}
