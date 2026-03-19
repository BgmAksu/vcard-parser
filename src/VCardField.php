<?php

declare(strict_types=1);

class VCardField
{
    private string $name;
    private string $value;

    /**
     * @var array<string, string>
     */
    private array $parameters;

    /**
     * @param array<string, string> $parameters
     */
    public function __construct(string $name, string $value, array $parameters = [])
    {
        $this->name = $name;
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
     * @return array<string, string>
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }
}