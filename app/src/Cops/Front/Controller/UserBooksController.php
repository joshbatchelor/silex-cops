<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Front\Controller;

use Silex\ControllerProviderInterface;
use Cops\Core\Application;

/**
 * User Books controller class
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class UserBooksController implements ControllerProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function connect(\Silex\Application $app)
    {
        $controller = $app['controllers_factory'];

        $controller->post('/action/{id}', __CLASS__.'::bookAction')
            ->assert('id', '\d+')
            ->bind('user_books_action');

        return $controller;
    }

    /**
     * Book action
     *
     * @param Application $app
     * @param int         $id     Book ID
     * @param string      $action Action to make
     *
     * @return string
     */
    public function bookAction(Application $app, $id)
    {
        /**
         * @var \Cops\Core\Entity\UserBook
         */
        $userBook = $app['entity.user-book'];

        $action = $app['request']->get('action');

        $user = $app['security']->getToken()->getUser();

        $userBook
            ->setUserId($user->getId())
            ->setBookId($id)
            ->setAction($action);

        if ($app['request']->get('status', 'false') == 'false') {
            $return = $userBook->delete();
        } else {
            $return = $userBook->save();
        }

        return $return;
    }
}
