<?php
namespace WebExcess\InheritProperties\PostProcessor;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Configuration\ConfigurationManager;
use Neos\ContentRepository\Domain\Model\NodeType;
use Neos\ContentRepository\NodeTypePostprocessor\NodeTypePostprocessorInterface;

/**
 * NodeTypePostprocessor
 */
class NodeTypePostProcessor implements NodeTypePostprocessorInterface
{
    /**
     * @var ConfigurationManager
     * @Flow\Inject
     */
    protected $configurationManager;

    /**
     * @var boolean
     */
    protected $autoRemoveAtomProperties;

    /**
     * Returns the processed Configuration
     *
     * @param \Neos\ContentRepository\Domain\Model\NodeType $nodeType (uninitialized) The node type to process
     * @param array $configuration input configuration
     * @param array $options The processor options
     * @return void
     */
    public function process(NodeType $nodeType, array &$configuration, array $options)
    {
        if (is_array($options) && array_key_exists('segments', $options)) {
            $this->autoRemoveAtomProperties = is_array($options) && array_key_exists('autoRemoveAtomProperties', $options) && is_bool($options['autoRemoveAtomProperties']) ? $options['autoRemoveAtomProperties'] : false;

            foreach ($options['segments'] as $segment) {
                $this->walkTreeSegment($nodeType, $configuration, $options, $segment);
            }
        }
    }

    /**
     * Returns the by tree segment processed Configuration
     *
     * @param \Neos\ContentRepository\Domain\Model\NodeType $nodeType (uninitialized) The node type to process
     * @param array $configuration input configuration
     * @param array $options The processor options
     * @param string $segment The tree segment to process
     * @return void
     */
    private function walkTreeSegment(NodeType &$nodeType, array &$configuration, array &$options, $segment) {
        if (array_key_exists($segment, $configuration)) {
            foreach ($configuration[$segment] as $propertyName => $propertyValues) {
                if (strpos($propertyName, ' >')!==false) {
                    $propertyToRemove = substr($propertyName, 0, -2);
                    unset($configuration[$segment][$propertyToRemove]);
                    unset($configuration[$segment][$propertyName]);
                }
            }
        }
    }
}
