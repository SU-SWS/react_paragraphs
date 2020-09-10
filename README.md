# [React Paragraphs](https://github.com/SU-SWS/react_paragraphs)
##### Version: 8.x

[![Maintainability](https://api.codeclimate.com/v1/badges/9e90958f537c6d756e13/maintainability)](https://codeclimate.com/github/SU-SWS/react_paragraphs/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/9e90958f537c6d756e13/test_coverage)](https://codeclimate.com/github/SU-SWS/react_paragraphs/test_coverage)
[![CircleCI](https://circleci.com/gh/SU-SWS/react_paragraphs.svg?style=svg)](https://circleci.com/gh/SU-SWS/react_paragraphs)

Maintainers: [Mike Decker](https://github.com/pookmish)

Changelog: [Changelog.md](CHANGELOG.md)

Description
---

This module provides a UI for editing paragrahs in Drupal that is unlike any before it.

Installation
---

Install this module like any other module. [See Drupal Documentation](https://drupal.org/documentation/install/modules-themes/modules-8)

Configuration
---

Nothing special needed.


Troubleshooting
---

If you are experiencing issues with this module try reverting the feature first. If you are still experiencing issues try posting an issue on the GitHub issues page.

Developer
---

For React development, change directories into the `js` directory and run either
`yarn` or `npm install`. This will install all dependencies for webpack and
React. For faster development with the React framework, you can run `yarn dev`
or `npm run dev` which will create a live reloading url with the react widget.
To compile the css & js for use in a Drupal site, use `yarn build` or `npm run
build`. These commands will re-compile the appropriate files. Then ensure to
clear javascript and browser caches on the Drupal site being used.


Contribution / Collaboration
---

You are welcome to contribute functionality, bug fixes, or documentation to this module. If you would like to suggest a fix or new functionality you may add a new issue to the GitHub issue queue or you may fork this repository and submit a pull request. For more help please see [GitHub's article on fork, branch, and pull requests](https://help.github.com/articles/using-pull-requests)


Releases
---

Steps to build a new release:
- Checkout the latest commit from the `8.x-2.x` branch.
- Create a new branch for the release.
- Commit any necessary changes to the release branch.
  -  These may include, but are not necessarily limited to:
    - Update the version in any `info.yml` files, including in any submodules.
    - Update the CHANGELOG to reflect the changes made in the new release.
- Make a PR to merge your release branch into `master`
- Give the PR a semver-compliant label, e.g., (`patch`, `minor`, `major`).  This may happen automatically via Github actions (if a labeler action is configured).
- When the PR is merged to `master`, a new tag will be created automatically, bumping the version by the semver label.
- The github action is built from: [semver-release-action](https://github.com/K-Phoen/semver-release-action), and further documentation is available there.
