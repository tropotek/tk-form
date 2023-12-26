<?php

$fieldTemplates = [];

$fieldTemplates['form'] = [
    'options' => [
        'valid-css' => 'is-valid',
        'error-css' => 'is-invalid',
    ],
    'script' => <<<JS
      // Example starter JavaScript for disabling form submissions if there are invalid fields
      // https://getbootstrap.com/docs/5.2/forms/validation/
      (() => {
        'use strict'
        // Fetch all the forms we want to apply custom Bootstrap validation styles to
        const forms = document.querySelectorAll('.needs-validation');
        // Loop over them and prevent submission
        Array.from(forms).forEach(form => {
          form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
              event.preventDefault()
              event.stopPropagation()
            }
            form.classList.add('was-validated')
          }, false)
        })
      })();
    JS,
    'template' => <<<HTML
      <form class="tk-form g-3 {css}" {attrs}>
        {hidden}
    
        {errors}
          <div class="tk-form-errors row">
            {error}
              <span class="error text-danger">{message}</span>
            {/error}
          </div>
        {/errors}
        
        <div class="tk-form-fields row g-3 mt-1">{fields}</div>
        <div class="tk-actions d-grid gap-2 d-md-flex mt-2">{actions}</div>
        
      </form>
    HTML,
];

$fieldTemplates['tpl-form-hidden'] = [
    'template' => '<input {attrs}>',
];

$fieldTemplates['tpl-form-input'] = [
    'template' => <<<HTML
      <div class="{fieldCss}" {fieldAttrs}>
        {labelBlock}
        <label class="form-label {labelCss}" {labelAttrs}>{label}</label>
        {/labelBlock}
        <input class="form-control {css}" {attrs}>
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
      <div class="{fieldCss}" {fieldAttrs}>
        {labelBlock}
        <label class="form-label {labelCss}" {labelAttrs}>{label}</label>
        {/labelBlock}
        <div class="input-group input-group-merge {inputGroupCss}">
          <input class="form-control {css}" {attrs}>
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
      <div class="{fieldCss}" {fieldAttrs}>
        {labelBlock}
        <label class="form-label {labelCss}" {labelAttrs}>{label}</label>
        {/labelBlock}
        <div class="input-group input-group-merge {inputGroupCss}">
          <input class="form-control {css}" {attrs}>
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
      <div class="{fieldCss}" {fieldAttrs}>
        {labelBlock}
        <label class="form-label {labelCss}" {labelAttrs}>{label}</label>
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
      <div class="{fieldCss}" {fieldAttrs}>
        {labelBlock}
        <label class="form-label {labelCss}" {labelAttrs}>{label}</label>
        {/labelBlock}
        
        <div class="input-group input-group-merge {inputGroupCss}" {inputGroupAttrs}>
          <input class="form-control {css}" {attrs}/>
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
      <div class="{fieldCss}" {fieldAttrs}>
        {labelBlock}
        <label class="form-label {labelCss}" {labelAttrs}>{label}</label>
        {/labelBlock}
        <textarea class="form-control {css}" {attrs}>{value}</textarea>
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
      <div class="{fieldCss}" {fieldAttrs}>
        {labelBlock}
        <label class="form-label {labelCss}" {labelAttrs}>{label}</label>
        {/labelBlock}
        <select class="form-select {css}" {attrs}>
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
      <div class="{fieldCss}" {fieldAttrs}>
        {labelBlock}
        <label class="form-label {labelCss}" {labelAttrs}>{label}</label>
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
      <div class="{fieldCss}" {fieldAttrs}>
        {labelBlock}
        <label class="form-label {labelCss}" {labelAttrs}>{label}</label>
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
      <button class="btn btn-outline-primary {css}" {attrs}>
        {lIconBlock}<i class="{css}"></i>{/lIconBlock}
        <span>{text}</span>
        {rIconBlock}<i class="{css}"></i>{/rIconBlock}
      </button>
    HTML,
];

$fieldTemplates['tpl-form-submit-exit'] = [
    'template' => <<<HTML
      <div class="btn-group" id="tpl-form-submit-exit">
        <button type="submit" class="btn btn-outline-primary" name="{name}" value="{value}" title="{title}"><i class="fa fa-caret-left"></i></button>
        <button class="btn btn-outline-primary {css}" {attrs}>
          {lIconBlock}<i class="{css}"></i>{/lIconBlock}
          <span>{text}</span>
          {rIconBlock}<i class="{css}"></i>{/rIconBlock}
        </button>
      </div>
    HTML,
];

$fieldTemplates['tpl-form-button'] = [
    'template' => <<<HTML
      <button class="btn btn-outline-secondary {css}" {attrs}>
        {lIconBlock}<i class="{css}"></i>{/lIconBlock}
        <span>{text}</span>
        {rIconBlock}<i class="{css}"></i>{/rIconBlock}
      </button>
    HTML,
];

$fieldTemplates['tpl-form-link'] = [
    'template' => <<<HTML
      <a class="btn btn-outline-secondary {css}" {attrs}>
        {lIconBlock}<i class="{css}"></i>{/lIconBlock}
        <span>{text}</span>
        {rIconBlock}<i class="{css}"></i>{/rIconBlock}
      </a>
    HTML,
];






return $fieldTemplates;
