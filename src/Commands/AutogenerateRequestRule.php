<?php

namespace Ryanroydev\AutogenerateRequestRule\Commands;

use Illuminate\Support\Facades\File;
use Illuminate\Console\Command;
use Ryanroydev\AutogenerateRequestRule\Services\RequestRulesService;
use Ryanroydev\AutogenerateRequestRule\Services\FileMappingServices;
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
    protected $description = 'This command generates a custom form request class for a specified controller. The generated request will automatically include validation rules based on the fields provided in the form input.';

    /**
     * Execute the console command.
     */
    private RequestRulesService $request_rule_service;
    private FileMappingServices $map_service;

    // Constructor with dependency injection
    public function __construct(RequestRulesService $service, FileMappingServices $mapservice)
    {
        parent::__construct(); // Call parent constructor
        // Store the injected service
        $this->request_rule_service = $service;
        $this->map_service = $mapservice;
    }

    public function handle()
    {
        $controller = $this->argument('Controller');
        $controllerPath = $this->request_rule_service->getControllerPath($controller);

        // Validate if the controller file exists
        if (!class_exists($controllerPath)) {
            return $this->error("Controller class does not exist at: $controllerPath");
        }

        $viewnames = $this->request_rule_service->getViewReturningMethods($controllerPath);
        $outputs = [];

        foreach ($viewnames as $action => $viewname) {
            $bladeInputs = $this->request_rule_service->getBladeInputs($viewname);

           
            $CustomRequestName = str_replace('Controller', '', $controller) . ucfirst($action) . 'Request';
            $CustomRequestName = $this->request_rule_service->getUniqueRequestName($CustomRequestName);
            $outputs[] = $this->request_rule_service->generateCustomRequest($CustomRequestName);
            $requestFilePath = app_path("Http/Requests/{$CustomRequestName}.php");

            // Read the contents of the file
            if (!File::exists($requestFilePath)) {
                return $this->error("Request file does not exist at: $requestFilePath");
            }

            $requestContent = File::get($requestFilePath);

            // Generate new rules code based on blade inputs
            $rulesBladeInputs = $this->request_rule_service->generateRulesCode($bladeInputs);

            // Find the location of the rules() method
            $requestContent = $this->map_service->mapContentRules($requestContent, $rulesBladeInputs);

          
            // Save the modified content back to the file
            File::put($requestFilePath, $requestContent);
        }

        $this->info(implode("\n", $outputs));
    }
}
