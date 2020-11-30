<?php

require __DIR__.'/../vendor/autoload.php';

use App\Package;
use App\PackagePool;
use App\VersionStrategy;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\SingleCommandApplication;
use Symfony\Component\Console\Style\SymfonyStyle;

(new SingleCommandApplication())
    ->setDescription('Recreate a composer.json file from a vendor directory')
    ->setHelp('To know which version strategy to use, have a look at https://semver.org/')
    ->addArgument('vendor', InputArgument::OPTIONAL, 'Vendor directory path', 'vendor')
    ->addOption('require-all', null, InputOption::VALUE_NONE, 'Add every package to require')
    ->addOption('require-root-only', null, InputOption::VALUE_NONE, 'Add only packages that are not dependency of other packages to require')
    ->addOption('version-strategy', null, InputOption::VALUE_REQUIRED, sprintf('Version strategy (%s)', implode(', ', VersionStrategy::STRATEGIES)))
    ->addOption('out-file', null, InputOption::VALUE_REQUIRED, 'Generated file path', 'composer.json')
    ->setCode(function (InputInterface $input, OutputInterface $output) {
        $io = new SymfonyStyle($input, $output);

        $io->title("Vendor scrapper");

        if (null !== $input->getOption('version-strategy') && !in_array($input->getOption('version-strategy'), VersionStrategy::STRATEGIES, true)) {
            $io->error(sprintf(
                "Unexpected value '%s' for 'version-strategy', you should use one of %s",
                $input->getOption('version-strategy'),
                implode(', ', VersionStrategy::STRATEGIES)
            ));

            return 1;
        }

        if (false === $content = @file_get_contents($filepath = $input->getArgument('vendor').'/composer/installed.json')) {
            $io->error(sprintf('Cannot find or read %s', $filepath));

            return 1;
        }

        if (null === $installed = json_decode($content, true)) {
            $io->error(sprintf('%s does not contain valid JSON content', $filepath));

            return 1;
        }

        $packages = PackagePool::createFromInstalled($installed);

        $io->text('Root packages');
        $io->table(
            ['Name', 'Version'],
            array_map(static function (Package $package): array {
                return [$package->getName(), $package->getVersion()];
            }, $packages->getRequired())
        );

        $io->text('Dependency packages');
        $io->table(
            ['Name', 'Version'],
            array_map(static function (Package $package): array {
                return [$package->getName(), $package->getVersion()];
            }, $packages->getDependencies())
        );

        if ($input->getOption('require-all')) {
            foreach ($packages as $package) {
                $package->setIsRequired(true);
            }
        } elseif (!$input->getOption('require-root-only') && $io->confirm('Would you like to add dependency packages to required packages?', false)) {
            $question = (new ChoiceQuestion(
                'Select dependency packages to add to root packages',
                array_values(array_map(static function (Package $package): string {
                    return $package->getName();
                }, $packages->getDependencies()))
            )
            )->setMultiselect(true);

            foreach ($io->askQuestion($question) as $newRootPackage) {
                $packages[$newRootPackage]->setIsRequired(true);
            }
        }

        $io->text('Required packages');
        $io->table(
            ['Name', 'Version'],
            array_map(static function (Package $package): array {
                return [$package->getName(), $package->getVersion()];
            }, $packages->getRequired())
        );

        foreach ($packages->getRequired() as $package) {
            /** @var Package $package */
            $packages[$name = $package->getName()] = $package->setVersionByStrategy(
                $input->getOption('version-strategy') ?? $io->choice(sprintf('Choose a version strategy for %s', $name), VersionStrategy::STRATEGIES, VersionStrategy::STRATEGY_FIXED)
            );
        }

        $io->text('Result');
        $io->table(
            ['Name', 'Version'],
            array_map(static function (Package $package): array {
                return [$package->getName(), $package->getVersion()];
            }, $packages->getRequired())
        );

        if (!$io->confirm('Proceed?')) {
            $io->warning('File creation aborted!');

            return 1;
        }

        $outFile = $input->getOption('out-file');
        if (file_exists($outFile) && !$io->confirm(sprintf("File '%s' already exist, do you want to override it?", $outFile), false)) {
            $io->warning('File creation aborted!');

            return 1;
        }

        file_put_contents($input->getOption('out-file'), json_encode([
            'require' => array_map(static function (Package $package): string {
                return $package->getVersion();
            }, $packages->getRequired()),
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        $io->success(sprintf('%s successfully created! Feel free to modify it!', $outFile));

        return 0;
    })
    ->run()
;
