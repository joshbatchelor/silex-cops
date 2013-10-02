<?php
/*
 * This file is part of Silex Cops. Licensed under WTFPL
 *
 * (c) Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Cops\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Cops\Model\BookFile\BookFileFactory;

/**
 * Book controller class
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 */
class BookController
    extends \Cops\Model\Controller
    implements \Silex\ControllerProviderInterface
{
    /**
     * Connect method to dynamically add routes
     *
     * @see \Silex\ControllerProviderInterface::connect()
     *
     * @param \Application $app Application instance
     *
     * @return ControllerCollection ControllerCollection instance
     */
    public function connect(\Silex\Application $app)
    {
        $controller = $app['controllers_factory'];
        $controller->get('/{id}', __CLASS__.'::detailAction')
            ->assert('id' ,'\d+')
            ->bind('book_detail');

        $controller->get('/list/{page}', __CLASS__.'::listAction')
            ->assert('page', '\d+')
            ->value('page', 1)
            ->bind('book_list');

        $controller->get('/download/{id}/{format}', __CLASS__.'::downloadAction')
            ->assert('id', '\d+')
            ->bind('book_download');

        return $controller;
    }

    /**
     * Show details of a book
     *
     * @param \Silex\Application $app Silex app instance
     * @param int                $id  BookId
     *
     * @return string
     */
    public function detailAction(\Silex\Application $app, $id)
    {
        $book = $this->getModel('Book')->load($id);

        return $app['twig']->render($app['config']->getTemplatePrefix().'book.html', array(
            'pageTitle' => $book->getTitle(),
            'book' => $book
        ));

    }

    /**
     * Download book file
     *
     * @param int    $id     The book ID
     * @param string $format The book file format
     *
     * @return void
     */
    public function downloadAction($id, $format=BookFileFactory::TYPE_EPUB)
    {
        $book = $this->getModel('Book')->load($id);

        $bookFile = $book->getFile(strtoupper($format));

        if ($file = $bookFile->getFilePath()) {
            header('Content-type: '.$bookFile->getContentTypeHeader());
            header('Content-disposition:attachment;filename="'.$bookFile->getFileName().'"');
            header('Content-Transfer-Encoding: binary');
            readfile($file);
        }
        exit;
    }

    public function listAction($page)
    {
        return __FUNCTION__.$page;
    }
}