# Changelog

## v0.7 - 2025-11-28

### What's Changed

* fix: updates badge callbacks to return nullable integer values by @achyutkneupane in https://github.com/achyutkneupane/filament-log-viewer/pull/65
* docs: adds bug report template for issue tracking by @achyutkneupane in https://github.com/achyutkneupane/filament-log-viewer/pull/66
* feat: adds configuration for maximum log file size by @achyutkneupane in https://github.com/achyutkneupane/filament-log-viewer/pull/78
* feat: adds log viewer plugin retrieval logic by @achyutkneupane in https://github.com/achyutkneupane/filament-log-viewer/pull/79
* fix: trims leading slash from URL for route generation issue by @achyutkneupane in https://github.com/achyutkneupane/filament-log-viewer/pull/80
* refactor: improves log file reading by using streaming by @achyutkneupane in https://github.com/achyutkneupane/filament-log-viewer/pull/81

## v1.5.3 - 2025-11-16

### What's Changed

* fix: updates misplacement of `from` and `until` keys by @relisiuol in https://github.com/achyutkneupane/filament-log-viewer/pull/74

### New Contributors

* @relisiuol made their first contribution in https://github.com/achyutkneupane/filament-log-viewer/pull/74

## v1.5.2 - 2025-10-13

### What's Changed

* Improves Persian (Farsi) translations by @FaridAghili in https://github.com/achyutkneupane/filament-log-viewer/pull/73

### New Contributors

* @FaridAghili made their first contribution in https://github.com/achyutkneupane/filament-log-viewer/pull/73

## v1.5.1 - 2025-10-09

### What's Changed

* Adds PHPStan analysis step to CI workflow by @achyutkneupane in https://github.com/achyutkneupane/filament-log-viewer/pull/71
* PHPStan type-safe level `max` by @achyutkneupane in https://github.com/achyutkneupane/filament-log-viewer/pull/72

## v1.5 - 2025-10-07

### What's Changed

* docs: adds bug report template for issue tracking by @achyutkneupane in https://github.com/achyutkneupane/filament-log-viewer/pull/67
* chore: bug report template from markdown to YAML format by @achyutkneupane in https://github.com/achyutkneupane/filament-log-viewer/pull/68
* feat: adds PT_BR translation by @jeffersongoncalves in https://github.com/achyutkneupane/filament-log-viewer/pull/69
* feat: Parses and displays JSON with CodeEntry and Phiki by @achyutkneupane in https://github.com/achyutkneupane/filament-log-viewer/pull/70

### New Contributors

* @jeffersongoncalves made their first contribution in https://github.com/achyutkneupane/filament-log-viewer/pull/69

## v1.4.5 - 2025-10-03

### What's Changed

* docs: adds bug report template for issue tracking by @achyutkneupane in https://github.com/achyutkneupane/filament-log-viewer/pull/67
* chore: bug report template from markdown to YAML format by @achyutkneupane in https://github.com/achyutkneupane/filament-log-viewer/pull/68

## v1.4.4 - 2025-10-01

### What's Changed

* Normalizes path separators before removing logs prefix by @inerba in https://github.com/achyutkneupane/filament-log-viewer/pull/64

### New Contributors

* @inerba made their first contribution in https://github.com/achyutkneupane/filament-log-viewer/pull/64

## v1.4.3 - 2025-10-01

### What's Changed

* Enhances navigation group and label handling by @achyutkneupane in https://github.com/achyutkneupane/filament-log-viewer/pull/62
* Updates log count method to return null for zero counts by @achyutkneupane in https://github.com/achyutkneupane/filament-log-viewer/pull/63

## v0.6 - 2025-09-29

### What's Changed

* ci: Changes to PR Lint Github Action file by @achyutkneupane in https://github.com/achyutkneupane/filament-log-viewer/pull/59
* fix: return array values from filtered logs by @achyutkneupane in https://github.com/achyutkneupane/filament-log-viewer/pull/58

**Full Changelog**: https://github.com/achyutkneupane/filament-log-viewer/compare/v0.5.3...v0.6

## v1.4.2 - 2025-09-27

### 🎉 New Features

- feat: Add new language translations (#51) by @amidesfahani
  This pull request introduces translation files for several new languages to improve localization. 🌍

Added support for:

- Arabic (ar)
- Spanish (es)
- German (de)
- Farsi (fa)
- Hebrew (he)
- French (fr)
- Italian (it)
- Portuguese (pt)

### 🏗️ Build System

- ci: updates GitHub Actions workflow to use `pull_request` event (#54) by @achyutkneupane
  Changes the trigger event for the PR linting workflow and updates the checkout action version.
- ci: updates SonarQube scan action to version 6 (#55) by @achyutkneupane

### 🪚 Refactors

- refactor: updates coverage exclusions in `sonar-project.properties` (#56) by @achyutkneupane
  
- refactor: excludes language files from PHPUnit tests (#57) by @achyutkneupane
  

## v1.4.1 - 2025-09-27

- no changes

## v1.4 - 2025-09-27

- no changes

## v1.3.1 - 2025-08-19

### 🎉 New Features

- feat: Fetches and deleted nested log files (#46) by @achyutkneupane
  Closes #45
- feat: filter the logs by log file (#48) by @achyutkneupane
  Closes #44
  Closes #41

### 🐛 Bug Fixes

- fix: fixes the Unexpected encoding on plain text email (#43) by @achyutkneupane
  Closes #42

### 🏗️ Build System

- ci: updates GitHub Actions to run with matrix strategy (#47) by @achyutkneupane
  
- ci: Body added in PR template (#49) by @achyutkneupane
  

## v1.3.0 - 2025-08-19

- no changes

## v1.2.1 - 2025-08-08

### 🎉 New Features

- feat: add mail log reader with plain text & HTML preview (#40) by @achyutkneupane

### 🔍 Tests

- test: Sonar cloud exclusions added & more tests added (#39) by @achyutkneupane

## v1.2.0 - 2025-08-08

- no changes

## v1.1.3 - 2025-08-06

### 🏗️ Build System

- ci: Tests now generate coverage and and run sonarqube (#36) by @achyutkneupane
- ci: Coverage will be generated as clover XML (#38) by @achyutkneupane

### 🔍 Tests

- test: Unit Tests added for Log class (#35) by @achyutkneupane
- test: Feature tests added (#37) by @achyutkneupane

## v1.1.2 - 2025-08-06

- no changes

## v1.1.1 - 2025-08-05

### 🎉 New Features

- feat: Tabs added to filter log level (#28) by @achyutkneupane
- feat: Refresh Button added as header action (#30) by @achyutkneupane
- feat: Pagination added in log table (#34) by @achyutkneupane

### 🐛 Bug Fixes

- fix: Stack Trace parsing issue fixed (#32) by @achyutkneupane

### 🪚 Refactors

- refactor: Log Level Tabs moved to Trait (#29) by @achyutkneupane
- refactor: remove sushi and adapt for Filament v4 custom data (#31) by @achyutkneupane

### 📚 Documentation Changes

- docs: README for v4 updated (#33) by @achyutkneupane

## v1.1.0 - 2025-08-05

### 📚 Documentation Changes

- docs: Readme with versions for v1 (#26) by @achyutkneupane

## v1.0.0 - 2025-08-04

### 🎉 New Features

- feat: Filament v4 support (#24) by @achyutkneupane
- feat: log level filter added (#25) by @achyutkneupane

## v0.5.3 - 2025-08-04

### 🏗️ Build System

- ci: Releases version on workflow dispatch (#19) by @achyutkneupane

## v0.5.2 - 2025-08-04

### 🏗️ Build System

- ci: `filament-v3` as default branch (#17) by @achyutkneupane
- ci: release branch updated (#18) by @achyutkneupane

## v0.5.1-filament-v3.0 - 2025-08-04

### 🏗️ Build System

- ci: `filament-v3` as default branch (#17) by @achyutkneupane

## v0.5.0 - 2025-07-26

### 🎉 New Features

- feat: Configurable polling added in log-table (#15) by @achyutkneupane

## v0.4.3 - 2025-07-26

### 🪚 Refactors

- refactor: Clear action moved to page header instead of table header (#14) by @achyutkneupane

## v0.4.2 - 2025-07-25

### 📚 Documentation Changes

- docs: Badges added in README (#13) by @achyutkneupane

## v0.4.1 - 2025-07-23

- no changes

## v0.4.0 - 2025-07-23

### 🎉 New Features

- feat: Date Range Filter added  (#12) by @achyutkneupane

## v0.3.1 - 2025-07-23

### 🧹 Chores

- chore: PHPStan temporarily removed (#11) by @achyutkneupane

## v0.3.0 - 2025-07-23

### 🎉 New Features

- feat: Clear Logs Action (#10) by @Dipesh79

## v0.2.8 - 2025-07-23

- no changes

## v0.2.7 - 2025-07-22

### 🏗️ Build System

- ci: Changelog fix (2a3254c4350eff89b4e588e2ac4bf8fe65a7759a) by @achyutkneupane

## v0.2.3 - 2025-07-22

### Build System

- Changelog package changed for author tag

### Other Changes

- Update CHANGELOG
- Merge pull request [#7](https://github.com/achyutkneupane/filament-log-viewer/pull/7) by [achyutkneupane](https://github.com/achyutkneupane) from achyutkneupane/ci/changelog-update

## v0.2.2 - 2025-07-22

### [0.2.2](https://github.com/achyutkneupane/filament-log-viewer/compare/v0.2.1...v0.2.2) (2025-07-22)

### Bug Fixes

* Auth check removed in authorization ([e9de419](https://github.com/achyutkneupane/filament-log-viewer/commit/e9de419b5cdce48750745eb62f701795148e4b78))
* Users are authorized by default ([#6](https://github.com/achyutkneupane/filament-log-viewer/issues/6)) ([a45c8f7](https://github.com/achyutkneupane/filament-log-viewer/commit/a45c8f743332c7ebcf93bbcc641ec45cc7af3b32))

### Code Refactoring

* Pint fixes ([9121fb9](https://github.com/achyutkneupane/filament-log-viewer/commit/9121fb9e99b5393be83f94b46fe94e8d2fa24456))

## v0.2.1 - 2025-07-21

### [0.2.1](https://github.com/achyutkneupane/filament-log-viewer/compare/v0.2.0...v0.2.1) (2025-07-21)

### Documentation

* Filters added in docs ([6b791b8](https://github.com/achyutkneupane/filament-log-viewer/commit/6b791b8456fa0a72ef7aec4da7cf3126cada0b76))
* Filters documentation and images added ([#5](https://github.com/achyutkneupane/filament-log-viewer/issues/5)) ([d7ad27b](https://github.com/achyutkneupane/filament-log-viewer/commit/d7ad27b286b08324692c55c1f4ad918ec10c84be))
* Social image added ([1a827dd](https://github.com/achyutkneupane/filament-log-viewer/commit/1a827dd5769dd655bc4aa5b1d7bd375d35645e8d))

## v0.2.0 - 2025-07-21

### [0.2.0](https://github.com/achyutkneupane/filament-log-viewer/compare/v0.1.0...v0.2.0) (2025-07-21)

#### Features

* Log-level wise filers added ([#4](https://github.com/achyutkneupane/filament-log-viewer/issues/4)) ([ee74394](https://github.com/achyutkneupane/filament-log-viewer/commit/ee743947580efc5fc2f2a6549687bd4329cd1737))
* Tab-wise debugging added ([2b41e4c](https://github.com/achyutkneupane/filament-log-viewer/commit/2b41e4c42e9e7b9f65a4926f777d2d1128da8879))
* Tabs added according to log level ([6d0a55a](https://github.com/achyutkneupane/filament-log-viewer/commit/6d0a55a35764e0b5d2a3e53e348fbbb10aa95845))

## v0.1.0 - 2025-07-21

### [0.1.0](https://github.com/achyutkneupane/filament-log-viewer/compare/v0.0.1...v0.1.0) (2025-07-21)

#### Features

* Color for environment ([d7dc4df](https://github.com/achyutkneupane/filament-log-viewer/commit/d7dc4dfef7b32e8868357942176fc39de1aad54c))
* Message wrapped in table ([357b121](https://github.com/achyutkneupane/filament-log-viewer/commit/357b121f67eef854b4b3a7e09748d61e1625d3d3))
* Message wrapped in table ([#3](https://github.com/achyutkneupane/filament-log-viewer/issues/3)) ([4adceb6](https://github.com/achyutkneupane/filament-log-viewer/commit/4adceb6b4f203ec8994ce8bbd58d27e2e449ba6f))
* Searchable message ([d3b3d0a](https://github.com/achyutkneupane/filament-log-viewer/commit/d3b3d0a228ab99228bbd7783bc0c66d3b11c367f))

## v0.0.1 - 2025-07-21

### [0.0.1](https://github.com/achyutkneupane/filament-log-viewer/compare/v0.0.0...v0.0.1) (2025-07-21)
