<?php
namespace Tk\Form\Field;


/**
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 * @see http://www.google.com/recaptcha/intro/index.html
 */
class ReCapture extends Iface
{
    const RECAPTCHA_VERIFY_URL = 'https://www.google.com/recaptcha/api/siteverify';
    
    
    /**
     * @var string
     */
    protected $publicKey = '';

    /**
     * @var string
     */
    protected $privateKey = '';

    /**
     * @param string $name
     * @param $publicKey
     * @param $privateKey
     * @throws \Tk\Form\Exception
     */
    public function __construct($name, $publicKey, $privateKey)
    {
        parent::__construct($name);
        $this->publicKey = $publicKey;
        $this->privateKey = $privateKey;
    }

    /**
     * @param array|\ArrayObject $values
     * @return $this
     */
    public function load($values)
    {
        if ($this->getForm()->isSubmitted()) {
            $this->doValidate();
        }
        return $this;
    }

    /**
     * @param array $extraParams
     * @return bool
     */
    protected function doValidate($extraParams = array()) 
    {
        $request = \Tk\Request::createFromGlobals();
        $remoteIp = $request->getClientIp();
        $rResponse = isset($request['g-recaptcha-response']) ? $request['g-recaptcha-response'] : '';
        if (!$this->privateKey) {
            $this->addError('To use reCAPTCHA you must get an API key from <a href="https://www.google.com/recaptcha/intro/index.html">https://www.google.com/recaptcha/intro/index.html</a>');
            return false;
        }
        if (!$remoteIp) {
            $this->addError('For security reasons, you must pass the remote ip to reCAPTCHA');
            return  false;
        }
        if (!$rResponse) {
            $this->addError('incorrect captcha solution');
            return  false;
        }
        
        $res = $this->httpPost(self::RECAPTCHA_VERIFY_URL, array(
                'secret' => $this->privateKey,
                'response' => $rResponse,
                'remoteip' => $remoteIp
            )+$extraParams);
        $res = json_decode($res);

        $codes = array(
            'missing-input-secret' => 'The secret parameter is missing.',
            'invalid-input-secret' => 'The secret parameter is invalid or malformed.',
            'missing-input-response' => 'The response parameter is missing.',
            'invalid-input-response' => 'The response parameter is invalid or malformed.'
        );

        if (!$res || !$res->success) {
            $error = isset($codes[$res->{'error-codes'}]) ? $res->{'error-codes'} : 'Invalid Captcha value.';
            $this->addError($error);
            return  false;
        }
        
        return true;
    }

    /**
     * @param $url
     * @param $data
     * @param int $port
     * @return mixed
     */
    protected function httpPost ($url, $data, $port = 80) {
        // Get resource
        $curl = curl_init();
        // Configure options, incl. post-variables to send.
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $data
        ));
        // Send request. Due to CURLOPT_RETURNTRANSFER, this will return reply as string.
        $resp = curl_exec($curl);
        curl_close($curl);
        return $resp;
    }

    /**
     * @param mixed|string $html
     * @return $this
     */
    public function setValue($html)
    {
        return $this;
    }

    /**
     * Get the element HTML
     *
     * @return string|\Dom\Template
     * @throws \Dom\Exception
     */
    public function show()
    {
        /* @var \Dom\Template $template */
        $template = $this->getTemplate();
        if (!$template->keyExists('var', 'element')) {
            return $template;
        }
        $template->setAttr('element', 'data-sitekey', $this->publicKey);

        $template->insertHtml('element', $this->getValue());

        $this->decorateElement($template);
        return $template;
    }
    
    /**
     * makeTemplate
     *
     * @return \Dom\Template
     */
    public function __makeTemplate()
    {
        $xhtml = <<<HTML
<div>
  <script src="https://www.google.com/recaptcha/api.js" async="async" defer="defer" data-jsl-static="data-jsl-static"></script>
  <div class="g-recaptcha" data-sitekey="publicKey" var="element"></div>
</div>
HTML;
        return \Dom\Loader::load($xhtml);
    }

}