# React Paragraphs

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
