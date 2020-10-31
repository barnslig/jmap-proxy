<?php

namespace barnslig\JMAP\Core\Controllers;

use barnslig\JMAP\Core\RequestContext;
use Psr\Http\Server\RequestHandlerInterface;

abstract class AbstractController implements RequestHandlerInterface
{
    /**
     * The JMAP request context
     *
     * @var RequestContext
     */
    private $context;

    /**
     * The global config
     *
     * @var array<string, mixed>
     */
    private $config;

    /**
     * Construct a new controller
     *
     * @param RequestContext $context JMAP request context
     * @param array<string, mixed> $config JMAP core config
     */
    public function __construct(RequestContext $context, array $config)
    {
        $this->context = $context;
        $this->config = $config;
    }

    /**
     * Get the JMAP request context
     *
     * @return RequestContext
     */
    public function getContext(): RequestContext
    {
        return $this->context;
    }

    /**
     * Get a config option
     *
     * @param string $key Config option key
     * @return mixed
     */
    public function getOption(string $key)
    {
        return $this->config[$key];
    }
}
