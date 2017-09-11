# latte-view

[Latte templating engine](https://github.com/nette/latte) wrapper for [Slim microframework](https://www.slimframework.com/).

You can use this small library to integrate Latte templates into a project based on Slim framework.

This project was created for [course APV](http://odinuv.cz/en/apv/course/) on Mendel University in Brno.

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
    return $this->view->render($response, 'index.latte');
})->setName('index');
```

To use Slim's build in [router](https://www.slimframework.com/docs/objects/router.html) in a Latte macro like this
`{link routeName}` add following lines:

```php
$latteView->addParam('router', $container->router);
$latteView->addMacro('link', function (MacroNode $node, PhpWriter $writer) use ($container) {
    if (strpos($node->args, ' ') !== false) {
        return $writer->write("echo \$router->pathFor(%node.word, %node.args);");
    } else {
        return $writer->write("echo \$router->pathFor(%node.word);");
    }
});
```

Remember to set names to your routes: 

```php
$app->get('/test', function (Request $request, Response $response, $args) {
})->setName('routeName');
```