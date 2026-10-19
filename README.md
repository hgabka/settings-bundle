# hgabka/settings-bundle

Adatbázisban tárolt, az adminból szerkeszthető alkalmazás-beállítások (kulcs → érték), típusos értékekkel, nyelvfüggő változatokkal és kategóriákkal; fájl-cache-ből gyorsan olvashatók. Mellette egy szerkeszthető URL-átirányítás (redirect) kezelő is van.

## Telepítés

```bash
composer require hgabka/settings-bundle
```

```php
// config/bundles.php
Hgabka\SettingsBundle\HgabkaSettingsBundle::class => ['all' => true],
```

Függőségek: `hgabka/utils-bundle`, `a2lix/translation-form-bundle`, `stof/doctrine-extensions-bundle`, Sonata Admin. Táblák: `hg_settings_settings`, `hg_settings_setting_category` (+ fordítás táblák), `hg_settings_redirect`.

## Konfiguráció

```yaml
# config/packages/hgabka_settings.yaml
hgabka_settings:
    editor_role: ROLE_SETTING_ADMIN    # beállítások értékének szerkesztése
    creator_role: ROLE_SUPER_ADMIN     # új beállítás létrehozása / szerkezetének módosítása
```

## Használat

### Beállítás felvétele

A Sonata adminban (`SettingAdmin`, `creator_role` jogosultsággal) létrehozható egy `Setting`:

| Mező | Jelentés |
|---|---|
| `name` | a lekérdezéshez használt kulcs (snake_case, pl. `contact_email`) |
| `type` | az érték típusa (lásd lent) |
| `category` | opcionális csoportosítás (`SettingCategory`, fordítható névvel, sorrendezhető) |
| `culture_aware` | nyelvenként külön érték (különben egy közös `general_value`) |
| `required`, `editable`, `visible` | kötelező / szerkeszthető / látható-e az adminban |

### Olvasás PHP-ból

```php
use Hgabka\SettingsBundle\Helper\SettingsManager;

$email = $settings->get('contact_email');                    // aktuális nyelv
$email = $settings->get('contact_email', 'en', 'n/a');       // nyelv + alapérték
$email = $settings->getContactEmail();                       // __call: getXxx() -> xxx_yyy (tableize)
$all   = $settings->getValues('hu');                         // az összes beállítás egy tömbben
$text  = $settings->replaceSettings('Hívj: [phone]', '[', ']'); // [név] helyőrzők cseréje
```

Az értékek `FilesystemAdapter` cache-ben (`<cache_dir>/settings`) vannak; mentéskor/törléskor a `SettingListener` frissíti őket (`addSettingToCache`, `removeFromCache`, `regenerateCache`, `clearCache`).

### Olvasás Twigből

```twig
{{ get_setting('contact_email') }}
{{ get_setting('slogan', 'en') }}
{{ 'Üdv a(z) [site_name] oldalon'|replace_settings('[', ']') }}
```

`is_setting_visible(setting)` az admin felületi sablonokhoz van.

## Beállítás-típusok

| Típus (`getId()`) | Osztály |
|---|---|
| `string` | `StringSettingType` |
| `text` | `TextSettingType` |
| `wysiwyg` | `WysiwygSettingType` |
| `integer` | `IntegerSettingType` |
| `number` | `NumberSettingType` |
| `bool` | `BoolSettingType` |
| `email` | `EmailSettingType` |
| `date`, `datetime`, `daterange`, `datetimerange` | `DateSettingType`, `DateTimeSettingType`, `DateRangeSettingType`, `DatetimeRangeSettingType` |

Saját típus: valósítsd meg a `Model\SettingTypeInterface`-t (`getId`, `getName`, `getFormType`, `getFormTypeOptions`, `transformValue`, `reverseTransformValue`, `getHtml`, `isEditable`, `isVisible`, `getPriority`, `getFormTransformer`) – vagy származtass az `AbstractSettingType`, `AbstractChoiceSettingType`, `AbstractTranslatedEnumSettingType` ősből. Az interfészt megvalósító szolgáltatások autoconfigure-ral megkapják a `hg_setting.setting_type` taget és regisztrálódnak a `SettingsManager`-ben (`alias` attribútummal az azonosító felülírható). Az űrlap bővíthető a `Event\SettingFormTypeEvent`-tel.

## Átirányítások (Redirect)

A `Redirect` entitás (`domain`, `origin`, `target`, `permanent`) az adminból (`RedirectAdmin`) szerkeszthető. A `Router\RedirectRouter` (`router` tag, prioritás: 255, tehát elsőként fut) a beérkező útvonalat a rögzített átirányításokhoz illeszti (jelenleg csak a `domain` nélküli rekordokat veszi figyelembe); a `/*`-ra végződő `origin` wildcardként működik (a `target` mögé másolja a maradék útvonalat). Az átirányítás a Symfony `RedirectController::urlRedirectAction`-jével történik (`permanent` → 301, különben 302).
