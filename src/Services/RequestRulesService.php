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
            return "Blade view not found: {$bladePath}";
        }
        $bladeContent = file_get_contents($bladePath);
        // Use regex to find input elements and extract their type and name attributes
        preg_match_all('/<input[^>]*(?:name=["\']?([^"\'>]+)["\']?[^>]*type=["\']?([^"\'>]+)["\']?|type=["\']?([^"\'>]+)["\']?[^>]*name=["\']?([^"\'>]+)["\']?)[^>]*\/?>/i', $bladeContent, $matches);

        // Combine the type and name into strings
        $inputDetails = [];
        foreach ($matches[1] as $index => $name) {
            if($name != ''){
                $type = $matches[2][$index];
                $inputDetails[$name] = $type;
            }
        }
        // Get the content of the Blade file
        return $inputDetails;
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
        }
        return  $result;
    }

}