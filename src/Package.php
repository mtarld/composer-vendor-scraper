<?php

namespace App;

class Package
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $version;

    /**
     * @var string
     */
    private $versionNormalized;

    /**
     * @var bool
     */
    private $isRequired;

    public function __construct(string $name, string $version, string $versionNormalized, bool $isRequired)
    {
        $this->name = $name;
        $this->version = $version;
        $this->versionNormalized = $versionNormalized;
        $this->isRequired = $isRequired;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setVersionByStrategy(string $strategy): self
    {
        $strategyOffsetMap = [
            VersionStrategy::STRATEGY_FIXED => 4,
            VersionStrategy::STRATEGY_PATCH => 4,
            VersionStrategy::STRATEGY_MINOR => 3,
            VersionStrategy::STRATEGY_MAJOR => 2,
        ];

        $versionParts = explode('.', $this->versionNormalized);
        $versionParts = array_splice($versionParts, 0, $strategyOffsetMap[$strategy]);

        if (1 !== count($versionParts)) {
            $this->version = ('fixed' === $strategy ? '' : '^').implode('.', $versionParts);
        }

        return $this;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function setIsRequired(bool $isRequired): self
    {
        $this->isRequired = $isRequired;

        return $this;
    }

    public function isRequired(): bool
    {
        return $this->isRequired;
    }
}
