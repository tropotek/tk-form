<?php

$fieldTemplates = [];

$fieldTemplates['form'] = [
    'template' => <<<HTML
      <form class="tk-form tk-form-inline {css}" {attrs}>
        {hidden}
        <div class="tk-form-fields row gy-1 gx-2 align-items-center">{fields} {actions}</div>        
      </form>
    HTML,
];

$fieldTemplates['tpl-form-hidden'] = [
    'template' => '<input {attrs}>',
];

$fieldTemplates['tpl-form-input'] = [
    'template' => <<<HTML
      <div class="col-auto {fieldCss}" {fieldAttrs}>
        {labelBlock}
        <label class="form-label visually-hidden {labelCss}" {labelAttrs}>{label}</label>
        {/labelBlock}
        <input class="form-control form-control-sm {css}" {attrs}>
        {noteBlock}
          <div class="form-text text-secondary">{notes}</div>
        {/noteBlock}
        {errorBlock}
          <div class="invalid-feedback">{error}</div>
        {/errorBlock}
      </div>
    HTML,
];

$fieldTemplates['tpl-form-input-button'] = [
    'template' => <<<HTML
      <div class="col-auto {fieldCss}" {fieldAttrs}>
        {labelBlock}
        <label class="form-label visually-hidden {labelCss}" {labelAttrs}>{label}</label>
        {/labelBlock}
        <div class="input-group input-group-merge {inputGroupCss}">
          <input class="form-control form-control-sm {css}" {attrs}>
          <button class="btn btn-white {buttonCss}" type="button" {buttonAttrs}>{buttonText}</button>
        </div>
        {noteBlock}
          <div class="form-text text-secondary">{notes}</div>
        {/noteBlock}
        {errorBlock}
          <div class="invalid-feedback" var="error">{error}</div>
        {/errorBlock}
      </div>
    HTML,
];

$fieldTemplates['tpl-form-input-link'] = [
    'template' => <<<HTML
      <div class="col-auto {fieldCss}" {fieldAttrs}>
        {labelBlock}
        <label class="form-label visually-hidden {labelCss}" {labelAttrs}>{label}</label>
        {/labelBlock}
        <div class="input-group input-group-merge {inputGroupCss}">
          <input class="form-control form-control-sm {css}" {attrs}>
          <a class="btn btn-white {buttonCss}" {buttonAttrs}>{buttonText}</a>
        </div>
        {noteBlock}
          <div class="form-text text-secondary">{notes}</div>
        {/noteBlock}
        {errorBlock}
          <div class="invalid-feedback" var="error">{error}</div>
        {/errorBlock}
      </div>
    HTML,
];

$fieldTemplates['tpl-form-html'] = [
    'template' => <<<HTML
      <div class="col-auto {fieldCss}" {fieldAttrs}>
        {labelBlock}
        <label class="form-label visually-hidden {labelCss}" {labelAttrs}>{label}</label>
        {/labelBlock}
        <div class="{css}" {attrs}>{html}</div>
        {noteBlock}
          <div class="form-text text-secondary">{notes}</div>
        {/noteBlock}
        {errorBlock}
          <div class="invalid-feedback" var="error">{error}</div>
        {/errorBlock}
      </div>
    HTML,
];

$fieldTemplates['tpl-form-file'] = [
    'template' => <<<HTML
      <div class="col-auto {fieldCss}" {fieldAttrs}>
        {labelBlock}
        <label class="form-label visually-hidden {labelCss}" {labelAttrs}>{label}</label>
        {/labelBlock}
        
        <div class="input-group input-group-merge {inputGroupCss}" {inputGroupAttrs}>
          <input class="form-control form-control-sm {css}" {attrs}/>
          {viewBlock}
            <a class="btn btn-white" href="{viewUrl}" target="_blank" {viewAttrs}><i class="fa fa-eye"></i></a>
          {/viewBlock}
          {deleteBlock}
            <a class="btn btn-white" href="{deleteUrl}" title="Delete" data-confirm="Are you sure you want to delete this file?" {deleteAttrs}><i class="fa fa-trash"></i></a>
          {/deleteBlock}
        </div>
        
        {noteBlock}
          <div class="form-text text-secondary">{notes}</div>
        {/noteBlock}
        {errorBlock}
          <div class="invalid-feedback" var="error">{error}</div>
        {/errorBlock}
      </div>
    HTML,
];

$fieldTemplates['tpl-form-textarea'] = [
    'template' => <<<HTML
      <div class="col-auto {fieldCss}" {fieldAttrs}>
        {labelBlock}
        <label class="form-label visually-hidden {labelCss}" {labelAttrs}>{label}</label>
        {/labelBlock}
        <textarea class="form-control form-control-sm {css}" {attrs}>{value}</textarea>
        {noteBlock}
          <div class="form-text text-secondary">{notes}</div>
        {/noteBlock}
        {errorBlock}
          <div class="invalid-feedback" var="error">{error}</div>
        {/errorBlock}
      </div>
    HTML,
];

$fieldTemplates['tpl-form-select'] = [
    'template' => <<<HTML
      <div class="col-auto {fieldCss}" {fieldAttrs}>
        {labelBlock}
        <label class="form-label visually-hidden {labelCss}" {labelAttrs}>{label}</label>
        {/labelBlock}
        <select class="form-select form-select-sm {css}" {attrs}>
          {options}
        </select>
        {noteBlock}
          <div class="form-text text-secondary">{notes}</div>
        {/noteBlock}
        {errorBlock}
          <div class="invalid-feedback" var="error">{error}</div>
        {/errorBlock}
      </div>
    HTML,
];

$fieldTemplates['tpl-form-checkbox'] = [
    'template' => <<<HTML
      <div class="col-auto {fieldCss}" {fieldAttrs}>
        {labelBlock}
        <label class="form-label visually-hidden {labelCss}" {labelAttrs}>{label}</label>
        {/labelBlock}
        {optionsBlock}
          <div class="form-check {optionCss}">
            <input type="hidden" name="{shadowName}" value="">
            <input type="checkbox" class="form-check-input {css}" {attrs}>
            <label class="form-check-label" for="{id}">{label}</label>
            {noteBlock}
                <p class="m-0 cb-notes text-muted">{notes}</p>
            {/noteBlock}
          </div>
        {/optionsBlock}
        {noteBlock}
          <div class="form-text text-secondary">{notes}</div>
        {/noteBlock}
        {errorBlock}
          <div class="invalid-feedback" var="error">{error}</div>
        {/errorBlock}
      </div>
    HTML,
];

$fieldTemplates['tpl-form-radio'] = [
    'template' => <<<HTML
      <div class="col-auto {fieldCss}" {fieldAttrs}>
        {labelBlock}
        <label class="form-label visually-hidden {labelCss}" {labelAttrs}>{label}</label>
        {/labelBlock}
        {optionsBlock}
          <div class="form-check {optionCss}">
            <input type="hidden" name="{shadowName}" value="">
            <input type="radio" class="form-check-input {css}" {attrs}>
            <label class="form-check-label" for="{id}">{label}</label>
            {noteBlock}
                <p class="m-0 cb-notes text-muted">{notes}</p>
            {/noteBlock}
          </div>
        {/optionsBlock}
        {noteBlock}
          <div class="form-text text-secondary">{notes}</div>
        {/noteBlock}
        {errorBlock}
          <div class="invalid-feedback" var="error">{error}</div>
        {/errorBlock}
      </div>
    HTML,
];

$fieldTemplates['tpl-form-submit'] = [
    'template' => <<<HTML
      <div class="tk-inline-btn col-auto {fieldCss}" {fieldAttrs}>
        <button class="btn btn-outline-primary {css}" {attrs}>
          {lIconBlock}<i class="{css}"></i>{/lIconBlock}
          <span>{text}</span>
          {rIconBlock}<i class="{css}"></i>{/rIconBlock}
        </button>
      </div>
    HTML,
];

$fieldTemplates['tpl-form-submit-exit'] = [
    'template' => <<<HTML
      <div class="tk-inline-btn col-auto {fieldCss}" {fieldAttrs}>
        <div class="btn-group" id="tpl-form-submit-exit">
          <button type="submit" class="btn btn-outline-primary" name="{name}" value="{value}" title="{title}"><i class="fa fa-caret-left"></i></button>
          <button class="btn btn-outline-primary {css}" {attrs}>
            {lIconBlock}<i class="{css}"></i>{/lIconBlock}
            <span>{text}</span>
            {rIconBlock}<i class="{css}"></i>{/rIconBlock}
          </button>
        </div>
      </div>
    HTML,
];

$fieldTemplates['tpl-form-button'] = [
    'template' => <<<HTML
      <div class="tk-inline-btn col-auto {fieldCss}" {fieldAttrs}>
        <button class="btn btn-outline-secondary {css}" {attrs}>
          {lIconBlock}<i class="{css}"></i>{/lIconBlock}
          <span>{text}</span>
          {rIconBlock}<i class="{css}"></i>{/rIconBlock}
        </button>
      </div>
    HTML,
];

$fieldTemplates['tpl-form-link'] = [
    'template' => <<<HTML
      <div class="tk-inline-btn col-auto {fieldCss}" {fieldAttrs}>
        <a class="btn btn-outline-secondary {css}" {attrs}>
          {lIconBlock}<i class="{css}"></i>{/lIconBlock}
          <span>{text}</span>
          {rIconBlock}<i class="{css}"></i>{/rIconBlock}
        </a>
      </div>
    HTML,
];






return $fieldTemplates;
