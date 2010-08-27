<?php

namespace Blog\Controller;

class AdminController extends \ZendX\Application53\Controller\Action
{
    public function init()
    {
        $auth = \Zend_Auth::getInstance();
        if (!$auth->hasIdentity() || !$auth->getIdentity()->isAllowed('blog:admin')) {
            throw new \Exception('Not allowed.');
        }
    }

    public function addAction()
    {
        $page = new \Core\Model\Page('2col');

        $form = new \Blog\Form\Article();
        $form->removeElement('slug');
        $form->setAction('/blog/admin/add/');
        $form->setView(new \Zend_View());

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            if ($form->isValid($data)) {
                $article = \Blog\Service\Article::createArticle($data);
                \Zend_Registry::get('em')->persist($article);
                \Zend_Registry::get('em')->flush();
                header('Location: /');
            }
        }

        $block = new \Core\Block\Standard(new \Core\Model\View('Blog'), 'admin/add.phtml');
        $block->setContent($form);
        $page->addBlock($block);
        
        echo $page->render();
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');
        $article = \Zend_Registry::get('em')->getRepository('Blog\Model\Article')->find($id);
        if (null === $article) {
            throw new \Exception('Article invalid.');
        }
        
        $page = new \Core\Model\Page('2col');

        $form = new \Blog\Form\Article();
        $form->setView(new \Zend_View());

        $populate = array(
            'id' => $article->getId(),
            'slug' => $article->getSlug(),
            'title' => $article->getTitle(),
            'description' => $article->getDescription(),
            'content' => $article->getContent(),
            'date' => $article->getDate()->format('Y-m-d H:i:s'),
            'published' => $article->getPublished()
        );
        $form->populate($populate);
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            if ($form->isValid($data)) {
                \Blog\Service\Article::updateArticle($article, $data);
                \Zend_Registry::get('em')->flush();
                header('Location: /view/' . $article->getSlug());
            }
        }

        $block = new \Core\Block\Standard(new \Core\Model\View('Blog'), 'admin/add.phtml');
        $block->setContent($form);
        $page->addBlock($block);

        echo $page->render();
    }
}