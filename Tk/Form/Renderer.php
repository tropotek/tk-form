<?php
namespace Tk\Form;

use Dom\Template;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Tk\Form;
use Tk\Traits\SystemTrait;

/**
 * @author Tropotek <http://www.tropotek.com/>
 */
class Renderer
{
    use SystemTrait;

    protected Form $form;

    protected ?EventDispatcherInterface $dispatcher;

    protected array $fieldTemplates = [];

    protected array $tabGroupTemplates = [];

    protected array $fieldsetTemplates = [];



    public function __construct(Form $form)
    {
        $this->form = $form;
        $this->dispatcher = $this->getFactory()->getEventDispatcher();
        $this->initFieldTemplates($this->makePath($this->getConfig()->get('form.template.path')));
    }

    protected function initFieldTemplates(string $path): void
    {
        // TODO: find out how to get this list dynamically???
        $tplNames = [
            'tpl-form',
            'tpl-hidden',
            'tpl-none',
            'tpl-input',
            'tpl-textarea',
            'tpl-select',
            'tpl-checkbox',
            'tpl-radio',
            'tpl-switch',
            'tpl-file',
            'tpl-button',
            'tpl-submit',
            'tpl-link',
        ];
        foreach ($tplNames as  $name) {
            $doc = new \DOMDocument();
            $doc->loadHTMLFile($path);
            $tpl = $doc->getElementById($name);
            $tpl->removeAttribute('id');
            $this->fieldTemplates[substr($name, 4)] = Template::load($doc->saveXML($tpl));
        }
        //vd(array_keys($this->fieldTemplates));
    }

    public function getFieldTemplate(string $fieldType): Template
    {
        if (isset($this->fieldTemplates[$fieldType])) {
            return clone $this->fieldTemplates[$fieldType];
        }
        return clone $this->fieldTemplates['hidden'];
    }

    public function getForm(): Form
    {
        return $this->form;
    }

    public function setForm(Form $form): static
    {
        $this->form = $form;
        return $this;
    }

    public function getDispatcher(): EventDispatcherInterface
    {
        return $this->dispatcher;
    }





}