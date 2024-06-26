<?php

declare(strict_types=1);

namespace PhpMyAdmin\Controllers\Table;

use PhpMyAdmin\ConfigStorage\RelationCleanup;
use PhpMyAdmin\Controllers\AbstractController;
use PhpMyAdmin\Current;
use PhpMyAdmin\DatabaseInterface;
use PhpMyAdmin\FlashMessages;
use PhpMyAdmin\Http\Response;
use PhpMyAdmin\Http\ServerRequest;
use PhpMyAdmin\Message;
use PhpMyAdmin\ResponseRenderer;
use PhpMyAdmin\Template;
use PhpMyAdmin\Util;

use function __;
use function _ngettext;
use function count;

final class DropColumnController extends AbstractController
{
    public function __construct(
        ResponseRenderer $response,
        Template $template,
        private DatabaseInterface $dbi,
        private FlashMessages $flash,
        private RelationCleanup $relationCleanup,
    ) {
        parent::__construct($response, $template);
    }

    public function __invoke(ServerRequest $request): Response|null
    {
        $selected = $_POST['selected'] ?? [];

        if (empty($selected)) {
            $this->response->setRequestStatus(false);
            $this->response->addJSON('message', __('No column selected.'));

            return null;
        }

        $selectedCount = count($selected);
        if (($_POST['mult_btn'] ?? '') === __('Yes')) {
            $i = 1;
            $statement = 'ALTER TABLE ' . Util::backquote(Current::$table);

            foreach ($selected as $field) {
                $this->relationCleanup->column(Current::$database, Current::$table, $field);
                $statement .= ' DROP ' . Util::backquote($field);
                $statement .= $i++ === $selectedCount ? ';' : ',';
            }

            $this->dbi->selectDb(Current::$database);
            $result = $this->dbi->tryQuery($statement);

            if (! $result) {
                $message = Message::error($this->dbi->getError());
            }
        } else {
            $message = Message::success(__('No change'));
        }

        if (! isset($message)) {
            $message = Message::success(
                _ngettext(
                    '%1$d column has been dropped successfully.',
                    '%1$d columns have been dropped successfully.',
                    $selectedCount,
                ),
            );
            $message->addParam($selectedCount);
        }

        $this->flash->addMessage($message->isError() ? 'danger' : 'success', $message->getMessage());
        $this->redirect('/table/structure', ['db' => Current::$database, 'table' => Current::$table]);

        return null;
    }
}
