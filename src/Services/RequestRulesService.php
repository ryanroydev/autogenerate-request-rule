<?php

namespace Ryanroydev\AutogenerateRequestRule\Services;
use Illuminate\Support\Facades\Artisan;
use ReflectionClass; // Import from the global namespace

class RequestRulesService
{

    /**
     * Get the full path to the specified controller.
     *
     * This method constructs the file path for a controller located in the 
     * `app/Http/Controllers` directory based on the provided controller name.
     *
     * @param string $controller The name of the controller (without the .php extension).
     * @return string The full path to the controller file.
     */
    public function getControllerPath(String $controller) : String
    {
        $controllerPath  = "App\\Http\\Controllers\\$controller";
        //todo
        return $controllerPath;
    }

     /**
     * getViewReturningMethods.
     *
     * @param string $controllerPath controller name class.
     * @return array $viewReturningMethods Tget all method in class.
     */
    public function getViewReturningMethods(String $controllerPath) : Array
    {
        
        $reflection = new ReflectionClass($controllerPath);
        $methods = $reflection->getMethods();
        $instance = $reflection->newInstance();
        $viewReturningMethods = [];

        foreach($methods as $method){
            if($method->isPublic()){
                
                $methodBody =  file($method->getFileName());
                $startLine = $method->getStartLine();
                $endLine = $method->getEndLine();
             

                //extract whole method 
                $methodLines = array_slice($methodBody,$startLine - 1,$endLine - $startLine + 1);
                $methodString = implode('',$methodLines);

                // Check if the method returns a view
                if (preg_match('/return\s+view\((.+?)\);/', $methodString, $matches)) {
                    
                       // Extract the view name from the match
                    $viewName = trim($matches[1], '\'"'); // Remove quotes
                    $viewReturningMethods[$method->getname()] = $viewName;
                }
            }
        }
        
        return $viewReturningMethods;

    }

     /**
     * Get the inputs of a Blade view file.
     *
     * @param string $viewName The name of the view (without the .blade.php extension).
     * @return array The inputs of the Blade view.
     */
    public function getBladeInputs(string $viewName): array
    {

        $viewName = str_replace('.', '/', $viewName);
        // Construct the path to the Blade file
        $bladePath = resource_path("views/{$viewName}.blade.php");

        // Check if the Blade file exists
        if (!file_exists($bladePath)) {
            return [
                'error' => true,
                'message' => "Blade view not found at: {$bladePath}",
            ];
        }
        $bladeContent = file_get_contents($bladePath);

        preg_match_all('/<form[^>]*action=["\']?([^"\'>]*)["\']?[^>]*>(.*?)<\/form>/is', $bladeContent, $formMatches);

      
        $formInputs = [];

        if(empty($formMatches[0]))
        {
            $formInputs[] = $this->getInputsInText($bladeContent);
           
        } else {
            foreach ($formMatches[0] as $index => $form) {

                preg_match('/action=["\']?(\{\{\s*route\([\'"]?([^\'"\s]+)[\'"]?\s*\)\s*\}\}|[^"\'>]+)["\']?/', $form, $actionmatches);

                $formContent = $formMatches[2][$index]; // Get inner form content
              
                $formInputs[] =  array_merge(
                    $this->getInputsInText($formContent), 
                    ['action' => $actionmatches[1] ?? '']
                );
            }
        }
        // Get the content of the Blade file
        return $formInputs;
    }

    protected function getInputsInText($contents){
        $inputs = [];
       
        // Now extract input fields from the form content
        preg_match_all('/<input[^>]*(?:name=["\']?([^"\'>]+)["\']?[^>]*type=["\']?([^"\'>]+)["\']?|type=["\']?([^"\'>]+)["\']?[^>]*name=["\']?([^"\'>]+)["\']?)[^>]*\/?>/i', $contents, $inputMatches);
            
        // Loop through the matches and organize them
        foreach ($inputMatches[0] as $key => $match) {
            
            $name = !empty($inputMatches[4][$key]) ? $inputMatches[4][$key] : (!empty($inputMatches[1][$key]) ? $inputMatches[1][$key] : null);
            // Type can be in either $inputMatches[3] or $inputMatches[2]
            $type = !empty($inputMatches[3][$key]) ? $inputMatches[3][$key] : (!empty($inputMatches[2][$key]) ? $inputMatches[2][$key] : null);
            

           
            if ($name && $type) {
                $inputs[$name] = $type;
            }
        }
        
        return $inputs;
    }

     /**
     * generateCustomRequest.
     *
     * @param string $requestClass The request class name .
     * @return array The output path.
     */
    public function generateCustomRequest(String $requestClass){
        
        Artisan::call('make:request '.$requestClass);
        return Artisan::output();
    }

     /**
     * generateRulesCode.
     *
     * @param Array $rulesArray The rules Array fields.
     * @return String  $resultCode The result code.
     */
    public function generateRulesCode(Array $rulesArray) : String
    {
        $rulesCode = "\n        return [\n";
        foreach ($rulesArray as $field => $type) {

            $validation = $this->getRulesByType($type);
            $rulesCode .= "            '$field' => '$validation',\n";
        }
        $rulesCode .= "        ];\n";
        return $rulesCode;
    }

     /**
     * getRulesByType.
     *
     * @param Array $type The type of field.
     * @return String  $result The result.
     */
    protected function getRulesByType(String $type) : String
    {
        $result = 'required';
        switch ($type) {
            case 'text':
                $result  =  'required|string|max:255';
                break;
            case 'number':
                 $result  =  'required|integer';
                break;  
            case 'date':
                 $result  =  'required|date';
                break;
            case 'email':
                $result  =  'required|email';
                break;  
        }
        return  $result;
    }

     /**
     * Get Unique Request Name.
     *
     * @param string $baseRequestName Current Name.
     * @return String  $newRequestName The New Name if exist base name.
     */
    public function getUniqueRequestName(string $baseRequestName): string
    {
        $requestFilePath = app_path("Http/Requests/{$baseRequestName}.php");
        $counter = 1;

        // Check if the request file exists and increment the counter if it does
        while (file_exists($requestFilePath)) {
            // Create a new request name with incremented counter
            $newRequestName = str_replace('Request', "Request{$counter}", $baseRequestName);
            $requestFilePath = app_path("Http/Requests/{$newRequestName}.php");
            $counter++;
        }

        // Return the unique request name
        return $newRequestName ?? $baseRequestName; // Fallback to base name if no incrementing is needed
    }
}