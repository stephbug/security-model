<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Guard\Authorization\Expression;

use Illuminate\Http\Request;
use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;
use StephBug\SecurityModel\Guard\Authentication\TrustResolver;
use StephBug\SecurityModel\Guard\Authorization\Hierarchy\RoleHierarchy;
use StephBug\SecurityModel\Guard\Authorization\Voter\AccessVoter;
use StephBug\SecurityModel\Role\RoleSecurity;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

class SecurityExpressionVoter extends AccessVoter
{
    /**
     * @var SecurityExpressionLanguage
     */
    private $expressionLanguage;

    /**
     * @var TrustResolver
     */
    private $trustResolver;

    /**
     * @var RoleHierarchy
     */
    private $roleHierarchy;

    public function __construct(SecurityExpressionLanguage $expressionLanguage,
                                TrustResolver $trustResolver,
                                RoleHierarchy $roleHierarchy = null)
    {
        $this->expressionLanguage = $expressionLanguage;
        $this->trustResolver = $trustResolver;
        $this->roleHierarchy = $roleHierarchy;
    }

    public function addExpressionLanguageProvider(ExpressionFunctionProviderInterface $provider)
    {
        $this->expressionLanguage->registerProvider($provider);
    }

    public function vote(Tokenable $token, array $attributes, $subject = null): int
    {
        $vote = $this->abstain();
        $variables = null;

        foreach ($attributes as $attribute) {
            if (!$this->supportAttribute($attribute)) {
                continue;
            }

            if (!$attribute instanceof Expression) {
                $attribute = new Expression($attribute);
            }

            if (null === $variables) {
                $variables = $this->getVariables($token, $subject);
            }

            $vote = $this->deny();

            if ($this->expressionLanguage->evaluate($attribute, $variables)) {
                return $this->grant();
            }
        }

        return $vote;
    }

    private function supportAttribute($attribute)
    {
        return str_contains($attribute, '(') && str_contains($attribute, ')');
    }

    private function getVariables(Tokenable $token, $subject): array
    {
        $roles = $this->getTokenRoles($token);

        $variables = [
            'token' => $token,
            'user' => $token->getUser(),
            'object' => $subject,
            'roles' => array_map(function (RoleSecurity $role) {
                return $role->getRole();
            }, $roles),
            'trust_resolver' => $this->trustResolver
        ];

        if ($subject instanceof Request) {
            $variables['request'] = $subject;
        }

        return $variables;
    }

    private function getTokenRoles(Tokenable $token): array
    {
        $roles = $token->getRoles();

        if ($this->roleHierarchy) {
            return $this->roleHierarchy->getReachableRoles($roles);
        }

        return $roles;
    }
}