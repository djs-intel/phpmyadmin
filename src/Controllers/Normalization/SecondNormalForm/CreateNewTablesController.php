<?php

declare(strict_types=1);

namespace PhpMyAdmin\Controllers\Normalization\SecondNormalForm;

use PhpMyAdmin\Controllers\AbstractController;
use PhpMyAdmin\Current;
use PhpMyAdmin\Http\Response;
use PhpMyAdmin\Http\ServerRequest;
use PhpMyAdmin\Normalization;
use PhpMyAdmin\ResponseRenderer;
use PhpMyAdmin\Template;

use function json_decode;

final class CreateNewTablesController extends AbstractController
{
    public function __construct(ResponseRenderer $response, Template $template, private Normalization $normalization)
    {
        parent::__construct($response, $template);
    }

    public function __invoke(ServerRequest $request): Response|null
    {
        $partialDependencies = json_decode($request->getParsedBodyParam('pd'), true);
        $tablesName = json_decode($request->getParsedBodyParam('newTablesName'));
        $res = $this->normalization->createNewTablesFor2NF(
            $partialDependencies,
            $tablesName,
            Current::$table,
            Current::$database,
        );
        $this->response->addJSON($res);

        return null;
    }
}
