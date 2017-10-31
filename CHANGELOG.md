# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [2.1] - 2017-10-31
### Changed
- Oxid installation script updated
- changed oxid temp dir to "/tmp/oxid_tmp" for all container
- Updated php config and remove opcache error
- removed typo in variable name

### Added
- Add my.cnf file to all container for better mysql handline
- extended mysql 5.5 container and changed default charset and collation to utf-8
- Added container for php5.5 (unstable)
- enabled xdebug for remote debugging with PHPStorm
- added database dumps to project

## [2.0] - 2017-10-20
### Added 
- CHANGELOG.md
- Support new PHP versions (PHP7.0 and PHP5.6)
- Added composer.phar pre installed to PHP5.6 and PHP7.0
- Added installer to kickstart your development (you choose the version)

### Changed
- Updated the Documentation


## [1.1.5] - 2017-08-10
### Changed
- PHP Version to 5.6.31
- OXID Version to 4.10.5
- Default Theme is now Flow
- Updated demo data in oxid.sql to current OXID version
