<?php

namespace Blog\Model;

/**
 * @Entity(repositoryClass="Blog\Repository\Article")
 * @Table(name="BlogArticle")
 */
class Article extends \Core\Model\AbstractModel
{

    /**
     * @var integer
     * @Id @Column(type="integer", name="article_id")
     * @GeneratedValue
     */
    protected $id;

    /**
     * @var string
     * @Column(type="string", name="slug", length="255", nullable="false", unique="true")
     */
    protected $slug;

    /**
     * @var string
     * @Column(type="string", name="title", length="255", nullable="false")
     */
    protected $title;

    /**
     * @var string
     * @Column(type="string", name="description", length="3000", nullable="false")
     */
    protected $description;

    /**
     * @var string
     * @Column(type="text", name="content", nullable="false")
     */
    protected $content;

    /**
     * @var DateTime
     * @Column(type="datetime", name="date", nullable="false")
     */
    protected $date;

    /**
     * @var boolean
     * @Column(type="boolean", name="published", nullable="false")
     */
    protected $published;

    /**
     * @param string $title
     * @param string $content
     * @param DateTime $date
     * @param boolean $published
     */
    public function __construct($title, $content = '', $date = null, $published = false)
    {
        $this->setTitle($title);
        $this->setContent($content);
        $this->setDescription($content);
        $this->setDate($date);
        $this->setPublished($published);
    }

    /**
     * @param string $title
     * @return Article
     */
    public function setTitle($title)
    {
        $this->title = $title;
        if (!$this->slug) {
            $this->setSlug($this->slug($title));
        }
        return $this;
    }

    /**
     * @param DateTime $date
     * @return Article
     */
    public function setDate(\DateTime $date = null)
    {
        if (null === $date) {
            $date = new \DateTime();
        }
        $this->date = $date;
        return $this;
    }

    /**
     * @param boolean $published
     * @return Article
     */
    public function setPublished($published = true)
    {
        $this->published = $published;
        return $this;
    }

    /**
     * Returns abbreviated content text
     *
     * @return string
     */
    public function getShortContent()
    {
        return $this->description ? $this->description : $this->content;
    }

    /**
     * Returns the url to view the inidividual article
     *
     * @return string
     */
    public function getUrl()
    {
        $d = $this->getDate();
        return '/article/' . $this->getSlug() . '/';
    }

    /**
     * @param string $value
     * @return string
     */
    protected function slug($value)
    {
        $filter = new \Blog\Filter\Slug($this->getRepository(), 'findBySlug');
        return $filter->filter(substr($value, 0, 255));
    }
}