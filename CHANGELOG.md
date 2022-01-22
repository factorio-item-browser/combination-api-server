# Changelog

## 1.2.1 - 2022-01-22

### Added

- CORS headers.

### Fixed

- Authorization via Api-Key in the OpenAPI document.

## 1.2.0 - 2022-01-08

### Added

- Google Analytics tracking for the requests.

### Changed

- Upgraded the dependencies. Modernized codebase.
- Doctrine configuration from XML files to attributes.
- Logging library from Laminas Log to Monolog.

### Removed

- Support for PHP 8.0. The minimal required version is now PHP 8.1.

## 1.1.2 - 2021-07-21

### Fixed

- Validation request returning a combination as invalid if a request to the Factorio mod Portal has failed. Now a 503
  status code is returned in these cases.

## 1.1.1 - 2021-06-21

### Added

- Sorting of the mods by their names in the combination responses.

## 1.1.0 - 2021-05-27

### Added

- Parameter `first` to the job list request.

## 1.0.2 - 2021-05-24

### Changed

- PHP version from 7.4 to 8.0.

## 1.0.1 - 2021-04-01

### Added

- Support for new ~ dependency modifier as of [Factorio 1.1.28](https://forums.factorio.com/viewtopic.php?f=3&t=97273).

## 1.0.0 - 2021-02-16

- Initial release of the Combination API Server project.
