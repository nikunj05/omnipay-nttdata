<?php
namespace Omnipay\Nttdata;

use Omnipay\Omnipay;
use Cx\Modules\checkout\omnipay\Omnipay\Common\AbstractGateway;
use Cx\Modules\checkout\lib\CheckoutLibraryFactory;
use Cx\Core\Html\Sigma;

/**
 * NTT Data Gateway
 *
 * @author    Nikunj Goriya <nikunj.goriya@payrexx.com>
 * @copyright 2013-2014 Cherry Ltd.
 * @license   http://opensource.org/licenses/mit-license.php MIT
 * @version   2.0.0
 */
class Gateway extends AbstractGateway
{
    /**
     * Category needed for PSP-Overview in settings
     *
     * @access      protected
     * @var         integer
     */
    protected $category = array(
                                CHECKOUT_PSP_CATEGORY_CC, 
                                CHECKOUT_PSP_CATEGORY_ALTERNATIVE
                            );

    /**
     * @return string
     */
    public function getType()
    {
        return 'omnipay';
    }

    /**
     * Available countries needed for PSP-Overview in settings
     *
     * @access      protected
     * @var         array
     */
    protected $countries = array(
            'NL', 'CN', 'KR', 'MY', 'PH', 'SG', 'TH'
        );

    protected $availablePaymentMethods = array(
            'creditcard'     => array(
                'visa',
                'mastercard',
                'american_express'
            ),
            'online_transfer' => array(
                'alipay',
                'paysbuy',
                'enets'
            )
        );

    /**
     * @return integer
     */
    public function getId()
    {
        return CHECKOUT_PSP_NTTDATA;
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->getParameter('apiKey');
    }

    /**
     * @param  string $value
     * @return $this
     */
    public function setApiKey($value)
    {
        return $this->setParameter('apiKey', $value);
    }

    /**
     * Get all available payment methods.
     *
     * @access      public
     * @return      array
     */
    public function getAvailablePaymentMethods()
    {
        return $this->availablePaymentMethods;
    }

    /**
     * PSP short identifier
     *
     * @access      public
     * @return      string
     */
    public function shortIdentifier()
    {
        return 'NTTDATA';
    }

    /**
     * PSP long identifier
     *
     * @access      public
     * @return      string
     */
    public function longIdentifier()
    {
        return "NTTDATA";
    }

    /**
     * Check if the current settings are valid.
     *
     * @access      public
     * @return      boolean
     */
    public function isValid()
    {
        
        $settings = $this->get();
        $currencies = $this->getCurrencies();
        return (!empty($settings['mode']) &&
                !empty($currencies) &&
                !empty($settings['payment_methods']) &&
                !empty($settings['merchant_id'])
        );
    }

    /**
     * 
     * @param type $settings
     */
    public function checkFormSubmit($settings)
    {
        $_ARRAYLANG = $this->getArrayLang();
        global $_CONFIG;
        if (isset($_POST['submit'])) {
            if(!empty($settings)) {

                if (empty($_POST['nttdata']['currencies'])) {
                    $this->addMessage($_ARRAYLANG['TXT_CHECKOUT_SETTINGS_ERROR_NO_CURRENCY_SELECTED'], 'alert');
                }

                if (empty($_POST['nttdata']['payment_methods'])) {
                    $this->addMessage($_ARRAYLANG['TXT_CHECKOUT_SETTINGS_ERROR_NO_PAYMENT_METHOD_SELECTED'], 'alert');
                }
                
                foreach ($_POST['nttdata'] as $name => $value) {
                    $settings[contrexx_input2raw($name)] = contrexx_input2raw($value);
                }
                
                if ($this->update($settings)) {
                    $this->addMessage($_ARRAYLANG['TXT_CHECKOUT_CHANGES_SAVED_SUCCESSFULLY']);
                } else {
                    $this->addMessage($_ARRAYLANG['TXT_CHECKOUT_SETTINGS_CHANGES_COULD_NOT_BE_SAVED'], 'alert');
                }
            } else {
                if (empty($_POST['nttdata']['currencies'])) {
                    $this->addMessage($_ARRAYLANG['TXT_CHECKOUT_SETTINGS_ERROR_NO_CURRENCY_SELECTED'], 'alert');
                }

                if (empty($_POST['nttdata']['payment_methods'])) {
                    $this->addMessage($_ARRAYLANG['TXT_CHECKOUT_SETTINGS_ERROR_NO_PAYMENT_METHOD_SELECTED'], 'alert');
                    return false;
                }
                
                foreach ($_POST['nttdata'] as $name => $value) {
                    $settings[contrexx_input2raw($name)] = contrexx_input2raw($value);
                }
                
                if ($this->insert($_POST['nttdata'])) {
                    $this->addMessage($_ARRAYLANG['TXT_CHECKOUT_CHANGES_SAVED_SUCCESSFULLY']);
                } else {
                    $this->addMessage($_ARRAYLANG['TXT_CHECKOUT_SETTINGS_CHANGES_COULD_NOT_BE_SAVED'], 'alert');
                }
            } 
        }
        return $settings;
    }

    /**
     * 
     * @param string $block
     * @param object $form
     * 
     * @return string
     */
    public function renderFrontend($block, $form = null)
    {
        $template = new \HTML_Template_Sigma();
        $settings = $this->get();
        switch ($block) {
            case 'fields':
                $template->loadTemplateFile(ASCMS_MODULE_PATH . '/checkout/View/Template/Frontend/Psp/' . $this->getName() . '.html');

                if ($this->useCreditCards()) {
                    $template->touchBlock('creditcards');
                } else {
                    $template->hideBlock('creditcards');
                }

                $template->setGlobalVariable($this->getArrayLang());
                $template->setVariable('CREDIT_CARD_FIELDS', $this->getCreditCardFields()->get());
                break;
            case 'info':
                $template->loadTemplateFile(ASCMS_MODULE_PATH . '/checkout/View/Template/Frontend/Psp/redirect_info.html');
                $template->setVariable('TXT_CHECKOUT_REDIRECT_INFO', $this->getRedirectInfo());
                break;
            case 'button':
                return '';
                break;
        }

        return $template->get();
    }

    /**
     * 
     * @param Sigma $template
     */
    public function renderBackendSettings(Sigma $template) 
    {
        global $_CONFIG;
        $_ARRAYLANG = Omnipay::getArrayLang();
        $checkout_library = CheckoutLibraryFactory::getInstance();
        $getNttdataDBSettings = $this->get();
        $arrNttdata        = $this->checkFormSubmit($getNttdataDBSettings);
        
        \JS::registerJS(ASCMS_MODULE_WEB_PATH . '/checkout/View/Script/Backend/Psp/Nttdata.min.js?v=1.0');

        \ContrexxJavascript::getInstance()->setVariable(array(
            'TXT_CHECKOUT_DISABLED' => $_ARRAYLANG['TXT_CHECKOUT_DISABLED'],
            'TXT_CHECKOUT_ENABLED' => $_ARRAYLANG['TXT_CHECKOUT_ENABLED'],
            'TXT_ADD' => $_ARRAYLANG['TXT_ADD'],
            'TXT_CHECKOUT_SETTINGS_PSP_MANAGE_PAYMENT_METHODS_REMOVE' => $_ARRAYLANG['TXT_CHECKOUT_SETTINGS_PSP_MANAGE_PAYMENT_METHODS_REMOVE'],
        ), 'checkout');

        // prepare all available payment method groups
        $paymentMethodOptions = '';
        foreach ($arrNttdata['payment_methods'] as $value) {
            $paymentMethodOptions .= '<option value="' .  $value 
                . '" selected="selected"></option>';
        }

        $paymentMethodSelection = '';
        foreach ($arrNttdata['payment_methods'] as $name) {
            $paymentMethodSelection .= '<img id="' . $name
                . '" class="active" width="50" height="32" src="' 
                . ASCMS_MODULE_WEB_PATH 
                . '/checkout/View/Media/svg_cardicons/card_' 
                . $name . '.svg"/>';
        }

        $availablePaymentMethods = $this->getAvailablePaymentMethods();
        $nttdataPaymentMethodGroupLinks = $nttdataPaymentMethodGroupTables = '';

        foreach ($availablePaymentMethods as $group => $paymentMethods) {
            $paymentGroupName = 
                'TXT_CHECKOUT_SETTINGS_PSP_NTTDATA_PAYMENT_METHOD_'
                . strtoupper($group);
            if (empty($_ARRAYLANG[$paymentGroupName])) {
                $_ARRAYLANG[$paymentGroupName] = $paymentGroupName;
            }
            // in CHECKOUT_NTTDATA_PAYMENT_METHOD_GROUPS
            $nttdataPaymentMethodGroupLinks .= "<a href='#' rel='$group'>" 
                . $_ARRAYLANG[$paymentGroupName] . "</a>";

            $rows = '<tr><th colspan="3">' . $_ARRAYLANG[$paymentGroupName] 
                    . '</th></tr>';

            foreach ($paymentMethods as $paymentMethod) {
                $paymentMethodName =
                    'TXT_CHECKOUT_SETTINGS_PSP_NTTDATA_PAYMENT_METHOD_'
                    . strtoupper($paymentMethod);
                if (empty($_ARRAYLANG[$paymentMethodName])) {
                    $_ARRAYLANG[$paymentMethodName] = $paymentMethodName;
                }
                if (in_array($paymentMethod, $arrNttdata['payment_methods'])) {
                    $button = '<button class="payment-method-group remove" 
                        name="remove" value="' . $paymentMethod . '">'
                        . $_ARRAYLANG[
                        'TXT_CHECKOUT_SETTINGS_PSP_MANAGE_PAYMENT_METHODS_REMOVE']
                        . '</button>';
                } else {
                    $button = '<button class="payment-method-group add" 
                            name="add" value="' . $paymentMethod . '">'
                        . $_ARRAYLANG['TXT_ADD']
                        . '</button>';
                }
                $icon = '<img width="50" src="' . ASCMS_MODULE_WEB_PATH
                        . '/checkout/View/Media/svg_cardicons/card_'
                        . $paymentMethod . '.svg">';
                $rows .= '<tr><td width="10%">' . $icon . '</td><td width="70%">'
                        . $_ARRAYLANG[$paymentMethodName]
                        . '</td><td width="20%" style="text-align:right;">'
                        . $button . '</td></tr>';
            }

            // in CHECKOUT_NTTDATA_PAYMENT_METHOD_GROUP_TABLES
            $table = <<<EOD
<div class="payment-method-group $group">
    <table class="adminlist">$rows</table>
</div>
EOD;
            $nttdataPaymentMethodGroupTables .= $table;
        }

        $currencyOptions = '';
        foreach ($checkout_library->getCurrencies() as $id => $currency) {
            $currencyOptions .= '<option value="' . $id . '"'
                . (in_array($id, $arrNttdata['currencies'])
                ? ' selected="selected"' : '') . '>' . $currency
                . '</option>';
        }

        $modeOptions = '
            <option value="test"' . (($arrNttdata['mode'] == 'test')
            ? ' selected="selected"' : '') . '>'
            . $_ARRAYLANG['TXT_CHECKOUT_SETTINGS_PSP_MODE_TEST']
            . '</option>
            <option value="live"' . (($arrNttdata['mode'] == 'live')
            ? ' selected="selected"' : '') . '>'
            . $_ARRAYLANG['TXT_CHECKOUT_SETTINGS_PSP_MODE_LIVE']
            . '</option>
        ';

        $template->setVariable(array(
            'CHECKOUT_NTTDATA_PAYMENT_METHOD_OPTIONS'      => $paymentMethodOptions,
            'CHECKOUT_NTTDATA_PAYMENT_METHOD_SELECTION'    => $paymentMethodSelection,
            'CHECKOUT_NTTDATA_CURRENCY_OPTIONS'            => $currencyOptions,
            'CHECKOUT_NTTDATA_PAYMENT_METHOD_GROUPS'       => $nttdataPaymentMethodGroupLinks,
            'CHECKOUT_NTTDATA_PAYMENT_METHOD_GROUP_TABLES' => $nttdataPaymentMethodGroupTables,
            'CHECKOUT_NTTDATA_MODE_OPTIONS'                => $modeOptions,
            'CHECKOUT_SETTINGS_PSP_NTTDATA_MERCHANT_ID' => $arrNttdata['merchant_id']
        ));
    }

    protected function getDefaultParams()
    {
        global $multiSiteInstanceName;
        $arrNttdata = $this->get();

        $currencies  = $arrNttdata['currencies'];
        $mode   = $arrNttdata['mode'];
        $merchantId  = $arrNttdata['merchant_id'];

        $language = isset($_POST['language']) ? contrexx_input2raw($_POST['language']) : LANG_ID;
        if (is_numeric($language)) {
            $language = \FWLanguage::getLanguageCodeById($language);
        }
        return array(
            'MerchantCode'                => $merchantId,
            'mode'               => $mode,
            'encoding'           => 'UTF-8',
            'solution_name'      => 'Payrexx',
            'solution_version'   => '1.0',
            'integrator_name'    => 'Payrexx AG',
            'integrator_version' => '1.0',
            'param'              => $multiSiteInstanceName,
            'language'           => $language
        );
    }

    /**
     * Store the values of the post in the session
     *
     * @param array $arrUserData
     * @param array $parameters
     */
    public function preProcess($arrUserData = array(), $parameters = array())
    {
        unset($_SESSION['nttdata']);
        $getNttdataDBSettings = $this->get();
        $paymentMethods = $getNttdataDBSettings['payment_methods'];

        $_SESSION['nttdata']['paymentType'] = isset($parameters['post']['paymentType']) ? contrexx_input2raw($parameters['post']['paymentType']) : '';

        $_SESSION['nttdata']['BRAND']       = isset($parameters['post']['BRAND']) ? contrexx_input2raw($parameters['post']['BRAND']) : '';

        if (!empty($_SESSION['nttdata']['paymentType'])) {
            switch ($_SESSION['nttdata']['paymentType']) {
                case 'cc':
                    $_SESSION['nttdata']['CCNo'] = isset($parameters['post']['cardnumber']) ? contrexx_input2raw($parameters['post']['cardnumber']) : '';
                    $_SESSION['nttdata']['CCHolderName'] = isset($parameters['post']['cardholder']) ? contrexx_input2raw($parameters['post']['cardholder']) : '';
                    $_SESSION['nttdata']['CCMonth']  = isset($parameters['post']['exp_month']) ? contrexx_input2raw($parameters['post']['exp_month']) : '';
                    $_SESSION['nttdata']['CCYear']   = isset($parameters['post']['exp_year']) ? contrexx_input2raw($parameters['post']['exp_year']) : '';
                    $_SESSION['nttdata']['CCCVV']        = isset($parameters['post']['cvc']) ? contrexx_input2raw($parameters['post']['cvc']) : '';
                    break;
                default :
                    break;
            }
        }
    }

    /**
     * Process the payment using PayOne
     * 
     * @param Object $landingPage
     * @param Transaction $transaction
     * @param  array $landingPageParams
     * 
     * @return array
     */
    public function process($landingPage, \Cx\Modules\checkout\Model\Entity\Transaction $transaction, $landingPageParams = null)
    {
        global $_CONFIG;    
        $reference = $transaction->getUuid();
        $arrNttdata = $this->get();
        $merchantId  = $arrNttdata['merchant_id'];
        $contact    = $transaction->getInvoice()->getContact();

        $personalData = array(
            'UserName' => $contact->getFirstName(),
            'country' => $contact->getCountryISOAlpha3(),
        );

        // if (!empty($contact->getTitleInCurrentLang())) {
        //     $personalData['title'] = $contact->getTitleInCurrentLang();
        // }
        // if (!empty($contact->getStreet())) {
        //     $personalData['street'] = $contact->getStreet();
        // }

        if (!empty($contact->getPhone())) {
            $personalData['UserContact'] = $contact->getPhone();
        }
       
        // if (!empty($contact->getPlace())) {
        //     $personalData['city'] = $contact->getPlace();
        // }

        if ($contact->getEmail()) {
            $personalData['UserEmail'] = $contact->getEmail();
        }
        $availablePaymentMethods = $this->getAvailablePaymentMethods();
        if (!empty($availablePaymentMethods)) {    
            $landingPageParams['PHPSESSID'] = session_id();
            $transaction->setCardBrand($_SESSION['nttdata']['BRAND']);
            $this->em->persist($transaction);
            $this->em->flush();
            
            $postParams = array(
                'MerchantKey' => 'apple',
                'MerchantCode' => $merchantId,
                'RefNo' => $reference,
                'Amount'       => $transaction->getAmount(),
                'Currency'     => $transaction->getInvoice()->getCurrency(),
                'PaymentId' => '2',
                'ProdDesc' => $_SESSION['checkout']['description'],
                'Signature' => '',
                'CCHolderName' => $_SESSION['nttdata']['CCHolderName'],
                'CCNo' => $_SESSION['nttdata']['CCNo'],
                'CCCVV' => $_SESSION['nttdata']['CCCVV'],
                'CCMonth' => $_SESSION['nttdata']['CCMonth'],
                'CCYear' => $_SESSION['nttdata']['CCYear'],
                //'notifyUrl' => 'https://dispatcher.payrexx.com.loc/skrill/response.php',
                'BackendURL' => 'https://dispatcher.payrexx.com/nttdata/response.php',
                'ResponseURL' => \Cx\Core\Routing\Url::fromPage($landingPage, array('result' => 1, 'ref' => $reference))->toString()
            );
            
            $request = array_merge($postParams, $personalData);
            $response = $this->purchase($request)->send();
            echo "<pre>"; var_dump($response->getMessage()); exit;
            if ($response->isRedirect()) {
                $transaction->setReference($reference);
                $this->em->persist($transaction);
                $this->em->flush();
                $paymentUrl = $response->getRedirectUrl();
                
                // Redirect to skrill payment gateway page
                $return['redirectUrl'] = $paymentUrl;
            } else {
                if ($response->getMessage()) {
                    $return['messages']['error'] = $response->getMessage();
                }  
            }
        }
        return $return;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Nttdata';
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultParameters()
    {
        return [
            'merchantCode'      => '',
            'merchantKey'  => '',
            'testMode'   => false,
        ];
    }

    /**
     * @access      public
     * @return      boolean
     */
    public function useCreditCards()
    {
        $settings = $this->get();
        $diff = array_intersect(array('visa', 'mastercard', 'american_express'), 
            $settings['payment_methods']);
        return !!count($diff);
    }

    /**
     * Get the merchant's code.
     *
     * @return string code
     */
    public function getMerchantId()
    {
        return $this->getParameter('merchant_id');
    }

    /**
     * Set the merchant's code.
     *
     * @param string $value code
     *
     * @return self
     */
    public function setMerchantId($value)
    {
        return $this->setParameter('merchant_id', $value);
    }

    /**
     * Get the URL to which the transaction details will be posted after the payment
     * process is complete.
     *
     * @return string notify url
     */
    public function getNotifyUrl()
    {
        return $this->getParameter('notifyUrl');
    }

    /**
     * Set the URL to which the transaction details will be posted after the payment
     * process is complete.
     *
     * Alternatively you may specify an email address to which you would like to receive
     * the results. If the notify url is omitted, no transaction details will be sent to
     * the merchant.
     *
     * @param string $value notify url
     *
     * @return self
     */
    public function setNotifyUrl($value)
    {
        return $this->setParameter('notifyUrl', $value);
    }

    /**
     * Get the secret word used for signatures.
     *
     * @return string Secret word
     */
    public function getSecretWord()
    {
        return $this->getParameter('secretWord');
    }

    /**
     * Set the secret word used for signatures.
     *
     * @param string $value Secret word
     *
     * @return self
     */
    public function setSecretWord($value)
    {
        return $this->setParameter('secretWord', $value);
    }

    /**
     * Create a new charge.
     *
     * @param  array $parameters request parameters
     *
     * @return Message\PaymentResponse               response
     */
    public function purchase(array $parameters = [])
    {
        //echo "<pre>"; print_r($parameters); exit;
        return $this->createRequest('Omnipay\Nttdata\Message\PaymentRequest', $parameters);
    }

    /**
     * Finalises a payment (callback).
     *
     * @param  array $parameters request parameters
     *
     * @return Message\PaymentResponse               response
     */
    public function completePurchase(array $parameters = [])
    {
        return $this->createRequest('Omnipay\Nttdata\Message\CompletePurchaseRequest', $parameters);
    }

    /**
     * Authorize and prepare a refund.
     *
     * @param  array $parameters request parameters
     *
     * @return Message\AuthorizeResponse               response
     */
    public function refund(array $parameters = [])
    {
        return $this->createRequest('Omnipay\Nttdata\Message\RefundRequest', $parameters);
    }
}
