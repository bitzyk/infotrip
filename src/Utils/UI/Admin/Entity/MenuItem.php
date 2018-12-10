<?php
/**
 * Created by PhpStorm.
 * User: cbitoi
 * Date: 07/12/2018
 * Time: 14:49
 */

namespace Infotrip\Utils\UI\Admin\Entity;


class MenuItem
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
     * @var bool
     */
    private $hasSubmenu = false;

    /**
     * @var MenuItem[]
     */
    private $submenu = array();

    /**
     * @var string
     */
    private $cssClass = '';

    /**
     * @var string
     */
    private $currentRouteName = '';

    /**
     * @var string
     */
    private $routeName;

    /**
     * @var bool
     */
    private $visible = true;

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $label
     * @return MenuItem
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
     * @return MenuItem
     */
    public function setLink($link)
    {
        $this->link = $link;
        return $this;
    }

    /**
     * @return bool
     */
    public function isHasSubmenu()
    {
        return $this->hasSubmenu;
    }

    /**
     * @param bool $hasSubmenu
     * @return MenuItem
     */
    public function setHasSubmenu($hasSubmenu)
    {
        $this->hasSubmenu = $hasSubmenu;
        return $this;
    }

    /**
     * @return MenuItem[]
     */
    public function getSubmenu()
    {
        return $this->submenu;
    }

    /**
     * @param MenuItem[] $submenu
     * @return MenuItem
     */
    public function setSubmenu(array $submenu)
    {
        $this->submenu = $submenu;
        return $this;
    }

    /**
     * @return string
     */
    public function getCssClass()
    {
        return $this->cssClass;
    }

    /**
     * @param string $cssClass
     * @return MenuItem
     */
    public function setCssClass($cssClass)
    {
        $this->cssClass = $cssClass;
        return $this;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        if (count($this->submenu) === 0) {
            return ($this->routeName === $this->currentRouteName);
        } else {
            foreach ($this->submenu as $submenu) {
                if ($submenu->isActive()) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param string $currentRouteName
     * @return MenuItem
     */
    public function setCurrentRouteName($currentRouteName)
    {
        $this->currentRouteName = $currentRouteName;
        return $this;
    }

    /**
     * @param string $routeName
     * @return MenuItem
     */
    public function setRouteName($routeName)
    {
        $this->routeName = $routeName;
        return $this;
    }

    /**
     * @return bool
     */
    public function isVisible()
    {
        return $this->visible;
    }

    /**
     * @param bool $visible
     * @return MenuItem
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;
        return $this;
    }

}