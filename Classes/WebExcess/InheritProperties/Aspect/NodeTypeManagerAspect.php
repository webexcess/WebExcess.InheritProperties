<?php
namespace WebExcess\InheritProperties\Aspect;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Aop\JoinPointInterface;
use Neos\Flow\Configuration\ConfigurationManager;
use WebExcess\InheritProperties\Configuration\Source\YamlSource;

/**
 * @Flow\Aspect
 */
class NodeTypeManagerAspect
{

    /**
     * Around advice, implements the new method "loadNodeTypes" of the new "NodeTypeManager"
     *
     * @param  \Neos\Flow\AOP\JoinPointInterface $joinPoint The current join point
     * @return void
     * @Flow\Around("method(Neos\ContentRepository\Domain\Service\NodeTypeManager->injectConfigurationManager())")
     */
    public function newInjectConfigurationManager(\Neos\Flow\AOP\JoinPointInterface $joinPoint) {
        /** @var ConfigurationManager $configurationManager */
        $configurationManager = $joinPoint->getMethodArgument('configurationManager');
        $configurationManager->injectConfigurationSource(new YamlSource());
        $configurationManager->injectConfigurationPostProcessors(array(
            'NodeTypes' => array(
                'WebExcess\InheritProperties\PostProcessor\ConfigurationManagerPostProcessor')
            )
        );
        $joinPoint->setMethodArgument('configurationManager', $configurationManager);
        $joinPoint->getAdviceChain()->proceed($joinPoint);
    }

}
