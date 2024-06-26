<?php

declare(strict_types=1);

namespace PhpMyAdmin\Controllers\Table\Structure;

use PhpMyAdmin\Controllers\AbstractController;
use PhpMyAdmin\Controllers\Table\StructureController;
use PhpMyAdmin\Database\CentralColumns;
use PhpMyAdmin\Http\Response;
use PhpMyAdmin\Http\ServerRequest;
use PhpMyAdmin\Identifiers\DatabaseName;
use PhpMyAdmin\Message;
use PhpMyAdmin\ResponseRenderer;
use PhpMyAdmin\Template;
use Webmozart\Assert\Assert;

use function __;
use function is_array;

final class CentralColumnsAddController extends AbstractController
{
    public function __construct(
        ResponseRenderer $response,
        Template $template,
        private CentralColumns $centralColumns,
        private StructureController $structureController,
    ) {
        parent::__construct($response, $template);
    }

    public function __invoke(ServerRequest $request): Response|null
    {
        $GLOBALS['message'] ??= null;

        $selected = $request->getParsedBodyParam('selected_fld', []);

        if (! is_array($selected) || $selected === []) {
            $this->response->setRequestStatus(false);
            $this->response->addJSON('message', __('No column selected.'));

            return null;
        }

        Assert::allString($selected);

        $centralColsError = $this->centralColumns->syncUniqueColumns(
            DatabaseName::from($request->getParsedBodyParam('db')),
            $selected,
            false,
            $request->getParsedBodyParam('table'),
        );

        if ($centralColsError instanceof Message) {
            $GLOBALS['message'] = $centralColsError;
        }

        if (empty($GLOBALS['message'])) {
            $GLOBALS['message'] = Message::success();
        }

        ($this->structureController)($request);

        return null;
    }
}
