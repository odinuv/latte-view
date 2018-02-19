# latte-view

[Latte templating engine](https://github.com/nette/latte) wrapper for [Slim microframework](https://www.slimframework.com/).

You can use this small library to integrate Latte templates into a project based on Slim framework.

This project was created for [course APV](http://odinuv.cz/course/) on Mendel University in Brno.

## Installation

You can download this library using [Composer](https://getcomposer.org/):

```bash
composer require ujpef/latte-view
```

## Methods

### __construct(Latte\Engine $latte, $pathToTemplates)

Create an instance of Latte wrapper. Pass instance of Latte engine and path to your templates. You can optionally
configure Latte engine before you pass it to the wrapper (eg. set up cache folder for templates).

### addParam($name, $param)

Make template variable called `$name` with `$param` value.

### addParams(array $params)

Pass multiple values into a template. The `$params` array must be associative.

### render(Response $response, $name, array $params = [])

Render a template given by `$name` with set of template variables given by `$params` associative array and create
new Response object.

Returns new instance of Response object which can be returned from route or middleware.

### addFilter($title, callable $callback)

Add a custom Latte filter - `{$variable|customFilter}`.

### addMacro($name, callable $callback)

Add a custom Latte macro - `{customMacro param}`.

## Integration with Slim framework

Define a dependency for Slim framework (change templates source folder and cache directory location if needed):

```php
use Latte\Engine;
use Ujpef\LatteView;

$container['view'] = function ($container) use ($settings) {
    $engine = new Engine();
    $engine->setTempDirectory(__DIR__ . '/../cache');

    $latteView = new LatteView($engine, __DIR__ . '/../templates/');
    return $latteView;
};
```

To return result of Latte template rendering call the `render()` method.

```php
$app->get('/[{name}]', function (Request $request, Response $response, $args) {
	$tplVars = [
    	'variable' => 123
    ];
    return $this->view->render($response, 'index.latte', $tplVars);
})->setName('index');
```

In template use:

```html
<!DOCTYPE html>
<html>
	<head>
    	<title>test template</title>
    </head>
    <body>
		contents of variable: {$variable}
    </body>
</html>
```

## Using named routes

To use Slim's build in [router](https://www.slimframework.com/docs/objects/router.html) in a Latte macro like this
`{link routeName}` add following lines into dependency definition:

```php
use Latte\MacroNode;
use Latte\PhpWriter;

$container['view'] = function ($container) use ($settings) {
	//...
    $latteView->addParam('router', $container->router);
    $latteView->addMacro('link', function (MacroNode $node, PhpWriter $writer) use ($container) {
        if (strpos($node->args, ' ') !== false) {
            return $writer->write("echo \$router->pathFor(%node.word, %node.args);");
        } else {
            return $writer->write("echo \$router->pathFor(%node.word);");
        }
    });
    return $latteView;
};
```

Remember to set names for your routes:

```php
$app->get('/test', function (Request $request, Response $response, $args) {
	//route implementation
})->setName('routeName');
```

This also works with route placeholders:

```php
$app->get('/test/{something}', function (Request $request, Response $response, $args) {
	//route implementation
})->setName('routeName');
```

In template use:

```html
<a href="{link routeName ['something' => 123]}">link</a>
```