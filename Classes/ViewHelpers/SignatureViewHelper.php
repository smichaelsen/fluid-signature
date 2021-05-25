<?php

declare(strict_types=1);

namespace Smic\FluidSignature\ViewHelpers;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class SignatureViewHelper extends AbstractViewHelper
{
    protected const ALWAYS_ALLOWED = ['settings'];

    public function initializeArguments()
    {
        $this->registerArgument('required', 'string', 'Comma separated list of variables. If given, an exception is thrown if any of the variables in not defined in the current variable context.', false);
        $this->registerArgument('allowed', 'string', 'Comma separated list of variables. If given the current variable context is checked for any variables that are not in `required` or `allowed` and an exception is thrown if a variable is found. `allowed=""` means only the `required` variables are allowed.', false);
        $this->registerArgument('defaultValues', 'array', '', false);
    }

    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
    {
        if ($arguments['required'] !== null) {
            $requiredVariables = GeneralUtility::trimExplode(',', $arguments['required'], true);
            foreach ($requiredVariables as $requiredVariableName) {
                if (!$renderingContext->getVariableProvider()->exists($requiredVariableName)) {
                    throw new \Exception('Required fluid variable ' . $requiredVariableName . ' is not available', 1621947254);
                }
            }
        }
        if ($arguments['allowed'] !== null) {
            $allowedVariables = [
                ...GeneralUtility::trimExplode(',', (string)$arguments['required'], true),
                ...GeneralUtility::trimExplode(',', $arguments['allowed'], true),
                ...self::ALWAYS_ALLOWED,
            ];
            foreach ($renderingContext->getVariableProvider()->getAllIdentifiers() as $variableName) {
                if (!in_array($variableName, $allowedVariables)) {
                    throw new \Exception('Variable ' . $variableName . ' was provided but not allowed', 1621948774);
                }
            }
        }
        if (is_array($arguments['defaultValues'])) {
            foreach ($arguments['defaultValues'] as $variableName => $defaultValue) {
                if (!$renderingContext->getVariableProvider()->exists($variableName)) {
                    $renderingContext->getVariableProvider()->add($variableName, $defaultValue);
                }
            }
        }
    }
}
