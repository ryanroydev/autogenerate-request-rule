<?php

namespace Ryanroydev\AutogenerateRequestRule\Services;

class FileMappingServices
{
    /**
     * Maps the content rules in the request file.
     *
     * @param string $requestContent The content of the request file.
     * @param string $newRules The new rules to be inserted.
     * @return array An array containing either the modified content or an error message.
     */
    public function mapContentRules(string $requestContent, string $newRules): array
    {
        // Define possible rules method signatures
        $rulesMethodSignatures = ['public function rules(): array', 'public function rules()'];

        foreach ($rulesMethodSignatures as $methodSignature) {
            $rulesMethodPosition = strpos($requestContent, $methodSignature);

            if ($rulesMethodPosition === false) {
                return $this->createErrorResponse("Public function rules not found in custom request.");
            }

            // Find the position of the existing return statement
            $returnPosition = strpos($requestContent, 'return [', $rulesMethodPosition);
            if ($returnPosition === false) {
                return $this->createErrorResponse("Return statement not found in the custom request.");
            }

            // Find the end of the existing rules method
            $methodEndPosition = strpos($requestContent, '}', $returnPosition);
            if ($methodEndPosition === false) {
                return $this->createErrorResponse("End of the existing rules method not found.");
            }

            // Replace the old return statement and rules with the new one
            $requestContent = substr_replace($requestContent, $newRules, $returnPosition, $methodEndPosition - $returnPosition);

            return [
                'error' => false,
                'data' => $requestContent,
            ];
        }

        return $this->createErrorResponse("An unknown error occurred.");
    }

    /**
     * Creates a standardized error response.
     *
     * @param string $message The error message.
     * @return array An array containing the error status and message.
     */
    private function createErrorResponse(string $message): array
    {
        return [
            'error' => true,
            'message' => $message,
        ];
    }
}
