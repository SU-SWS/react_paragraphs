# React Paragraphs

8.2.13
--------------------------------------------------------------------------------
_Release Date: 2022-07-27_

- Fixed focus issue with mui modal

8.2.12
--------------------------------------------------------------------------------
_Release Date: 2022-07-15_

- Increased dialog width for larger screens.

8.2.11
--------------------------------------------------------------------------------
_Release Date: 2022-07-08_

- Updated uasort to return integers
- fixed composer namespace to lowercase
- D8CORE-4972 Support link_attributes module (#114)
- D8CORE-2353 Make link fields equal width


8.x-2.10
--------------------------------------------------------------------------------
_Release Date: 2022-05-10_

- D8CORE-2331: Updated help text display for link and ckeditor fields (#112)
- Removed D8 Tests


8.x-2.9
--------------------------------------------------------------------------------
_Release Date: 2022-03-02_

- Removed patch which has been merged into drupal/paragraphs (#108)

8.x-2.7
--------------------------------------------------------------------------------
_Release Date: 2021-05-07_

- D8CORE-3743 Use limited cookie to temporarily save form data (#94) (023282d)

8.x-2.6
--------------------------------------------------------------------------------
_Release Date: 2021-03-05_

- D8CORE-3554 Patched paragraphs module to fix behaviors plugin setting (9d2ec85)
- D8CORE-3380 Allow media library widget to be sortable (#90) (1435ba9)
- D8CORE-3392 Sort the view displays based on their order in the view (#89) (d8fb8e8)

8.x-2.5
--------------------------------------------------------------------------------
_Release Date: 2021-02-08_

- Fixed name of rest config entity (e90df66)
- D8CORE-3263 Improve the media library widget with cardinality above 1 (#87) (e47936f)
- Updated circleci testing (#85) (d4478a7)

8.x-2.4
--------------------------------------------------------------------------------
_Release Date: 2020-12-04_

- D8CORE-1420 Fix the ckeditor toolbar when scrolling down the item. (#83) (142ccbd)
- Update tests for D9 phpunit (#82) (e432833)
- phpunit void return annoation (7e823de)
- Added config export for D9 Requirements (#81) (dff7625)

8.x-2.3
--------------------------------------------------------------------------------
_Release Date: 2020-11-06_

- D8CORE-2857 keep upper case characters in link fields (#78) (2c39332)
- Adjusted method typehinting to also accept null (for testing) (e31be83)
- Fixed media browser missing its bundle (a975773)
- D8CORE-2686 Paragraph and row behaviors UI (#77) (48451f4)
- Fix default values in autocomplete and allow special characters in text fields (#76) (d49c144)
- D8CORE-2852: Add ckeditor event listener for changes (#75) (631fac2)
- D8CORE-2709 Entity Reference Autocomplete & Select support (#74) (eca809f)

8.x-2.2
--------------------------------------------------------------------------------
_Release Date: 2020-10-05_

- Fixed paragraph entity form modal label (#72) (369e99a)
- Fixed logger message (#71) (e0a5cdc)
- D8CORE-2685 Discover behaviors defined in modules and themes yml (#70) (b0b7c7e)
- D8CORE-2150: Adjust link widget to mirror the php link widget (#69) (664aebb)
- Fix the upgrade for mismatched paragraph revisions (#68) (e6b40b2)

8.x-2.1
--------------------------------------------------------------------------------
_Release Date: 2020-09-15_

- Update react_paragraphs.install (#66) (6803b17)

8.x-2.0
--------------------------------------------------------------------------------
_Release Date: 2020-09-10_

- D8CORE-000: Keep all the users changes if the form does not validate (#62) (d201c5a)
- D8CORE-000: Added form settings to set the maximum column widths (#61) (a7badc3)
- D8CORE-2460: Decode double encoded json array (#60) (e988359)
- D8CORE-000: V2 React Paragraphs (#59) (be19ff8)
- initial 8.x-2.x branch (902bf6c)

8.x-1.11
--------------------------------------------------------------------------------
_Release Date: 2020-08-07_

- D8CORE-2225 Pass max length setting down to the widget (#57) (ed7fcc4)

8.x-1.10
--------------------------------------------------------------------------------
_Release Date: 2020-07-13_

- D8CORE-2329 Fix CKEditor widget when a user deletes all text (#53) (5b6133c)

8.x-1.9
--------------------------------------------------------------------------------
_Release Date: 2020-06-17_

- Allow unlimited media library cardinality (#50) (32b4d27)
- fixed link widget when the initial value is empty (e3b3d13)
- D8CORE-2106 Allowed higher cardinality for link fields (#48) (b26e62a)

8.x-1.8
--------------------------------------------------------------------------------
_Release Date: 2020-05-15_

- Use alter method and fixed the object in the react field (#46) (98db612)
- D8CORE-000: Allow post getFieldInfo altering. (#44) (45d397d)
- D8CORE-1943: Fix the link widget and how it handles the suggestions and absolute urls (#41) (99c21bb)
- D8CORE-1823: Fix link widget when the user inputs <front> (#40) (06e1d6e)

8.x-1.7
--------------------------------------------------------------------------------
_Release Date: 2020-04-16_

- Added error handeling for media library widget (#39)
- Check for value if a user didnt select a media item (#33)
- D8CORE-1548 Dont trigger form updated on component load (#34)
- D8CORE-1644 Added documentation for the release workflow
- D8CORE-1829 Visual tweaks and API call adjustments (#35)
- Filter Row order to get rid of null values (#37)

8.x-1.6
--------------------------------------------------------------------------------
_Release Date: 2020-03-17_

- Fix tab indexes when the media browser is open so users can enter alt text on the modal
- Fixes behat testing issue where input fields are not interactive when modal is open.

8.x-1.5
--------------------------------------------------------------------------------
_Release Date: 2020-03-17_

- Fixed incorrectly named method in unit test.

8.x-1.4
--------------------------------------------------------------------------------
_Release Date: 2020-03-05_

- D8CORE-1680 Esc key on dialogs to exit
- D8CORE-1421 Refactored ckeditor widget and simplified
- D8CORE-1498 D8CORE-1502 Improve multiple per row
- D8CORE-1421: Moved webpack compiler to the root level of the module and added compiling of the scss for the drupal areas.
- D8CORE-1421: Adjusted styles on the frontend from flex: 1 0 0; to widths with gutters.
- D8CORE-1421: Removed resizeable components until we get that in scope
- D8CORE-1421: converted a few classes to functional components
- D8CORE-1421: removed react-modal in favor of material-ui dialog since its already a dependency.
- D8CORE-1421: added a "minWidth" configurable for each paragraph item
- D8CORE-1421: Rows are disabled from dropping if the rows are full of items
- D8CORE-1421: Rows are disabled if the items within the row require the full 12 columns.
- D8CORE-1421: Added a "Delete" button the paragraph under the "edit" button. this uses drupal's dropbutton styles and js.
- D8CORE-1421: Limited admin titles to 40 characters and improved display of the admin title with ellipsis.
- D8CORE-1421: Move form api fetch to the widget manager and store results so that it "caches" the form data. Preventing multiple api hits by open/closing/opening an item.
- D8CORE-1421: Fixed error when a paragraph doesn't exist and cron is trying to index the node.

8.x-1.3
--------------------------------------------------------------------------------
_Release Date: 2020-03-05_

- Refactored and simplified the ckeditor widget.
- Added some github actions.

8.x-1.2
--------------------------------------------------------------------------------
_Release Date: 2020-02-27_

- Added viewfield widget (#15)
- D8CORE-1400: MS IE flexbox support. (#18)
- D8CORE-1443 Fixed links with arbitrary text (#16)
- When the modal is trying to close, submit the modal form instead to check for required fields (#19)

8.x-1.1
--------------------------------------------------------------------------------
_Release Date: 2020-02-21_

- D8CORE-1320 Spacing and styling adjustments to the widgets (#12)
- D8CORE-1292 D8CORE-1293 Fix ckeditor when using source and other formats

8.x-1.0
--------------------------------------------------------------------------------
_Release Date: 2020-02-14_

- Happy Valentines Day!
- Initial Release.
