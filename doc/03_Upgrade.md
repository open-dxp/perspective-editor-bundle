# Update Notes

## 1.1.0
* [CHORE] Replace Codeception with Pest and `open-dxp/test-foundation`
* [CHORE] Require `open-dxp/opendxp` ^1.5

## Migrating from `pimcore/perspective-editor:^1.8` to `open-dxp/perspective-editor-bundle`
* Changed PHP namespace to `OpenDxp\Bundle\PerspectiveEditorBundle`
* Changed Bundle name to `OpenDxpPerspectiveEditorBundle`
* Changed top-level config node to `opendxp_perspective_editor`
* Replaced class-names, translations, labels, etc.:
  * `pimcore` => `opendxp` (yaml identifiers, config keys, translations)
  * `Pimcore` => `OpenDxp` (PHP identifiers, namespaces, classes)
  * `PIMCORE` => `OPENDXP` (PHP constants)
  * `pimcore`(company/vendor) => `open-dxp` (composer package, github/packagist references)
