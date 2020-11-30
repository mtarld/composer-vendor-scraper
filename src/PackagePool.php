<?php

namespace App;

class PackagePool implements \ArrayAccess, \IteratorAggregate
{
    /**
     * @var array<Package>
     */
    private $packages;

    public static function createFromInstalled(array $installed): self
    {
        $instance = new self();

        $dependencies = array_reduce($installed, static function (array $dependencies, array $package) {
            return array_merge(
                $dependencies,
                array_keys($package['require'] ?? []),
                array_keys($package['require-dev'] ?? [])
            );
        }, []);

        $instance->packages = [];
        foreach ($installed as $installedPackage) {
            $instance->packages[$installedPackage['name']] = new Package(
                $installedPackage['name'],
                $installedPackage['version'],
                $installedPackage['version_normalized'],
                !in_array($installedPackage['name'], $dependencies, true)
            );
        }

        return $instance;
    }

    /**
     * @return array<Package>
     */
    public function getRequired(): array
    {
        return array_filter($this->packages, static function (Package $package): bool {
            return $package->isRequired();
        });
    }

    /**
     * @return array<Package>
     */
    public function getDependencies(): array
    {
        return array_filter($this->packages, static function (Package $package): bool {
            return !$package->isRequired();
        });
    }

    /**
     * @return \Traversable|Package[]
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->packages);
    }

    public function offsetExists($offset)
    {
        return isset($this->packages[$offset]);
    }

    /**
     * @param string $offset
     * @return Package
     */
    public function offsetGet($offset)
    {
        return $this->packages[$offset];
    }

    /**
     * @param string $offset
     * @param Package $value
     */
    public function offsetSet($offset, $value)
    {
        $this->packages[$offset] = $value;
    }

    /**
     * @param string $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->packages[$offset]);
    }
}
