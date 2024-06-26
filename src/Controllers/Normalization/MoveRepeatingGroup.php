<?php

declare(strict_types=1);

namespace PhpMyAdmin\Controllers\Normalization;

use PhpMyAdmin\Controllers\AbstractController;
use PhpMyAdmin\Current;
use PhpMyAdmin\Http\Response;
use PhpMyAdmin\Http\ServerRequest;
use PhpMyAdmin\Normalization;
use PhpMyAdmin\ResponseRenderer;
use PhpMyAdmin\Template;

final class MoveRepeatingGroup extends AbstractController
{
    public function __construct(ResponseRenderer $response, Template $template, private Normalization $normalization)
    {
        parent::__construct($response, $template);
    }

    public function __invoke(ServerRequest $request): Response|null
    {
        $repeatingColumns = $request->getParsedBodyParam('repeatingColumns');
        $newTable = $request->getParsedBodyParam('newTable');
        $newColumn = $request->getParsedBodyParam('newColumn');
        $primaryColumns = $request->getParsedBodyParam('primary_columns');
        $res = $this->normalization->moveRepeatingGroup(
            $repeatingColumns,
            $primaryColumns,
            $newTable,
            $newColumn,
            Current::$table,
            Current::$database,
        );
        $this->response->addJSON($res);

        return null;
    }
}
