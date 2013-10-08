<?php
/**
 * Enlight
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://enlight.de/license
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@shopware.de so we can send you a copy immediately.
 *
 * @category   Enlight
 * @package    Enlight_View
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 * @version    $Id$
 * @author     Heiner Lohaus
 * @author     $Author$
 */

/**
 * The Enlight_View_Default class is the responsible representation of the interface between the template manager of smarty
 * and the internal smarty template.
 *
 * The Enlight_View_Default represents the interface between the template manager of smarty and
 * the internal smarty template. This class is responsible for passing the variables to the loaded template,
 * switch the caching or extends the template blocks.
 * Instead to access the template class directly, this manager will be used so the template can be modified afterwards.
 *
 * @category   Enlight
 * @package    Enlight_View
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 */
class Enlight_View_Twig implements Enlight_View_EngineInterface
{

    protected $assignments = array();

    /**
     * The template manager instance.
     *
     * @var Twig_Environment
     */
    protected $engine;

    /**
     * The loaded template instance.
     *
     * @var     Enlight_Template_Default
     */
    protected $template;

    /**
     * Default nocache flag.
     *
     * @var     bool
     */
    protected $nocache;

    /**
     * Default assign scope
     *
     * @var     int
     */
    protected $scope;

    /**
     * The Enlight_View_Default constructor expects an instance of the Twig_Environment and set it
     * into the internal property.
     *
     * @param   Twig_Environment $engine
     */
    public function __construct(Twig_Environment $engine)
    {
        $this->engine = $engine;
        $this->assignments = array();
    }

    /**
     * Returns the instance of the Twig_Environment which has been set in the class constructor.
     * @return  Twig_Environment
     */
    public function Engine()
    {
        return $this->engine;
    }

    /**
     * Returns the instance of the Enlight_Template_Default which will be set by the setTemplate or loadTemplate
     * function.
     *
     * @return  Enlight_Template_Twig
     */
    public function Template()
    {
        if ($this->template === null) {
            throw new Enlight_Exception('Template was not loaded failure');
        }
        return $this->template;
    }

    /**
     * This function sets the default template directory into the internal instance of the Twig_Environment
     *
     * @param   string|array $path
     * @return  Enlight_View_Default
     */
    public function setTemplateDir($path)
    {
        $path = (array) $path;
        foreach($path as $templatePath) {
            $this->engine->getLoader()->addPath($templatePath);
        }
        return $this;
    }

    /**
     * This function adds a template directory into the internal instance of the Twig_Environment
     *
     * @param   $templateDir
     * @param   null $key
     * @return  Enlight_View_Default
     */
    public function addTemplateDir($templateDir, $key = null)
    {
        $this->engine->getLoader()->addPath($templateDir);
        return $this;
    }

    /**
     * Sets the current template instance into the internal property.
     *
     * @param   Enlight_Template_Twig $template
     * @return  Enlight_View_Default
     */
    public function setTemplate($template = null)
    {
        //todo@dr: Add Template Interface for type hinting
        $this->template = $template;
        return $this;
    }

    /**
     * Checks if a template is stored.
     *
     * @return  bool
     */
    public function hasTemplate()
    {
        return isset($this->template);
    }

    /**
     * Loads a template by name over the Twig_Environment.
     *
     * @param   string $template_name
     * @return  Enlight_View_Default
     */
    public function loadTemplate($template_name)
    {
        return $this->createTemplate($template_name);
    }

    /**
     * Creates a new template by name over the Twig_Environment.
     *
     * @param   $template_name
     * @return  Enlight_Template_Twig
     */
    public function createTemplate($template_name)
    {
        $this->template = new Enlight_Template_Twig($this, $template_name);
        return $this->template;
    }

    /**
     * This function extends the internal array with the given template.
     * @param   $template_name
     * @return  Enlight_View_Default
     */
    public function extendsTemplate($template_name)
    {
        if ($this->template !== null) {
            $this->template->extendsTemplate($template_name);
        }
        return $this;
    }

    /**
     * Extends a template block by name.
     *
     * @param          $spec
     * @param          $content
     * @param   string $mode
     * @return  Enlight_View_Default
     */
    public function extendsBlock($spec, $content, $mode)
    {
        if ($this->template !== null) {
            $this->Template()->extendsBlock($spec, $content, $mode);
        }
        return $this;
    }

    /**
     * Checks if the Twig_Environment stored the given template.
     * @param   $template_name
     * @return  bool
     */
    public function templateExists($template_name)
    {
        return $this->engine->templateExists($template_name);
    }

    /**
     * Assigns a specified value to the template.
     * If no cache or scope given, the default settings for this property will be used.
     *
     * @param   string $spec
     * @param   mixed  $value
     * @param   bool   $nocache
     * @param   int    $scope
     * @return \Enlight_View|\Enlight_View_Default
     */
    public function assign($spec, $value = null, $nocache = null, $scope = null)
    {
        if ($this->template !== null) {
            $this->template->assign($spec, $value);
        } else {
            $this->assignments[$spec] = $value;
        }

        return $this;
    }

    /**
     * Resets a specified value or all values.
     *
     * @param   string $spec
     * @param   int $scope
     * @return  Enlight_View_Default
     */
    public function clearAssign($spec = null, $scope = null)
    {
        if ($this->scope !== null && $scope === null) {
            $scope = $this->scope;
        }
        return $this->Template()->clearAssign($spec, $scope);
    }

    /**
     * Returns a specified value or all values.
     *
     * @param   string|null $spec
     * @return  mixed|array
     */
    public function getAssign($spec = null)
    {
        return $this->assignments;
    }

    /**
     * Renders the current template.
     *
     * @return  string
     */
    public function render()
    {
        return $this->Template()->fetch();
    }

    /**
     * Fetch an template by name over the Twig_Environment.
     *
     * @param   $template_name
     * @return  string
     */
    public function fetch($template_name)
    {
        return $this->engine->fetch($template_name, $this->template);
    }

    /**
     * Setter method for the nocache property. Used as default if the parameter is not given in the assign method.
     *
     * @param   bool $value
     * @return  Enlight_View_Default
     */
    public function setNocache($value = true)
    {
        $this->nocache = (bool)$value;
        return $this;
    }

    /**
     * Setter method for the scope property. Used as default if the parameter is not given in the assign method.
     * @param   int|null $scope
     * @return  Enlight_View_Default
     */
    public function setScope($scope = null)
    {
        $this->scope = $scope;
        return $this;
    }

    /**
     * Enable or disable the caching within the template.
     *
     * @param   bool $value
     * @return  Enlight_View_Default
     */
    public function setCaching($value = true)
    {
        $this->Template()->caching = (bool)$value;
        return $this;
    }

    /**
     * Checks if the template is already cached.
     * @return  bool
     */
    public function isCached()
    {
        return false;
        //todo@hl Fix is cached function
        //return $this->template !== null ? $this->template->isCached() : false;
    }

    /**
     * Sets the cache id into the internal template object.
     *
     * @param   string|array $cache_id
     * @return  Enlight_View_Default
     */
    public function setCacheId($cache_id = null)
    {
        $this->Template()->setCacheId($cache_id);
        return $this;
    }

    /**
     * Adds a cache id into the internal template object.
     *
     * @param   string|array $cache_id
     * @return  Enlight_View_Default
     */
    public function addCacheId($cache_id)
    {
        $this->Template()->addCacheId($cache_id);
        return $this;
    }

    /**
     * Returns the cache id of the internal template object.
     * @return  string
     */
    public function getCacheId()
    {
        return $this->Template()->cache_id;
    }
}