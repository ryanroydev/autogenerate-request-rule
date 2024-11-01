<?php

namespace Ryanroydev\AutogenerateRequestRule\Commands;

use Illuminate\Support\Facades\File;
use Illuminate\Console\Command;
use Ryanroydev\AutogenerateRequestRule\Services\RequestRulesService;
use Ryanroydev\AutogenerateRequestRule\Services\FileMappingServices;

class AutogenerateRequestRule extends Command
{
    protected $signature = 'ryanroydev:autogenerate-request-rule {Controller}';
    protected $description = 'Generates a custom form request class for a specified controller with automatic validation rules.';

    private RequestRulesService $requestRuleService;
    private FileMappingServices $mapService;

    public function __construct(RequestRulesService $service, FileMappingServices $mapService)
    {
        parent::__construct();
        $this->requestRuleService = $service;
        $this->mapService = $mapService;
    }

    public function handle()
    {
        $controller = $this->argument('Controller');
        $controllerPath = $this->requestRuleService->getControllerPath($controller);

        if (!$this->validateController($controllerPath)) {
            return;
        }

        $viewNames = $this->requestRuleService->getViewReturningMethods($controllerPath);
        $outputs = [];

        foreach ($viewNames as $action => $viewName) {
            $bladeInputs = $this->requestRuleService->getBladeInputs($viewName);

            if ($this->hasError($bladeInputs)) {
                $this->error($bladeInputs['message']);
                continue;
            }

            foreach ($bladeInputs as $bladeInput) {
               

                $actionName = $this->sanitizeActionName($bladeInput['action'] ?? $action);
                $customRequestName = $this->generateRequestName($controller, $actionName);
                $outputs[] = $this->requestRuleService->generateCustomRequest($customRequestName);
                $requestFilePath = app_path("Http/Requests/{$customRequestName}.php");

                if (!File::exists($requestFilePath)) {
                    $this->error("Request file does not exist at: {$requestFilePath}");
                    continue;
                }

                $requestContent = File::get($requestFilePath);
                $rulesBladeInputs = $this->requestRuleService->generateRulesCode($bladeInput);
                $requestContent = $this->mapService->mapContentRules($requestContent, $rulesBladeInputs);

                if ($this->hasError($requestContent)) {
                    $this->error($requestContent['message']);
                    continue;
                }

                File::put($requestFilePath, $requestContent['data']);
            }
        }

        $this->info(implode("\n", $outputs));
    }

    private function validateController(string $controllerPath): bool
    {
        if (!class_exists($controllerPath)) {
            $this->error("Controller class does not exist at: $controllerPath");
            return false;
        }
        return true;
    }

    private function sanitizeActionName(string $action): string
    {
        return $this->removeSpecialCharacters(ucfirst(str_replace('route', '', $action)));
    }

    private function generateRequestName(string $controller, string $action): string
    {
        return $this->requestRuleService->getUniqueRequestName(
            str_replace('Controller', '', $controller) . $action . 'Request'
        );
    }

    private function hasError(array $response): bool
    {
        return !empty($response['error']) && $response['error'] === true;
    }

    private function removeSpecialCharacters(string $string): string
    {
        return preg_replace('/[^a-zA-Z0-9]/', '', $string);
    }
}
