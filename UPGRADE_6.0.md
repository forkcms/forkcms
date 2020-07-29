UPGRADE FROM 5.x to 6.0
=======================

## Javascript files updated to ES6

Changes:

- We now use webpack instead of gulp

Fixes:

- weird location of installer js and css
- expliciet double js in frontend and backend

Removed:

- 'shareMenu' function
- 'keyValueBox', 'tagsBox', 'multipleSelectBox' functions
- utils form static functions

## Upgrade backend to Bootstrap 4

Changes:

- Grid system is now build on flexbox
- Cards are the old panels, thumbnails and wells
- Added a lot of utility classes

Everything you need to know about the conversion: https://getbootstrap.com/docs/4.5/migration/#summary
