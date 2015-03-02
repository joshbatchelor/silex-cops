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

use Silex\ControllerProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

/**
 * OPDS controller class
 *
 * @author Mathieu Duplouy <mathieu.duplouy@gmail.com>
 *
 * @codeCoverageIgnore
 */
class OpdsController implements ControllerProviderInterface
{
    /**
     * Connect method to dynamically add routes
     *
     * @see \Silex\ControllerProviderInterface::connect()
     *
     * @param  Application $app Application instance
     *
     * @return ControllerCollection ControllerCollection instance
     */
    public function connect(Application $app)
    {
        $controller = $app['controllers_factory'];

        $controller->get('/', __CLASS__.'::indexAction')
            ->bind('opds_home');

        // authors related
        $controller->get('/authors', __CLASS__.'::authorsAction')
            ->bind('opds_authors');
        $controller->get('/authors/{letter}', __CLASS__.'::authorsAlphaAction')
            ->assert('letter', '\w+|0')
            ->bind('opds_authors_alpha');
        $controller->get('/author/{id}', __CLASS__.'::authorDetailAction')
            ->assert('id', '\d+')
            ->bind('opds_author_detail');

        // series related
        $controller->get('/series', __CLASS__.'::seriesAction')
            ->bind('opds_series');
        $controller->get('/series/{letter}', __CLASS__.'::seriesAlphaAction')
            ->assert('letter', '\w+|0')
            ->bind('opds_series_alpha');
        $controller->get('/serie/{id}', __CLASS__.'::serieDetailAction')
            ->assert('id', '\d+')
            ->bind('opds_serie_detail');

        // search related
        $controller->get('/search', __CLASS__.'::searchAction')
            ->bind('opds_search');

        return $controller;
    }

    /**
     * Index action, render the home OPDS feed
     *
     * @param  Application $app Application instance
     *
     * @return string
     */
    public function indexAction(Application $app)
    {
        // Todo : get the last added book date to make the update time in xml feed

        // Create the whole structure from twig template
        $xml =  $app['twig']->render('opds/home.xml.twig', array(
            'updated'     => date('Y-m-d\TH:i:sP'),
            'nbLastAdded' => $app['config']->getValue('last_added')
        ));

        return $this->_checkXml($xml);
    }

    /**
     * Get the alphabetic list of authors
     *
     * @param  Application $app Application instance
     *
     * @return string
     */
    public function authorsAction(Application $app)
    {
        $xml =  $app['twig']->render('opds/authors.xml.twig', array(
            'updated'           => date('Y-m-d\TH:i:sP'),
            'authorsAggregated' => $app['model.author']->getAggregatedList(),
        ));

        return $this->_checkXml($xml);
    }

    /**
     * Get the list of authors beginning by a letter
     *
     * @param Application  $app    Application instance
     * @param string       $letter First letter of author's name
     *
     * @return string
     */
    public function authorsAlphaAction(Application $app, $letter)
    {
        if ($letter === '0') {
            $letter = '#';
        }
        $authors = $app['model.author']->getCollection()->getByFirstLetter($letter);

        $xml =  $app['twig']->render('opds/authors_alpha.xml.twig', array(
            'updated' => date('Y-m-d\TH:i:sP'),
            'authors' => $authors,
        ));

        return $this->_checkXml($xml);
    }

    /**
     * Get the alphabetic list of series
     *
     * @param  Application $app Application instance
     *
     * @return string
     */
    public function seriesAction(Application $app)
    {
        $xml =  $app['twig']->render('opds/series.xml.twig', array(
            'updated'          => date('Y-m-d\TH:i:sP'),
            'seriesAggregated' => $app['model.serie']->getAggregatedList(),
        ));

        return $this->_checkXml($xml);
    }

    /**
     * Get the list of series beginning by a letter
     *
     * @param Application $app    Application instance
     * @param string       $letter First letter of serie's name
     *
     * @return string
     */
    public function seriesAlphaAction(Application $app, $letter)
    {
        if ($letter === '0') {
            $letter = '#';
        }
        $series = $app['model.serie']->getCollection()->getByFirstLetter($letter);

        $xml =  $app['twig']->render('opds/series_alpha.xml.twig', array(
            'updated' => date('Y-m-d\TH:i:sP'),
            'series'  => $series,
        ));

        return $this->_checkXml($xml);
    }

    /**
     * Get a serie detail
     *
     * @param Application $app     Application instance
     * @param int         $id      Serie ID
     */
    public function serieDetailAction(Application $app, $id)
    {
        $serie = $app['model.serie']->load($id);

        $xml = $app['twig']->render('opds/serie_detail.xml.twig', array(
            'updated' => date('Y-m-d\TH:i:sP'),
            'serie'   => $serie,
        ));

        return $this->_checkXml($xml);
    }

    /**
     * Check and return XML string by parsing it with DomDocument
     *
     * @param string $xmlString
     *
     * @return string
     */
    private function _checkXml($xmlString)
    {
        // Load into DOM to ensure the xml is well formated
        $dom = new \DOMDocument;
        $dom->loadXml($xmlString);
        return $dom->saveXml();
    }
}