<?php
/**
 * @link      http://github.com/zendframework/ItcSsaBlog for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ItcSsaBlog\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Zend\Paginator\Paginator;
use ItcSsaBlog\Entity\Post;
use Zend\Mvc\MvcEvent;

/**
 * This is the main controller class of the Blog. The 
 * controller class is used to receive user input,  
 * pass the data to the models and pass the results returned by models to the 
 * view for rendering.
 */
class IndexController extends AbstractActionController 
{
    /**
     * Entity manager.
     * @var Doctrine\ORM\EntityManager 
     */
    private $entityManager;
    
    /**
     * Post manager.
     * @var ItcSsaBlog\Service\PostManager 
     */
    private $postManager;
    
    /**
     * Constructor is used for injecting dependencies into the controller.
     */
    public function __construct($entityManager, $postManager) 
    {
        $this->entityManager = $entityManager;
        $this->postManager = $postManager;
    }
    
    /** 
     * We override the parent class onDispatch() method to
     * set an alternative layout for all actions in this controller.
     */
    public function onDispatch(MvcEvent $e)
    {
      // Call the base class' onDispatch() first and grab the response
      $response = parent::onDispatch($e);

      // Set alternative layout
      $this->layout()->setTemplate('layout/blog');

      // Return the response
      return $response;
    }

    /**
     * This is the default "index" action of the controller. It displays the 
     * Recent Posts page containing the recent blog posts.
     */
    public function indexAction() 
    {
        $page = $this->params()->fromQuery('page', 1);
        $tagFilter = $this->params()->fromQuery('tag', null);
        
        if ($tagFilter) {
         
            // Filter posts by tag
            $query = $this->entityManager->getRepository(Post::class)
                    ->findPostsByTag($tagFilter);
            
        } else {
            // Get recent posts
            $query = $this->entityManager->getRepository(Post::class)
                    ->findPublishedPosts();
        }
        
        $adapter = new DoctrineAdapter(new ORMPaginator($query, false));
        $paginator = new Paginator($adapter);
        $paginator->setDefaultItemCountPerPage(10);        
        $paginator->setCurrentPageNumber($page);
                       
        // Get popular tags.
        $tagCloud = $this->postManager->getTagCloud();
        
        // Render the view template.
        return new ViewModel([
            'posts' => $paginator,
            'postManager' => $this->postManager,
            'tagCloud' => $tagCloud
        ]);
    }
    
}
