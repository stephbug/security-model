<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Guard\Authorization\Expression;

use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class SecurityExpressionLanguage extends ExpressionLanguage
{
    public function __construct(CacheItemPoolInterface $cache = null, array $providers = array())
    {
        array_unshift($providers, new SecurityExpressionLanguageProvider());

        parent::__construct($cache, $providers);
    }
}