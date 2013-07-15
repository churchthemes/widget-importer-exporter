=== Widget Importer & Exporter ===
Contributors: stevengliebe, churchthemes
Tags: widgets, widget, importer, exporter, import, export, backup
Requires at least: 3.5
Tested up to: 3.5.2
Stable tag: 0.7
License: http://www.gnu.org/licenses/old-licenses/gpl-2.0.html

Import and export your widgets.

== Description ==

Widget Importer & Exporter is useful for moving widgets from one WordPress site to another, backing up widgets and for theme developers to provide users with sample widgets.

= Importing =

Importing is a matter of uploading an export file created by the plugin. The results of an import are shown in a nicely formatted table with an explanation of what happened with each sidebar and widget.

Importation takes into consideration sidebars not existing in the current theme (widgets imported as *Inactive*), widgets that already exist in the same sidebar (widgets not duplicated) and widgets that are not supported by the site (widgets not imported).

= Exporting =

Widget Importer & Exporter can create an export file (in JSON format with .wie extension) out of currently active widgets. This file can be imported into other sites using this plugin or used to restore widgets to the same site later.

= Developers =

The ``wie_before_import`` action fires after a file is uploaded but before the data is imported. ``wie_after_import`` fires after the data is imported. Various filters are used throughout (browse the code). Let me know if you need more actions or filters.

Please jump on [GitHub](https://github.com/stevengliebe/widget-importer-exporter) to report issues and follow development.

== Installation ==

Please see [Installing Plugins](http://codex.wordpress.org/Managing_Plugins#Installing_Plugins) in the WordPress Codex.

After activation, go to *Tools > Widget Import/Export*

== Screenshots ==

1. Main import/export screen
2. Results from an import

== Changelog ==

= 0.7 =
* Various fixes, improvements

= 0.6 =
* Import hooks, translation file, code clean up

= 0.5 =
* Finished importing

= 0.4 =
* Import error checking and results table

= 0.3 =
* Uploading finished

= 0.2 =
* Minor clean up

= 0.1 =
* First release - export only