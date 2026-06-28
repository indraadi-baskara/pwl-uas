<?php

declare(strict_types=1);

namespace App\Validation;

final class Validator
{
    /** @var array<string, string> */
    private array $errors = [];

    /** @param array<string, mixed> $data */
    public function __construct(private readonly array $data) {}

    public function required(string $field): self
    {
        if (!isset($this->data[$field]) || $this->data[$field] === '') {
            $this->errors[$field] = "{$field} is required";
        }
        return $this;
    }

    public function email(string $field): self
    {
        if (isset($this->data[$field]) && !filter_var($this->data[$field], FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = "{$field} must be a valid email address";
        }
        return $this;
    }

    public function minLength(string $field, int $min): self
    {
        $value = (string) ($this->data[$field] ?? '');
        if ($value !== '' && mb_strlen($value) < $min) {
            $this->errors[$field] = "{$field} must be at least {$min} characters";
        }
        return $this;
    }

    public function passes(): bool
    {
        return $this->errors === [];
    }

    /** @return array<string, string> */
    public function errors(): array
    {
        return $this->errors;
    }

    public function firstError(): string
    {
        return reset($this->errors) ?: '';
    }
}
