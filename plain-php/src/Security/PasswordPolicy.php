<?php
namespace Ishep\Security;

final class PasswordPolicy
{
    public const MIN_LENGTH = 8;

    public function requirements(): array
    {
        return [
            'length' => 'At least '.self::MIN_LENGTH.' characters',
            'uppercase' => 'At least one uppercase letter',
            'lowercase' => 'At least one lowercase letter',
            'number' => 'At least one number',
        ];
    }

    public function errors(string $password): array
    {
        $errors = [];
        if (mb_strlen($password) < self::MIN_LENGTH) $errors[] = $this->requirements()['length'].'.';
        if (!preg_match('/[A-Z]/', $password)) $errors[] = $this->requirements()['uppercase'].'.';
        if (!preg_match('/[a-z]/', $password)) $errors[] = $this->requirements()['lowercase'].'.';
        if (!preg_match('/\d/', $password)) $errors[] = $this->requirements()['number'].'.';
        return $errors;
    }
}
