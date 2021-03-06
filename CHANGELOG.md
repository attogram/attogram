# Attogram Framework CHANGELOG

- Repository: <https://github.com/attogram/attogram>
- Click the version number to view changes since the previous release

## [Unreleased]

- Work in progress...

## [v0.8.2] - 2016-07-24

- added web virtual directory mime types: ogg, oga, ogv, json
- unit tests for
  [NullDatabase.php](https://github.com/attogram/attogram/blob/master/Attogram/NullDatabase.php)
- Attogram methods now have single return types, no mixed
- minor updates and codeclimate setup

## [v0.8.1] - 2016-07-21

- fixed config for databaseName
- Better Markdown parsing with
  [Parsedown Extra](https://github.com/erusev/parsedown-extra)

## [v0.8.0] - 2016-07-21

- fixed loader to properly get main config.php and module configs at startup
- single return types for Attogram functions, no more mixed types

## [v0.7.9] - 2016-07-21

- `shutdown()` function for all your `exit;` needs
- use DIRECTORY_SEPARATOR for per-OS filesystem goodness
- renamed db interface to `\Attogram\AttogramDatabaseInterface`
- continued code style cleanups and minor bugfixes

## [v0.7.8] - 2016-07-17

- merged in filesystem object into main Attogram object
- use [`Symfony Request`](http://symfony.com/doc/current/components/http_foundation/introduction.html)
  for \_GET, \_POST and \_SERVER
- continued style updates for PSR-1, PSR-2, and more
- updated [codeclimate](https://codeclimate.com/github/attogram/attogram)
  engine setup
- various bugfixes and refactors

## [v0.7.7] - 2016-07-14

- Allow `.html` actions and admin_actions
- continued cleanup

## [v0.7.6] - 2016-07-12

- continued refactoring/renaming/cleanup for PSR-1 + PSR-2 Code style conventions
- Merged AttogramFS class into main Attogram class
- new `modules/disabled/` directory as a holding area for disabled modules

## [v0.7.5] - 2016-07-11

- lots of refactoring/cleanup for PSR-1 + PSR-2 Code style conventions
- removed `attogram/logger.php`, now using \\Psr\\Log\\NullLogger
- Codacy integration: <https://www.codacy.com/app/attogram-project/attogram/dashboard>
- Codeclimate integration: <https://codeclimate.com/github/attogram/attogram>
- Project stat badges on README

## [v0.7.4] - 2016-07-09

- updates to Travis CI integration: <https://travis-ci.org/attogram/attogram>
  - for PHP 5.3 and PHP 5.4 use old phpunit, do not do code coverage
  - for PHP 5.5+ use new phpunit, do code coverage
- continued testing setup

## [v0.7.3] - 2016-07-08

- Removed all modules from main distribution.  Core modules now have their own repository
  - <https://github.com/attogram/attogram-database>
  - <https://github.com/attogram/attogram-user>
  - <https://github.com/attogram/attogram-info>
- setup framework for Travis CI integration: <https://travis-ci.org/attogram/attogram>
- setup framework for future phpunit Unit Testing
- bugfixes on numeric actions, missing db object, empty menus
- pimp buttons on the [README](https://github.com/attogram/attogram/blob/master/README.md)

## [v0.7.2] - 2016-07-07

- bugfix to allow numeric actions ( ./actions/123.php, ./actions/123.md, etc. )
- New [TODO](./TODO.md): [Issue #13](https://github.com/attogram/attogram/issues/13)
- Update [`./README.md`](./README.md)

## [v0.7.1] - 2016-07-05

- updated formatting and links in this `./CHANGELOG.md` file
- updated [`./TODO.md`](./TODO.md) with new + existing
  [open issues](https://github.com/attogram/attogram/issues) from github

## [v0.7.0] - 2016-07-05

- started this `CHANGELOG.md`!
- started [`TODO.md`](./TODO.md)
- fix for proper Bootstrap container div on editor pages
- allow HTML in event log messages
- renamed core modules to [`./modules/_attogram/`](./modules/_attogram/),
  [`./modules/_database/`](./modules/_database/),
  and [`./modules/_user/`](./modules/_user/)
- moved bootstrap, jquery and css files back to [`./public/web/`](./public/web/)
  for faster loading/caching
- new attogram function:
  [`doCacheHeaders($file)`](https://github.com/attogram/attogram/blob/2d0bbcc05d1ccecae31c8da247facd3e24306fed/attogram/attogram.php#L379)
  sets Last-Modified and Etag headers, and responds to HTTP_IF_MODIFIED_SINCE /
  HTTP_IF_NONE_MATCH requests for better caching of virtual web directory files
- new attogram function:
  [`getMarkdown($file)`](https://github.com/attogram/attogram/blob/2d0bbcc05d1ccecae31c8da247facd3e24306fed/attogram/attogram.php#L427)
  returns string of HTML version of markdown file
- misc refactoring and design tests

## [v0.6.9] - 2016-07-03

- Event log system added
- UTF-8 header fixes
- new public actions:
  [`./modules/_attogram/actions/readme.php`](./modules/_attogram/actions/readme.php),
  [`./modules/_attogram/actions/license.php`](./modules/_attogram/actions/license.php)
- new hard-coded homepage shows when no `home.php` or `home.md` actions found
- misc bug fixes and design updates
- announcement: <https://www.patreon.com/posts/6016378>

## [v0.6.8] - 2016-07-01

## [v0.6.7] - 2016-06-30

## [v0.6.6] - 2016-06-28

## [v0.6.5] - 2016-06-26

## [v0.6.4] - 2016-06-21

## [v0.6.3] - 2016-06-20

## [v0.6.2] - 2016-06-17

## [v0.6.1] - 2016-06-16

## [v0.6.0] - 2016-06-15

## [v0.5.9] - 2016-06-14

## [v0.5.8] - 2016-06-13

## [v0.5.7] - 2016-06-12

## [v0.5.6] - 2016-06-10

## [v0.5.5] - 2016-06-09

## [v0.5.4] - 2016-06-08

## [v0.5.3] - 2016-06-07

## [v0.5.2] - 2016-06-02

## [v0.5.1] - 2016-06-02

## [v0.5.0] - 2016-06-01

## [v0.4.9] - 2016-05-30

## [v0.4.8] - 2016-05-29

## [v0.4.7] - 2016-05-28

## [v0.4.6] - 2016-05-25

## [v0.4.5] - 2016-05-23

## [v0.4.4] - 2016-05-22

## [v0.4.3] - 2016-05-21

## [v0.4.2] - 2016-05-20

## [v0.4.1] - 2016-05-19

## [v0.4.0] - 2016-05-19

## [v0.3.9] - 2016-05-17

## [v0.3.8] - 2016-05-17

## [v0.3.7] - 2016-05-16

## [v0.3.6] - 2016-05-16

## [v0.3.5] - 2016-05-15

## [v0.3.4] - 2016-05-15

## [v0.3.3] - 2016-05-15

## [v0.3.2] - 2016-05-15

## [v0.3.1] - 2016-05-14

## [v0.3.0] - 2016-05-14

## [v0.2.9] - 2016-05-14

## [v0.2.8] - 2016-05-13

## [v0.2.7] - 2016-05-13

## [v0.2.6] - 2016-05-12

## [v0.2.5] - 2016-05-11

## [v0.2.4] - 2016-05-11

## [v0.2.3] - 2016-05-10

## [v0.2.2] - 2016-05-10

## [v0.2.1] - 2016-05-10

## [v0.2.0] - 2016-05-10

## [v0.1.9] - 2016-05-10

## [v0.1.8] - 2016-05-09

## [v0.1.7] - 2016-05-08

## [v0.1.6] - 2016-05-05

## [v0.1.5] - 2016-05-04

## [v0.1.4] - 2016-05-04

## [v0.1.3] - 2016-05-03

## [v0.1.2] - 2016-05-03

## [v0.1.1] - 2016-05-02

## [v0.1.0] - 2016-05-02

## [v0.0.9]- 2016-05-01

## [v0.0.8] - 2016-04-30

## [v0.0.7] - 2016-04-29

## [v0.0.6] - 2016-04-26

## [v0.0.5] - 2014-09-15

## [v0.0.4] - 2014-09-12

## [v0.0.3] - 2014-09-11

## [v0.0.2] - 2014-09-09

## [v0.0.1] - 2014-09-05

## [v0.0.0] - 2014-09-04

[Unreleased]: https://github.com/attogram/attogram/compare/v0.8.2...HEAD
[v0.8.2]: https://github.com/attogram/attogram/compare/v0.8.1...v0.8.2
[v0.8.1]: https://github.com/attogram/attogram/compare/v0.8.0...v0.8.1
[v0.8.0]: https://github.com/attogram/attogram/compare/v0.7.9...v0.8.0
[v0.7.9]: https://github.com/attogram/attogram/compare/v0.7.8...v0.7.9
[v0.7.8]: https://github.com/attogram/attogram/compare/v0.7.7...v0.7.8
[v0.7.7]: https://github.com/attogram/attogram/compare/v0.7.6...v0.7.7
[v0.7.6]: https://github.com/attogram/attogram/compare/v0.7.5...v0.7.6
[v0.7.5]: https://github.com/attogram/attogram/compare/v0.7.4...v0.7.5
[v0.7.3]: https://github.com/attogram/attogram/compare/v0.7.2...v0.7.3
[v0.7.2]: https://github.com/attogram/attogram/compare/v0.7.1...v0.7.2
[v0.7.1]: https://github.com/attogram/attogram/compare/v0.7.0...v0.7.1
[v0.7.0]: https://github.com/attogram/attogram/compare/v0.6.9...v0.7.0
[v0.6.9]: https://github.com/attogram/attogram/compare/3b97137...v0.6.9
[v0.6.8]: https://github.com/attogram/attogram/compare/v0.6.7...3b97137
[v0.6.7]: https://github.com/attogram/attogram/compare/v0.6.6...v0.6.7
[v0.6.6]: https://github.com/attogram/attogram/compare/v0.6.5...v0.6.6
[v0.6.5]: https://github.com/attogram/attogram/compare/v0.6.4...v0.6.5
[v0.6.4]: https://github.com/attogram/attogram/compare/v0.6.3...v0.6.4
[v0.6.3]: https://github.com/attogram/attogram/compare/v0.6.2...v0.6.3
[v0.6.2]: https://github.com/attogram/attogram/compare/v0.6.1...v0.6.2
[v0.6.1]: https://github.com/attogram/attogram/compare/v0.6.0...v0.6.1
[v0.6.0]: https://github.com/attogram/attogram/compare/v0.5.9...v0.6.0
[v0.5.9]: https://github.com/attogram/attogram/compare/v0.5.8...v0.5.9
[v0.5.8]: https://github.com/attogram/attogram/compare/v0.5.7...v0.5.8
[v0.5.7]: https://github.com/attogram/attogram/compare/v0.5.6...v0.5.7
[v0.5.6]: https://github.com/attogram/attogram/compare/v0.5.5...v0.5.6
[v0.5.5]: https://github.com/attogram/attogram/compare/v0.5.4...v0.5.5
[v0.5.4]: https://github.com/attogram/attogram/compare/v0.5.3...v0.5.4
[v0.5.3]: https://github.com/attogram/attogram/compare/v0.5.2...v0.5.3
[v0.5.2]: https://github.com/attogram/attogram/compare/666cef6...v0.5.2
[v0.5.1]: https://github.com/attogram/attogram/compare/b3a0bf2...666cef6
[v0.5.0]: https://github.com/attogram/attogram/compare/35825ae...b3a0bf2
[v0.4.9]: https://github.com/attogram/attogram/compare/97dcdaf...35825ae
[v0.4.8]: https://github.com/attogram/attogram/compare/56eb889...97dcdaf
[v0.4.7]: https://github.com/attogram/attogram/compare/87d9a83...56eb889
[v0.4.6]: https://github.com/attogram/attogram/compare/3cf6262...87d9a83
[v0.4.5]: https://github.com/attogram/attogram/compare/446ad3c...3cf6262
[v0.4.4]: https://github.com/attogram/attogram/compare/bb04ce8...446ad3c
[v0.4.3]: https://github.com/attogram/attogram/compare/fe6b232...bb04ce8
[v0.4.2]: https://github.com/attogram/attogram/compare/0bda689...fe6b232
[v0.4.1]: https://github.com/attogram/attogram/compare/93e2bfe...0bda689
[v0.4.0]: https://github.com/attogram/attogram/compare/7b48f5c...93e2bfe
[v0.3.9]: https://github.com/attogram/attogram/compare/bdb0682...7b48f5c
[v0.3.8]: https://github.com/attogram/attogram/compare/4477736...bdb0682
[v0.3.7]: https://github.com/attogram/attogram/compare/6f606b9...4477736
[v0.3.6]: https://github.com/attogram/attogram/compare/ab3bc31...6f606b9
[v0.3.5]: https://github.com/attogram/attogram/compare/0f4f56a...ab3bc31
[v0.3.4]: https://github.com/attogram/attogram/compare/f557264...0f4f56a
[v0.3.3]: https://github.com/attogram/attogram/compare/b293a91...f557264
[v0.3.2]: https://github.com/attogram/attogram/compare/aea13f8...b293a91
[v0.3.1]: https://github.com/attogram/attogram/compare/0b5e7b0...aea13f8
[v0.3.0]: https://github.com/attogram/attogram/compare/b66bcca...0b5e7b0
[v0.2.9]: https://github.com/attogram/attogram/compare/4eb8698...b66bcca
[v0.2.8]: https://github.com/attogram/attogram/compare/14e8900...4eb8698
[v0.2.7]: https://github.com/attogram/attogram/compare/e6d8a04...14e8900
[v0.2.6]: https://github.com/attogram/attogram/compare/533a77f...e6d8a04
[v0.2.5]: https://github.com/attogram/attogram/compare/4b367be...533a77f
[v0.2.4]: https://github.com/attogram/attogram/compare/1781c46...4b367be
[v0.2.3]: https://github.com/attogram/attogram/compare/b532b82...1781c46
[v0.2.2]: https://github.com/attogram/attogram/compare/4fe82ef...b532b82
[v0.2.1]: https://github.com/attogram/attogram/compare/88e1c73...4fe82ef
[v0.2.0]: https://github.com/attogram/attogram/compare/0893a7b...88e1c73
[v0.1.9]: https://github.com/attogram/attogram/compare/d1da012...0893a7b
[v0.1.8]: https://github.com/attogram/attogram/compare/a89bb59...d1da012
[v0.1.7]: https://github.com/attogram/attogram/compare/9f6ce9f...a89bb59
[v0.1.6]: https://github.com/attogram/attogram/compare/2d2f7c2...9f6ce9f
[v0.1.5]: https://github.com/attogram/attogram/compare/4149920...2d2f7c2
[v0.1.4]: https://github.com/attogram/attogram/compare/08f3f3d...4149920
[v0.1.3]: https://github.com/attogram/attogram/compare/ec5e753...08f3f3d
[v0.1.2]: https://github.com/attogram/attogram/compare/dbd2816...ec5e753
[v0.1.1]: https://github.com/attogram/attogram/compare/eb415ca...dbd2816
[v0.1.0]: https://github.com/attogram/attogram/compare/ffc3250...eb415ca
[v0.0.9]: https://github.com/attogram/attogram/compare/a76d150...ffc3250
[v0.0.8]: https://github.com/attogram/attogram/compare/a9c21b4...a76d150
[v0.0.7]: https://github.com/attogram/attogram/compare/060c5a4...a9c21b4
[v0.0.6]: https://github.com/attogram/attogram/compare/f13a7e4...060c5a4
[v0.0.5]: https://github.com/attogram/attogram/compare/d5f2428...f13a7e4
[v0.0.4]: https://github.com/attogram/attogram/compare/776b585...d5f2428
[v0.0.3]: https://github.com/attogram/attogram/compare/ec7f2c8...776b585
[v0.0.2]: https://github.com/attogram/attogram/compare/704757d...ec7f2c8
[v0.0.1]: https://github.com/attogram/attogram/compare/5f92a33...704757d
[v0.0.0]: https://github.com/attogram/attogram/tree/5f92a33
