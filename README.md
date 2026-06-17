# Protocols Manager

GLPI Plugin for making PDF reports with user inventory.

*Compatibile with GLPI 11*

## Features

- Generate PDF protocols with all or selected assets assigned to a user
- You can create multiple configurable templates for different use cases
- Template options:
  - Font, font size, table header color
  - Page orientation (Portrait / Landscape)
  - Logo with configurable height and alignment
  - Word breaking (fixed vs. dynamic column widths)
  - City and date in header
  - Upper content, main content and footer text fields
  - Placeholders {owner}, {admin}, {cur_date} that you can use in text fields in templates
  - Manufacturer + Model in one or two separate columns
  - Serial number and inventory number in one or two columns
  - Optional Status column
- You can add custom rows (for assets not in GLPI)
- Generated PDFs saved automatically to GLPI Documents and linked to the user
- Optional manual or automatic email sending with generated document

## Version 2.0

Plugin reworked for GLPI 11 and PHP 8.1+.
- Fixed all depreceated code, added new functions and methods
- Used more native GLPI functions and elements (both in backend and UI)
- Refreshed UI with more logical and intuitive layout
- Used newest version of dompdf library
- Added new features:
  - Default template
  - Template preview
  - Tooltips
  - Multiple logo options
  - Optional status column
  - More options in template config

If you have an idea, bug or problem - please create an issue.

## Compatibility

- GLPI 11+
- PHP 8.1+ 

## Installation

1. Download and extract package
2. Copy protocolsmanager folder to GLPI plugins directory
3. Go to GLPI Plugin Menu and click 'install' and then 'activate'

![Setup](https://raw.githubusercontent.com/mateusznitka/protocolsmanager/master/docs/img/setup-new.gif)

## Updating

1. Extract package and copy to plguins directory (replace old protocolsmanager folder)
2. Go to GLPI Plugin Menu, you should see 'to update' status.
3. Click on 'install' and then 'activate'

## Configuration

1. Go to Profiles and click on profile you want to add permissions to plugin
2. Select permissions and save
3. Go to Setup -> Protocols manager
4. Edit default or create new template: Fill all or some textboxes, choose your font, logo and other options
5. You can set default template

![Preparing](https://raw.githubusercontent.com/mateusznitka/protocolsmanager/master/docs/img/config-new.gif)

## Using the plugin

1. Go to Administration -> Users and click on user login
2. Go to Protocols Manager tab
3. Select some or all items
4. Write a comment to an item (optional)
5. Add and fill custom rows (optional)
6. Write a note to export (optional)
7. Select your template from list and click "Create"
8. Your protocol is on list above now, you can open it in new tab. It is available in Managament -> Documents too.
9. You can delete all or some protocols by selecting them and click "Delete".

![Generate](https://raw.githubusercontent.com/mateusznitka/protocolsmanager/master/docs/img/generate-new.gif)

## Notes

1. Generated items depends on what you assign to the user in GLPI
2. You can edit template core in HTML by editing template.php file in protocolsmanager/inc directory
3. You cannot update plugin from 1.4 or forked versions of Protocols Manager. You have to make fresh install.

## Current status

Plugin is under active maintenance. 

To do: 
- Update Wiki, GIFs on Github
- Put it in GLPI Marketplace

## Contact 

[mtnt.pl](https://mtnt.pl/#contact)

## Buy me a coffee :)
If you like my work, you can support me by a donate here:

<a href="https://www.buymeacoffee.com/mateusznitka" target="_blank"><img src="https://cdn.buymeacoffee.com/buttons/default-yellow.png" alt="Buy Me A Coffee" height="51px" width="210px"></a>

