<?php
/**
 * @author Jmango360
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

use Symfony\Component\Translation\TranslatorInterface;

abstract class JmCheckoutStep implements JmCheckoutStepInterface
{
    private $smarty;
    private $translator;
    private $title;

    protected $context;
    protected $template;
    protected $checkoutSession;
    protected $errorMessages = null;
    protected $module_url;
    public $module_name;

    public function __construct(
        Context $context,
        TranslatorInterface $translator,
        JmCheckoutSession $checkoutSession,
        $module_name
    ) {
        $this->module_name = $module_name;
        $this->context = $context;
        $this->smarty = $context->smarty;
        $this->translator = $translator;
        $this->checkoutSession = $checkoutSession;
        $this->module_url = $this->context->link->getModuleLink($this->module_name, 'jmcheckout', array());
        $this->context->smarty->assign('myopc_checkout_url', $this->module_url);
    }

    public function setTemplate($templatePath)
    {
        $this->template = $templatePath;
        return $this;
    }

    public function getTemplate()
    {
        return $this->template;
    }

    protected function getTranslator()
    {
        return $this->translator;
    }

    protected function renderTemplate($template, array $extraParams = array(), array $params = array())
    {
        $defaultParams = array(
            'title' => $this->getTitle(),
        );

        $scope = $this->smarty->createData(
            $this->smarty
        );

        $scope->assign(array_merge($defaultParams, $extraParams, $params));

        $tpl = $this->smarty->createTemplate(
            $template,
            $scope
        );

        $html = $tpl->fetch();

        return $html;
    }

    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle()
    {
        return $this->title;
    }


    public function getCheckoutSession()
    {
        return $this->checkoutSession;
    }

    public function setCheckoutSession($checkoutSession)
    {
        $this->checkoutSession = $checkoutSession;
    }

    public function getIdentifier()
    {
        // SomeClassNameLikeThis => some-class-name-like-this
        return Tools::camelCaseToKebabCase(get_class($this));
    }

    public function hasErrors()
    {
        return null != $this->errorMessages && !empty($this->errorMessages);
    }

    public function getErrors()
    {
        return $this->errorMessages;
    }
}
