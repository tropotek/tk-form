<?php
/*
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2007 Michael Mifsud
 */
namespace Form\Field;

/**
 * A form single option select box.
 * Only one option can be selected.
 *
 * The select box is sent an array of options in the following format:
 * <code>
 *   $options = array(
 *     array('name1', 'value 1'),
 *     array('name2', 'value 2')
 *   );
 * </code>
 *
 *
 * @package Form\Field
 */
class DualSelect extends Iface
{
    /**
     * @var array
     */
    protected $options = array();

    /**
     * @var array
     */
    protected $prependOptions = array();

    /**
     * @var array
     */
    protected $prependSelected = array();



    /**
     * Create an instance of this object
     *
     * @param string $name
     * @param array $options
     * @param \Form\Type\Iface $type
     *
     */
    public function __construct($name, $options = null, $type = null)
    {
        parent::__construct($name, $type);


        if ($options instanceof \Tk\Db\ArrayObject) {
            foreach ($options as $o) {
                $this->options[] = array($o->getSelectText(), $o->getSelectValue());
            }
        } else if (is_array($options) && count($options)) {
            $this->options = $options;
        }

    }

    /**
     * Does this field return data as an array.
     * This will happen when the field name ends in '[]'
     * So this can happen for multiple checkboxes and multi select lists etc...
     *
     * @return bool
     */
    public function hasArrayData()
    {
        return true;
    }

    /**
     * Compare a value and see if it si selected.
     *
     * @param string $val
     * @return bool
     */
    protected function isSelected($val)
    {
        $values = $this->type->getFieldValues();
        if (!array_key_exists($this->name, $values)) {
            return false;
        }
        if ($values[$this->name] instanceof \Tk\Db\ArrayObject) {
            foreach ($values[$this->name] as $obj) {
                if ($obj->id == $val) return true;
            }
        } else {
            if ($values && in_array($val, $values[$this->name])) {
                return true;
            }
        }
        return false;
    }

    /**
     * Render the default attributes of an element
     */
    public function show()
    {
        $t = $this->getTemplate();
        if (!$t->keyExists('var', 'element')) {
            return;
        }
        if (!$this->enabled) {
            $t->setAttr('element', 'disabled', 'disabled');
            $t->setAttr('el2', 'disabled', 'disabled');
        }
        if ($this->readonly) {
            $t->setAttr('element', 'readonly', 'readonly');
            $t->setAttr('el2', 'readonly', 'readonly');
        }
        if (!$this->autocomplete) {
            $t->setAttr('element', 'autocomplete', 'off');
            $t->setAttr('el2', 'autocomplete', 'off');
        }
        if ($this->accessKey) {
            $t->setAttr('element', 'accesskey', $this->accessKey);
            $t->setAttr('el2', 'accesskey', $this->accessKey);
        }
        if ($this->tabindex > 0) {
            $t->setAttr('element', 'tabindex', $this->tabindex);
            $t->setAttr('el2', 'tabindex', $this->tabindex);
        }
        
        foreach ($this->cssList as $v) {
            $t->addClass('element', $v);
            $t->addClass('el2', $v);
        }

        foreach ($this->attrList as $attr => $js) {
            $t->setAttr('element', $attr, $js);
            $t->setAttr('el2', $attr, $js);
        }
        $styleStr = '';
        foreach ($this->styleList as $style => $val) {
            $styleStr .= $style . ': ' . $val . '; ';
        }
        if ($styleStr) {
            $t->setAttr('element', 'style', $styleStr);
            $t->setAttr('el2', 'style', $styleStr);
        }

        $selected = array();
        foreach ($this->options as $arr) {
            if (is_array($arr) && $this->isSelected($arr[1])) {
                $selected[] = $arr;
                continue;
            }
            $row = $t->getRepeat('option');
            $row->setAttr('option', 'value', $arr[1]);
            $row->insertText('option', trim($arr[0]));
            $row->appendRepeat();
        }

        foreach ($selected as $arr) {
            if (!is_array($arr)) {
                continue;
            }
            $row = $t->getRepeat('selected');
            $row->setAttr('selected', 'value', $arr[1]);
            $row->insertText('selected', trim($arr[0]));
            $row->appendRepeat();
        }


        $t->setAttr('element', 'name', $this->name . '[]');
        $t->setAttr('element', 'id', $this->getId());

    }

    /**
     * makeTemplate
     *
     * @return string
     */
    public function __makeTemplate()
    {
        $xmlStr = <<<XML
<?xml version="1.0"?>
<div>
<style>
.tk-DualSelect .buttonPanel .button {
  width: 20px;
  margin: 4px 10px;
  padding: 0;
  border: 1px solid #999;
  background-color: #CCC;
}
.tk-DualSelect .buttonPanel .button:hover {
  cursor: pointer;
  background-color: #999;
}
.tk-DualSelect .listBox {
  vertical-align: top;
  display: inline-block;
}
.tk-DualSelect .listBox strong {
  display: inline-block;
  padding: 5px 10px;
}
.tk-DualSelect .listBox select {
  height: 150px;
  width: 250px;
}
.tk-DualSelect .buttonPanel {
  display: inline-block;
  padding: 30px 0px 0px 0px;
}
</style>
<script>
jQuery(function($) {
    /**
     * De-Select all option from the sleect box's
     */
    function cleanSelected(dbox)
    {
        $(dbox).find('.options select :selected, .selected select :selected').each(function(i, selected) {
            $(selected).removeAttr('selected');
        });
    }

    $('.tk-DualSelect').each(function (i, dbox) {
        var form = $(dbox).parents('form');

        // Ensure all options are selected onSubmit so the values are sent in the query.
        $(form).submit(function (e) {
            $(dbox).find('.selected select option').attr('selected', 'selected').prop('selected', true);
        });


        
        $(dbox).find('.add').click( function (e) {
            $(dbox).find('.options select :selected').each(function(i, selected) {
                $(selected).detach();
                $(dbox).find('.selected select').append(selected);
            });
            cleanSelected(dbox);
        });

        $(dbox).find('.addall').click( function (e) {
            $(dbox).find('.options select option').each(function(i, selected) {
                $(selected).detach();
                $(dbox).find('.selected select').append(selected);
            });
            cleanSelected(dbox);
        });

        $(dbox).find('.removeall').click( function (e) {
            $(dbox).find('.selected select option').each(function(i, selected) {
                $(selected).detach();
                $(dbox).find('.options select').append(selected);
            });
            cleanSelected(dbox);
        });

        $(dbox).find('.remove').click( function (e) {
            $(dbox).find('.selected select :selected').each(function(i, selected) {
                $(selected).detach();
                $(dbox).find('.options select').append(selected);
            });
            cleanSelected(dbox);
        });

    });

});
</script>

    <div class="tk-DualSelect field">
      <div class="listBox options">
        <strong>Options</strong><br/>
        <select class="" multiple="multiple" var="el2"><option repeat="option" var="option"></option></select>
      </div>
      <div class="buttonPanel">
        <div class="button add ui-state-default ui-corner-all">
         <span class="ui-icon ui-icon-arrowthick-1-e" title="Add Selected"></span>
        </div>
        <div class="button addall ui-state-default ui-corner-all">
          <span class="ui-icon ui-icon-arrowthickstop-1-e" title="Add All"></span>
        </div>
        <div class="button removeall ui-state-default ui-corner-all">
          <span class="ui-icon ui-icon-arrowthickstop-1-w" title="Remove All"></span>
        </div>
        <div class="button remove ui-state-default ui-corner-all">
          <span class="ui-icon ui-icon-arrowthick-1-w" title="Remove Selected"></span>
        </div>
      </div>
      <div class="listBox selected">
        <strong>Selected</strong><br/>
        <select class="" multiple="multiple" var="element"><option repeat="selected" var="selected"></option></select>
      </div>
    </div>
</div>
XML;

        $template = \Mod\Dom\Loader::load($xmlStr, $this->getClassName());
        return $template;
    }
}