<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Guard\Service\Recaller\Providers;

use Illuminate\Database\Eloquent\Model;
use StephBug\SecurityModel\Application\Values\Contract\RecallerIdentifier as BaseIdentifier;
use StephBug\SecurityModel\User\Exception\UserNotFound;
use StephBug\SecurityModel\User\UserRecaller;
use StephBug\SecurityModel\User\UserSecurity;

class SimpleRecallerProvider implements RecallerProvider
{
    /**
     * @var UserRecaller|Model
     */
    private $model;

    public function __construct(UserRecaller $model)
    {
        $this->model = $model;
    }

    public function requireUserFromRecaller(BaseIdentifier $identifier): UserSecurity
    {
        $user = $this->model->newInstance()->newQuery()
            ->where('recaller_token', $identifier->identify())
            ->first();

        if (!$user) {
            throw new UserNotFound('User not found with recaller identifier');
        }

        return $user;
    }

    public function refreshUserRecaller(UserSecurity $user, BaseIdentifier $identifier): UserSecurity
    {
        $recaller['recaller_token'] = $identifier->identify();
        $recaller->save();

        return $recaller->refresh();
    }
}