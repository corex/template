# Simple template system.

**_Versioning for this package follows http://semver.org/. Backwards compatibility might break on upgrade to major versions._**

The goal for this package is simplicity. It is based on Mustache but has a very simple approach.
If you need more than this "simplicity", Mustache is recommended.

Every call to load() and render() will use base paths.

Mustache is default set to escape variables. This has been disabled on this package.
It is however possible to escape by calling escape() on template if you need to escape values i.e. html-tags.

```php
// Set base path (can be called more than once). Paths will be searched in reverse order.
Template::basePath('/path/to/templates');
Template::basePath('/path/to/other/templates');
```

```php
// Load template.
$template = Template::load('test');

// Set escape on variables (default un-escaped).
$template->escape();

// Set path for templates.
$template->path('/path/to/some/other/templates');

// Set variable on template.
$template->variable('myVar', 'myValue');

// Set variables.
$template->variables([
    'myVar1' => 'myValue1',
    'myVar2' => 'myValue2'
]);

// Render template.
$content = $template->render();
```

```php
// Example of loading template, set var and render.
$content = Template::load('base')->variable('myVar', 'myValue')->render();
```

```php
// Render template directly.
$content = Template::render('base', [
    'myVar1' => 'myValue1',
    'myVar2' => 'myValue2'
]);
```

```php
// Parse template and render values.
$content = Template::parse('({{myVar}})', ['myVar' => 'myValue']);
```

```php
// Get Mustache engine.
$mustacheEngine = Template::mustacheEngine();
```

For further reading on Mustache, look at https://github.com/bobthecow/mustache.php/wiki
