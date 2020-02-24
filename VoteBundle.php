<?php

namespace VoteBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use VoteBundle\DependencyInjection\VoteExtension;

class VoteBundle extends Bundle
{
    /**
     * @return VoteExtension|\Symfony\Component\DependencyInjection\Extension\ExtensionInterface|null
     */
    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $this->extension = new VoteExtension();
        }

        return new VoteExtension();
    }
}
