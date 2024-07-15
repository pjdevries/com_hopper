# Hopper Joomla! Extension

## Raison d'être

The idea for this extension came from Herman Peeren and is a spin-off of his Joomla! Magazine article series **Tools to
Make a Joomla Component**. The series focuses on customising Joomla!, comparing various approaches, from using custom
fields and CCKs to developing a custom component.

Custom fields are fields that can be added to Joomla! core content types (articles, categories, users, contacts) to
extend or enhance their usability. While custom components can easily be installed on multiple Joomla! websites and most
CCKs allow project exports for import into other Joomla! websites, custom fields are not easily transferable. Hopper
aims to make that possible.

## Minimal Requirements

- Joomla! 5.1.x
- PHP 8.1

The extension is developed and (not very well) tested on Joomla! 5.1.x. It will most likely also work on Joomla! 4.x,
but it hasn't been tried there.

## Installation and configuration ##

Installation is the same as for any other Joomla extension. In its current form, it requires no configuration.

Where this extension differs from most others is its ability to export packages that can then be installed on other
sites. Upon first use, only the component is installed. After creating a project and its release, an installation
release package for the project can be downloaded for installation on other sites.

### Release package

A release is downloaded as a package of type [`package`](https://docs.joomla.org/Package). Different from other package
types, like `component`, `module`, `plugin`, etc., this type of package is a combination package, usually containing
multiple individual extension packages. In the case of Hopper, it contains both the Hopper component package and a
package of type `files`. This second package contains import files with the metadata of custom fields and metadata from
related entities, such as field groups and field-related categories.

After the release package has been installed, an installation script contained within it imports the data contained in
the files package using functionality provided by the component package.

When the installation of the release package is complete, the Hopper component is available for regular use, just as it
was on the site from which it was exported.

### Uninstallation

Uninstallation is the same as for any other Joomla extension.

On sites where only the component was installed, it can be uninstalled as usual. On sites where the combination package
is installed, uninstalling the combination package will also uninstall the separate component and files packages. All
packages can also be uninstalled individually. However, if any of the individual packages contained in the release
package is uninstalled, subsequently uninstalling the release package will result in an error message that can be
ignored.

When the files package is uninstalled, it has no consequences for the fields and associated entities that were imported
during the package's installation. They will still be available after the uninstallation.

## Description

The extension is a component designed to export custom fields and associated metadata, and import them into other
Joomla! sites.

**BEWARE!**: *The component currently does not take any existing data on the target website into account. Therefore, it
is ill-advised to import data into websites that are not empty. Only import data into new, empty, and freshly installed
Joomla! websites.*

In its current form, the component's functionality is quite limited and rudimentary, and it has not been extensively
tested.

> “From then on, when anything went wrong with a computer, we said it had bugs in it.”
_(Grace Hopper)_

Notwithstanding the above, an effort has been made to make the component as robust as possible. However, Joomla itself
does not allow us to make it as fail-safe as we would like. As a result, if an error occurs during the import phase, the
imported data will most likely not be consistent. This can not be remedied by performing the import a second time.
First, all dat that was already imported must be removed. While doing so, don't forget to empty the **Trash**.

## Usage

- Install the Hopper component.
- Create at least one project and one release for that project. The alias name of the poject will be used as a unique
  identifier for generation of installable packages.
- Download the project package.
- Install the project package into new, empty, and freshly installed Joomla! website.