<?php
declare(strict_types=1);

namespace Webjump\Halloween\Model;

use Magento\Framework\Exception\InputException;

class MessageValidator
{
    public function validate(?string $value): ?string
    {
        $message = $value ?? '';
        if (mb_strlen($message, 'UTF-8') > 200) {
            throw new InputException(__('The haunted message must contain at most 200 characters.'));
        }

        $message = trim($message);
        return $message === '' ? null : $message;
    }
}
