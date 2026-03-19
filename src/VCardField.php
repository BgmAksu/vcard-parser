<?php

declare(strict_types=1);

namespace VCard;

class VCardField
{
    private string $name;
    private string $value;

    /**
     * @var array<string, array<int, string>>
     */
    private array $parameters;

    /**
     * @param array<string, array<int, string>> $parameters
     */
    public function __construct(string $name, string $value, array $parameters = [])
    {
        $this->name = strtoupper($name);
        $this->value = $value;
        $this->parameters = $parameters;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @return string[]|null
     */
    public function getParameter(string $name): ?array
    {
        $normalizedName = strtoupper($name);

        return $this->parameters[$normalizedName] ?? null;
    }
}