<?php

namespace Ryanroydev\AutogenerateRequestRule\Services;

class FileMappingServices
{

    public function mapContentRules(String $requestContent, String $newRules)  : String
    {

        foreach(['public function rules(): array','public function rules()'] as $mapText)
        {
            $rulesMethodPosition = strpos($requestContent, $mapText);
            if($newRules === false)
            {
                return false;
            }
            
            // Find the position of the existing return statement
            $returnPosition = strpos($requestContent, 'return [', $rulesMethodPosition);
            if ($returnPosition === false) {
                return false;
            }

            // Find the end of the existing rules method
            $methodEndPosition = strpos($requestContent, '}', $returnPosition);
            if ($methodEndPosition === false) {
                return false;
            }

            // Replace the old return statement and rules with the new one
            $requestContent = substr_replace($requestContent, $newRules, $returnPosition, $methodEndPosition - $returnPosition);
            
            return $requestContent;
        }

        return false;
    }

}