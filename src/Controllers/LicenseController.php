<?php
/**
 * Simple script to set correct charset for the license
 */

declare(strict_types=1);

namespace PhpMyAdmin\Controllers;

use PhpMyAdmin\Http\Response;
use PhpMyAdmin\Http\ServerRequest;

use function __;
use function is_readable;
use function printf;
use function readfile;

/**
 * Simple script to set correct charset for the license
 */
class LicenseController extends AbstractController
{
    public function __invoke(ServerRequest $request): Response|null
    {
        $this->response->disable();
        $this->response->addHeader('Content-Type', 'text/plain; charset=utf-8');

        $filename = LICENSE_FILE;

        // Check if the file is available, some distributions remove these.
        if (@is_readable($filename)) {
            readfile($filename);

            return null;
        }

        printf(
            __(
                'The %s file is not available on this system, please visit %s for more information.',
            ),
            $filename,
            'https://www.phpmyadmin.net/',
        );

        return null;
    }
}
