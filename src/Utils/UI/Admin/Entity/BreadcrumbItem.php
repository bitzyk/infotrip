<?php

namespace Infotrip\Utils\UI\Admin\Entity;

class BreadcrumbItem
{
    /**
     * @var string
     */
    private $label = '';

    /**
     * @var string
     */
    private $link = '';

    /**
     * @var BreadcrumbItem|null
     */
    private $next;

    /**
     * @var BreadcrumbItem|null
     */
    private $previous;

    /**
     * @var
     */
    private $cssIcon = '';

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $label
     * @return BreadcrumbItem
     */
    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }

    /**
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @param string $link
     * @return BreadcrumbItem
     */
    public function setLink($link)
    {
        $this->link = $link;
        return $this;
    }

    /**
     * @return BreadcrumbItem
     */
    public function getNext()
    {
        return $this->next;
    }

    /**
     * @param BreadcrumbItem $next
     * @return BreadcrumbItem
     */
    public function setNext(BreadcrumbItem $next)
    {
        $this->next = $next;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCssIcon()
    {
        return $this->cssIcon;
    }

    /**
     * @param mixed $cssIcon
     * @return BreadcrumbItem
     */
    public function setCssIcon($cssIcon)
    {
        $this->cssIcon = $cssIcon;
        return $this;
    }

    /**
     * @param BreadcrumbItem $previous
     * @return BreadcrumbItem
     */
    public function setPrevious(BreadcrumbItem $previous)
    {
        $this->previous = $previous;
        return $this;
    }

    public function getOutput()
    {
        $output = ' <li';
        if ($this->previous) {
            $output .= ' class="active-bre"';
        }
        $output .= '>';
        if ($this->cssIcon) {
            $output .= '<i class="fa '.$this->cssIcon.'" aria-hidden="true" style="padding-right: 2px;"></i>';
        }
        if ($this->link) {
            $output .= '<a href="'.$this->link.'">';
        }
        $output .= ' ' . $this->getLabel();

        if ($this->link) {
            $output .= '</a>';
        }
        $output .= ' </li>';

        if($this->next) {
            $output .= $this->next->getOutput();
        }

        return $output;
    }

}