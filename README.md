# Composer vendor scraper

Have you ever been stuck with a vendor folder but without composer.json and composer.lock files?
Hope you don't! But, don't ask me why, sometimes it just happens...

Then, here comes the Composer vendor scraper!

## A simple CLI command
The Composer vendor scraper is a simple CLI command that scraps your `vendor` folder in order to
generate a `composer.json` file that fit with the original `vendor` folder.

This command cannot regenerate the exact original `composer.json` file but can generate one
that just works and that you'll be able to modify according to your needs.


## Cookbook
### Installation
You can easily install Composer vendor scraper using `curl`:
```
$ curl -o- https://raw.githubusercontent.com/mtarld/composer-vendor-scaper/master/bin/composer-vendor-scraper.phar > composer-vendor-scraper.phar
```

### Usage
Once the command installed, you could use it that way:

```bash
$ php composer-vendor-scaper.phar

Description:
  Recreate a composer.json file from a vendor directory

Usage:
  bin/composer-vendor-scraper.phar [options] [--] [<vendor>]

Arguments:
  vendor                                   Vendor directory path [default: "vendor"]

Options:
      --require-all                        Add every package to require
      --require-root-only                  Add only packages that are not dependency of other packages to require
      --version-strategy=VERSION-STRATEGY  Version strategy (fixed, patch, minor, major)
      --out-file=OUT-FILE                  Generated file path [default: "composer.json"]
  -h, --help                               Display help for the given command. When no command is given display help for the bin/composer-vendor-scraper.phar command

Help:
  To know which version strategy to use, have a look at https://semver.org/
```
## Example
```bash
php composer-vendor-scraper.phar
                                                    
Vendor scrapper
===============
                                                    
 Root packages
 ----------------- --------- 
  Name              Version  
 ----------------- --------- 
  symfony/console   v5.2.0   
  symfony/finder    v5.2.0   
 ----------------- --------- 

 Dependency packages
 ---------------------------------- --------- 
  Name                               Version  
 ---------------------------------- --------- 
  psr/container                      1.0.0    
  symfony/service-contracts          v2.2.0   
  symfony/string                     v5.2.0   
 ---------------------------------- --------- 

 Would you like to add dependency packages to required packages? (yes/no) [no]:
 > yes

 Select dependency packages to add to root packages:
  [0] psr/container
  [1] symfony/service-contracts
  [2] symfony/string
 > symfony/string

 Required packages
 ----------------- --------- 
  Name              Version  
 ----------------- --------- 
  symfony/console   v5.2.0   
  symfony/finder    v5.2.0   
  symfony/string    v5.2.0   
 ----------------- ---------

 Required packages
 ----------------- --------- 
  Name              Version  
 ----------------- --------- 
  symfony/console   v5.2.0   
  symfony/finder    v5.2.0   
  symfony/string    v5.2.0   
 ----------------- --------- 

 Choose a version strategy for symfony/console [fixed]:
  [0] fixed
  [1] patch
  [2] minor
  [3] major
 > patch

 Choose a version strategy for symfony/finder [fixed]:
  [0] fixed
  [1] patch
  [2] minor
  [3] major
 > minor

 Choose a version strategy for symfony/string [fixed]:
  [0] fixed
  [1] patch
  [2] minor
  [3] major
 > major

 Result
 ----------------- ---------- 
  Name              Version   
 ----------------- ---------- 
  symfony/console   ^5.2.0.0  
  symfony/finder    ^5.2.0    
  symfony/string    ^5.2      
 ----------------- ---------- 

 Proceed? (yes/no) [yes]:
 > yes


 [OK] composer.json successfully created! Feel free to modify it!
```

## Contributing
Please read [CONTRIBUTING.md](CONTRIBUTING.md) for details on our code of conduct, and the process for submitting pull requests to us.

After writing your fix/feature, you can run following commands in order to generate the updated PHAR.

```bash
# Install dev dependencies
$ composer install

# Build the new PHAR
$ composer build-phar
```

## Authors
 - Mathias Arlaud - [mtarld](https://github.com/mtarld) - <mathias(dot)arlaud@gmail(dot)com>
