<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\UserBundle\GoogleAuthenticator;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

class RendererListener
{
    protected $templating;

    protected $view;

    /**
     * Constructor.
     *
     * @param EngineInterface $templating Template engine
     * @param string          $view       The view name
     */
    public function __construct(EngineInterface $templating, $view)
    {
        $this->templating = $templating;
        $this->view = $view;
    }

    /**
     * Sets render google step in response.
     *
     * @param RequestEvent $event
     */
    public function onRender(RequestEvent $event)
    {
        $event->setResponse($this->templating->renderResponse($this->view,
            array('state' => $event->getState())
        ));
    }
}