<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Core\Entity;

use Cops\Core\AbstractCollection;

/**
 * User book collection
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class UserBookCollection extends AbstractCollection
{
    /**
     * Has one by action
     *
     * @param string $action
     *
     * @return bool
     */
    public function hasOneByAction($action)
    {
        foreach ($this as $element) {
            if ($element->getAction() == $action) {
                return true;
            }
        }

        return false;
    }


    /**
     * Find from user id
     *
     * @param int $userId
     *
     * @return $this
     */
    public function findFromUserId($userId)
    {
        return $this->setDataFromArray(
            $this->getRepository()->findFromUserId($userId)
        );
    }

    /**
     * Find from book id and user id
     *
     * @param int $bookId
     * @param int $userId
     *
     * @return $this
     */
    public function findFromBookIdAndUserId($bookId, $userId)
    {
        return $this->setDataFromArray(
            $this->getRepository()->findFromBookIdAndUserId($bookId, $userId)
        );
    }
}
