<?php

require __DIR__.'/vendor/autoload.php';

$pharFile = __DIR__.'/bin/composer-vendor-scraper.phar';

if (file_exists($pharFile)) {
    unlink($pharFile);
}

$phar = new Phar($pharFile);

$finder = new Symfony\Component\Finder\Finder();
$finder
    ->files()
    ->ignoreVCS(true)
    ->name('*.php')
    ->in('.')
    ->exclude('bin')
    ->filter(static function (SplFileInfo $file): bool {
        return __FILE__ !== $file->getRealPath();
    })
;

foreach ($finder as $file) {
    $phar->addFromString(
        sprintf('%s/%s', $file->getRelativePath(), $file->getFilename()),
        file_get_contents($file->getRealPath())
    );
}

$phar->setDefaultStub('src/index.php');
