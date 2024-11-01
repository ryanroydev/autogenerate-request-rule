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
    private RequestRulesService $requestRuleService;
    private FileMappingServices $mapService;

    // Constructor with dependency injection
    public function __construct(RequestRulesService $service, FileMappingServices $mapservice)
    {
        parent::__construct(); // Call parent constructor
        // Store the injected service
        $this->requestRuleService = $service;
        $this->mapService = $mapservice;
    }

    public function handle()
    {
        $controller = $this->argument('Controller');
        $controllerPath = $this->requestRuleService->getControllerPath($controller);

        // Validate if the controller file exists
        if (!class_exists($controllerPath)) {
            return $this->error("Controller class does not exist at: $controllerPath");
        }

        $viewnames = $this->requestRuleService->getViewReturningMethods($controllerPath);
        $outputs = [];

        foreach ($viewnames as $action => $viewname) {

            $bladeInputs = $this->requestRuleService->getBladeInputs($viewname);
            if ($this->hasError($bladeInputs)) {
                $this->error($bladeInputs['message']);
                continue;
            }
           
            $customRequestName = $this->requestRuleService->getUniqueRequestName(
                str_replace('Controller', '', $controller) . ucfirst($action) . 'Request'
            );

            $outputs[] = $this->requestRuleService->generateCustomRequest($customRequestName);
            $requestFilePath = app_path("Http/Requests/{$customRequestName}.php");

            if (!File::exists($requestFilePath)) {
                $this->error("Request file does not exist at: {$requestFilePath}");
                continue;
            }
            
            $requestContent = File::get($requestFilePath);
            $rulesBladeInputs = $this->requestRuleService->generateRulesCode($bladeInputs);
            $requestContent = $this->mapService->mapContentRules($requestContent, $rulesBladeInputs);

            if ($this->hasError($requestContent)) {
                $this->error($requestContent['message']);
                continue;
            }
            // Save the modified content back to the file
            File::put($requestFilePath, $requestContent['data']);
        }

        $this->info(implode("\n", $outputs));
    }

    private function hasError(array $response): bool
    {
        return isset($response['error']) && $response['error'] == true;
    }
}
