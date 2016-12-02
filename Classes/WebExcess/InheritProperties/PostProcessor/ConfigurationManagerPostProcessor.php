<?php
namespace WebExcess\InheritProperties\PostProcessor;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Configuration\ConfigurationManager;
use Neos\ContentRepository\Domain\Model\NodeType;
use Neos\ContentRepository\NodeTypePostprocessor\NodeTypePostprocessorInterface;
use Neos\Utility\Arrays;

/**
 * NodeTypePostprocessor
 */
class ConfigurationManagerPostProcessor
{

    /**
     * Returns the processed Configuration
     *
     * @param array $completeNodeTypeConfiguration input configurations
     * @return void
     */
    public function process(array &$completeNodeTypeConfiguration)
    {
        $completeNodeTypeConfigurationOverwrite = array();
        foreach ($completeNodeTypeConfiguration as $nodeTypeName => $nodeTypeConfiguration) {
            $superTypesProperties = array();
            if (isset($nodeTypeConfiguration['superTypes'])) {
                foreach ($nodeTypeConfiguration['superTypes'] as $superType => $enabled) {
                    if ($enabled && isset($completeNodeTypeConfiguration[$superType]['properties'])) {
                        foreach ($completeNodeTypeConfiguration[$superType]['properties'] as $superTypePropertyName => $superTypePropertyConfiguration) {
                            $superTypesProperties[$superTypePropertyName] = $superTypePropertyConfiguration;
                        }
                    }
                }
            }
            if (array_key_exists('properties', $nodeTypeConfiguration)) {
                foreach ($nodeTypeConfiguration['properties'] as $propertyName => $propertyConfiguration) {
                    if (strpos($propertyName, ' < ')!==false) {
                        $explodedPropertyName = explode(' < ', $propertyName);
                        if (count($explodedPropertyName) > 0) {
                            $completeNodeTypeConfigurationOverwrite[$nodeTypeName]['properties'][$explodedPropertyName[0]] = Arrays::arrayMergeRecursiveOverrule(
                                Arrays::arrayMergeRecursiveOverrule(
                                    array_key_exists($explodedPropertyName[1], $superTypesProperties) ? $superTypesProperties[$explodedPropertyName[1]] : array(),
                                    $propertyConfiguration
                                ),
                                isset($completeNodeTypeConfiguration[$nodeTypeName]['properties'][$explodedPropertyName[0]]) ? $completeNodeTypeConfiguration[$nodeTypeName]['properties'][$explodedPropertyName[0]] : array()
                            );
                            unset($completeNodeTypeConfiguration[$nodeTypeName]['properties'][$propertyName]);
                        }
                    }
                }
            }
        }
        $completeNodeTypeConfiguration = Arrays::arrayMergeRecursiveOverrule($completeNodeTypeConfiguration, $completeNodeTypeConfigurationOverwrite);
    }

}
