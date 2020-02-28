<?php
namespace WilokeListingTools\Controllers;

use WilokeListingTools\Framework\Helpers\FileSystem;
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\GetWilokeSubmission;
use WilokeListingTools\Framework\Helpers\Inc;
use WilokeListingTools\Framework\Helpers\WooCommerce;
use WilokeListingTools\Framework\Routing\Controller;
use WilokeListingTools\Frontend\User;
use WilokeListingTools\Models\InvoiceMetaModel;
use WilokeListingTools\Models\InvoiceModel;
use WilokeListingTools\Models\PaymentMetaModel;
use WilokeListingTools\Models\PaymentModel;
use WilokeListingTools\Models\PlanRelationshipModel;
use Konekt\PdfInvoice\InvoicePrinter;

class InvoiceController extends Controller
{
    public function __construct()
    {
        add_action('wiloke-listing-tools/changed-payment-status', [$this, 'update']);
        add_action('wiloke-listing-tools/woocommerce/after-order-succeeded',
            [$this, 'insertNewInvoiceAfterPayViaWooCommerceSucceeded'], 10, 1);
        add_action('woocommerce_subscription_payment_complete',
            [$this, 'insertNewInvoiceAfterPaymentViaWooCommerceSubscriptionRenewed'], 10, 1);

        add_action('wp_ajax_delete_all_invoices', [$this, 'deleteAllInvoices']);
        add_action('wilcity/paypal/insert-invoice', [$this, 'prepareInsertInvoice'], 10, 2);
        add_action('wilcity/stripe/insert-invoice', [$this, 'prepareInsertInvoice'], 10, 2);
        add_action('wilcity/direct-bank-transfer/insert-invoice', [$this, 'prepareInsertInvoice'], 10, 2);
        add_action('wp_ajax_download_invoice', [$this, 'downloadInvoice']);
        add_filter('wilcity/theme-options/configurations', [$this, 'addInvoiceSettingsToThemeOptions']);
        add_filter('wilcity/wiloke-listing-tools/invoice-attachment', [$this, 'generateInvoiceDownloadFilePath'], 10,
            2);
        add_action('init', [$this, 'emailDownloadInvoiceAsPdf']);
    }

    /*
     * This link will be inserted to customer email, and when clicking on this link, it will redirect this
     * home page and this function will handle it
     *
     * @since 1.2.0
     */
    public function emailDownloadInvoiceAsPdf()
    {
        if (!isset($_GET['action']) || $_GET['action'] != 'download_invoice' || empty($_GET['invoice'])) {
            return false;
        }

        $aParseInvoice = maybe_unserialize(base64_decode($_GET['invoice']));
        $url           = esc_url(FileSystem::getWilcityFolderUrl().implode('/', $aParseInvoice).'.pdf');

        header("Content-type:application/pdf");
        header("Content-Disposition: attachment;filename=".trim($aParseInvoice[1]).'.pdf');
        readfile($url);
        die();
    }

    /*
     * Adding Invoice Settings To Theme Options
     *
     * @since 1.2.0
     */
    public function addInvoiceSettingsToThemeOptions($aOptions)
    {
        $aOptions[] = [
            'title'            => 'Invoice Settings',
            'id'               => 'invoice_settings',
            'icon'             => 'dashicons dashicons-book-alt',
            'subsection'       => false,
            'customizer_width' => '500px',
            'fields'           => [
                [
                    'id'          => 'invoice_logo',
                    'type'        => 'media',
                    'title'       => 'Invoice Logo',
                    'description' => 'Leave empty to use Logo that uploaded under General Setting',
                ],
                [
                    'id'      => 'invoice_size',
                    'type'    => 'select',
                    'title'   => 'Invoice Size',
                    'options' => [
                        'A4'     => 'A4',
                        'Letter' => 'Letter',
                        'Legal'  => 'Legal'
                    ],
                    'default' => 'A4'
                ],
                [
                    'id'      => 'invoice_type',
                    'type'    => 'text',
                    'title'   => 'Invoice Type',
                    'default' => 'Sale Invoice'
                ],
                [
                    'id'      => 'invoice_reference',
                    'type'    => 'text',
                    'title'   => 'Invoice Reference',
                    'default' => 'IVC-%invoiceID%',
                ],
                [
                    'id'     => 'invoice_seller_section_settings_open',
                    'type'   => 'section',
                    'title'  => 'Seller Settings',
                    'indent' => true
                ],
                [
                    'id'      => 'invoice_billing_from_title',
                    'type'    => 'text',
                    'title'   => 'Billing From Title',
                    'default' => 'Billing From'
                ],
                [
                    'id'      => 'invoice_seller_company_name',
                    'type'    => 'text',
                    'title'   => 'Company Name',
                    'default' => 'Sample Company Name'
                ],
                [
                    'id'      => 'invoice_seller_company_address',
                    'type'    => 'text',
                    'title'   => 'Company Address',
                    'default' => '172 HoanKiem street'
                ],
                [
                    'id'      => 'invoice_seller_company_city_country',
                    'type'    => 'text',
                    'title'   => 'Company City and Country',
                    'default' => 'Hanoi, Vietnam'
                ],
                [
                    'id'     => 'invoice_seller_section_settings_close',
                    'type'   => 'section',
                    'title'  => '',
                    'indent' => false
                ],
                [
                    'id'     => 'invoice_purchaser_section_settings_open',
                    'type'   => 'section',
                    'title'  => 'Purchaser Settings',
                    'indent' => true
                ],
                [
                    'id'      => 'invoice_billing_to_title',
                    'type'    => 'text',
                    'title'   => 'Billing To Title',
                    'default' => 'Billing To'
                ],
                [
                    'id'     => 'invoice_purchaser_section_settings_close',
                    'type'   => 'section',
                    'title'  => '',
                    'indent' => false
                ],
                [
                    'id'      => 'invoice_badge',
                    'type'    => 'text',
                    'title'   => 'Badge Name',
                    'default' => 'Payment Paid'
                ],
                [
                    'id'      => 'invoice_notice_title',
                    'type'    => 'text',
                    'title'   => 'Notice Title',
                    'default' => 'Important Notice'
                ],
                [
                    'id'      => 'invoice_notice_description',
                    'type'    => 'textarea',
                    'title'   => 'Notice Description',
                    'default' => 'No item will be replaced or refunded if you don\'t have the invoice with you'
                ],
                [
                    'id'      => 'invoice_download_file_name',
                    'type'    => 'text',
                    'title'   => 'Download File Name',
                    'default' => 'INV-%invoiceID%-%invoiceDate%'
                ]
            ]
        ];

        return $aOptions;
    }

    /*
     * Generate Invoice
     *
     * @since 1.2.0
     * @var $outputType  I => Display on browser, D => Force Download, F => local path save, S => return document as string
     * @var $aData Array | params: invoiceID, userID
     */
    protected function generateInvoice($aData, $outputType = 'D')
    {
        $locale  = get_locale();
        $aLocale = explode('_', $locale);

        $userID = isset($aData['userID']) && !empty($aData['userID']) ? $aData['userID'] : '';

        $aInvoice = InvoiceModel::getInvoiceDetails($aData['invoiceID'], $userID);

        if (empty($aInvoice)) {
            return false;
        }

        $planID = PaymentModel::getField('planID', $aInvoice['paymentID']);

        $currency = apply_filters('wilcity/wiloke-listing-tools/generateInvoice/currency',
            html_entity_decode(GetWilokeSubmission::getSymbol(strtoupper($aInvoice['currency'])), ENT_COMPAT, 'UTF-8'));
//		$currency = iconv('UTF-8', 'iso-8859-2//TRANSLIT//IGNORE', $currency);

        $oInvoicePrinter = new InvoicePrinter(\WilokeThemeOptions::getOptionDetail('invoice_size'), $currency,
            $aLocale[0]);
        $oInvoicePrinter->AddFont('arialpl', '', 'arialpl.php');
        $oInvoicePrinter->font = 'arialpl';
//		$oInvoicePrinter->SetFont('times');

        $dateFormat = get_option('date_format');

        $aInvoiceLogo = \WilokeThemeOptions::getOptionDetail('invoice_logo');
        $billingDate  = date_i18n($dateFormat, strtotime($aInvoice['created_at']));
        $billingTime  = date_i18n(get_option('time_format'), strtotime($aInvoice['created_at']));

        if (isset($aInvoiceLogo['url']) && !empty($aInvoiceLogo['url'])) {
            $invoiceLogoUrl = $aInvoiceLogo['url'];
        } else {
            $aSiteLogo = \WilokeThemeOptions::getOptionDetail('general_logo');
            if (isset($aSiteLogo['url']) && !empty($aSiteLogo['url'])) {
                $invoiceLogoUrl = $aSiteLogo['url'];
            }
        }

        /* Header settings */
        if (!empty($invoiceLogoUrl)) {
            $oInvoicePrinter->setLogo($invoiceLogoUrl);
        }

        $oInvoicePrinter->setColor(GetSettings::getThemeColor());
        $oInvoicePrinter->setType(\WilokeThemeOptions::getOptionDetail('invoice_type'));

        $invoiceReference = \WilokeThemeOptions::getOptionDetail('invoice_reference');

        if (!empty($invoiceReference)) {
            $invoiceReference = str_replace('%invoiceID%', $aData['invoiceID'], $invoiceReference);
            $oInvoicePrinter->setReference($invoiceReference);
        }

        $oInvoicePrinter->setDate($billingDate);   //Billing Date
        $oInvoicePrinter->setTime($billingTime);   //Billing Time

        # Seller Information
        $aSellerNameInfo = [];
        if ($sellerCompanyName = \WilokeThemeOptions::getOptionDetail('invoice_seller_company_name')) {
            $aSellerNameInfo[] = iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $sellerCompanyName);
        }
        if ($sellerCompanyAddress = \WilokeThemeOptions::getOptionDetail('invoice_seller_company_address')) {
            $aSellerNameInfo[] = $sellerCompanyAddress;
        }
        if ($sellerCompanyCityAndCountry = \WilokeThemeOptions::getOptionDetail('invoice_seller_company_city_country')) {
            $aSellerNameInfo[] = $sellerCompanyCityAndCountry;
        }
        $oInvoicePrinter->setFrom(apply_filters('wilcity/wiloke-listing-tools/filter/invoince/seller-info',
            $aSellerNameInfo));

        # Purchaser Information
        $paymentID = InvoiceModel::getField('paymentID', $aData['invoiceID']);
        $userID    = PaymentModel::getField('userID', $paymentID);

        $aPurchaserInfo = [];
        $oPurchaserInfo = get_userdata($userID);

        if (!empty($oPurchaserInfo->billing_company)) {
            $aPurchaserInfo[] = $oPurchaserInfo->billing_company;
        } else {
            $aPurchaserInfo[] = $oPurchaserInfo->first_name.' '.$oPurchaserInfo->last_name;
        }

        $address = $oPurchaserInfo->billing_address_1;
        if (empty($oPurchaserInfo->billing_address_1)) {
            $address = User::getAddress($userID);
        }

        if (!empty($address)) {
            $aPurchaserInfo[] = $address;
        }

        if (!empty($oPurchaserInfo->billing_city)) {
            $aPurchaserInfo[] = $oPurchaserInfo->billing_city.' '.$oPurchaserInfo->billing_country;
        } else if (!empty($oPurchaserInfo->billing_country)) {
            $aPurchaserInfo[] = $oPurchaserInfo->billing_country;
        }

        $oInvoicePrinter->setTo(apply_filters('wilcity/wiloke-listing-tools/filter/invoince/purchaser-info',
            $aPurchaserInfo, $userID));

        $planName = get_the_title($planID);
        if (empty($planName)) {
            $planName = PaymentMetaModel::get($aInvoice['paymentID'], 'planName');
        }
        $planName = utf8_decode($planName);

        $oInvoicePrinter->addItem($planName,
            GetWilokeSubmission::getPackageType(PaymentModel::getField('packageType', $aInvoice['paymentID'])), 1, 0,
            $aInvoice['total'], $aInvoice['discount'], $aInvoice['total']);

        $oInvoicePrinter->addTotal(esc_html__('Sub Total', 'wiloke-listing-tools'), $aInvoice['subTotal']);
        $oInvoicePrinter->addTotal(esc_html__('Discount', 'wiloke-listing-tools'), $aInvoice['discount']);
        $oInvoicePrinter->addTotal(esc_html__('Total', 'wiloke-listing-tools'), $aInvoice['total'], true);

        if ($badge = \WilokeThemeOptions::getOptionDetail('invoice_badge')) {
            $oInvoicePrinter->addBadge($badge);
        }

        if ($invoiceTitle = \WilokeThemeOptions::getOptionDetail('invoice_notice_title')) {
            $oInvoicePrinter->addTitle($invoiceTitle);
        }

        if ($invoiceDesc = \WilokeThemeOptions::getOptionDetail('invoice_notice_description')) {
            $oInvoicePrinter->addParagraph($invoiceDesc);
        }

        if (!empty($sellerCompanyName)) {
            $oInvoicePrinter->setFooternote(html_entity_decode($sellerCompanyName));
        }

        $fileName = \WilokeThemeOptions::getOptionDetail('invoice_download_file_name');
        if (!empty($fileName)) {
            $fileName = str_replace(['%invoiceID%', '%invoiceDate%'],
                    [$aInvoice['ID'], date('m-d-y', strtotime($aInvoice['created_at']))], $fileName).'.pdf';
        } else {
            $fileName = 'INV-'.date('m-d-y', strtotime($aInvoice['created_at'])).'.pdf';
        }

        if ($outputType == 'F') {
            $userPath = FileSystem::getUserFolderDir(PaymentModel::getField('userID', $aInvoice['paymentID']));
            $fileDir  = trailingslashit($userPath).$fileName;
            try {
                $oInvoicePrinter->render($fileDir, $outputType);

                return FileSystem::getUserFolderUrl(PaymentModel::getField('userID', $aInvoice['paymentID'])).$fileName;
            } catch (\Exception $exception) {
                return '';
            }
        }

        try {
            $oInvoicePrinter->render($fileName, $outputType);
        } catch (\Exception $exception) {
            return false;
        }
    }

    /*
     * Generating a invoice, and save it to download file path. It's useful for Gmail Attachment
     *
     * @since 1.0
     */
    public function generateInvoiceDownloadFilePath($path, $aInvoiceInfo)
    {
        return $this->generateInvoice($aInvoiceInfo, 'F');
    }

    /*
     * New Invoice (Make your site more prefessional
     *
     * @since 1.2.0
     */
    public function downloadInvoice()
    {
        if (!isset($_POST['invoiceID']) || empty($_POST['invoiceID'])) {
            header('HTTP/1.0 403 Forbidden');
        }

        $status = $this->generateInvoice($_POST);
        if (!$status) {
            header('HTTP/1.0 403 Forbidden');
        }
    }

    protected function insertInvoice($paymentID, $aData)
    {
        $token     = '';
        $invoiceID = '';

        if (isset($aData['token']) && !empty($aData['token'])) {
            $token = $aData['token'];
            unset($aData['token']);
            $invoiceID = InvoiceMetaModel::getInvoiceIDByToken($token);
        }

        if (!empty($invoiceID)) {
            return false;
        }

        $invoiceID = InvoiceModel::set(
            $paymentID,
            $aData
        );

        if ($invoiceID) {
            if (!empty($token)) {
                InvoiceMetaModel::setInvoiceToken($invoiceID, $token);
            }

            do_action('wilcity/inserted-invoice', [
                'paymentID' => $paymentID,
                'total'     => $aData['total'],
                'subTotal'  => $aData['subTotal'],
                'tax'       => $aData['tax'],
                'currency'  => $aData['currency'],
                'discount'  => $aData['discount'],
                'invoiceID' => $invoiceID
            ]);
        }
    }

    public function prepareInsertInvoice($paymentID, $aData)
    {
        $this->insertInvoice($paymentID, $aData);
    }

    public function deleteAllInvoices()
    {
        $this->middleware('[isAdministrator]');
        InvoiceModel::deleteAll();

        wp_send_json_success();
    }

    /**
     *
     */
    private function insertWilokeSubmissionInvoiceAfterWooCommerceOrderCreated($aData)
    {
        $oOrder = new \WC_Order($aData['orderID']);

        $aItems = $oOrder->get_items();

        $packageType = PaymentModel::getPackageTypeByOrderID($aData['orderID']);
        if ($packageType == 'promotion') {
            $aPaymentIDs = PaymentModel::getPaymentIDsByWooOrderID($aData['orderID']);
            $order       = 0;
            foreach ($aItems as $aItem) {
                $paymentID = $aPaymentIDs[$order]['ID'];
                $invoiceID = InvoiceModel::getInvoiceIDByPaymentID($paymentID);

                if (empty($invoiceID)) {
                    InvoiceModel::set(
                        $paymentID,
                        [
                            'currency' => $oOrder->get_currency(),
                            'subTotal' => $aItem['subtotal'],
                            'discount' => floatval($aItem['subtotal']) - floatval($aItem['total']),
                            'tax'      => $aItem['total_tax'],
                            'total'    => $aItem['total']
                        ]
                    );
                }
                $order++;
            }
        } else {
            foreach ($aItems as $aItem) {
                $productID = $aItem['product_id'];
                $planID    = PlanRelationshipModel::getPlanIDByProductID($productID);
                //$payment = PlanRelationshipModel::getPlanIDByProductID($productID);

                if (!empty($planID)) {
                    $paymentID = PaymentModel::getPaymentIDByOrderIDAndPlanID($aData['orderID'], $planID);
                    $invoiceID = InvoiceModel::getInvoiceIDByPaymentID($paymentID);

                    if (empty($invoiceID)) {
                        InvoiceModel::set(
                            $paymentID,
                            [
                                'currency' => $oOrder->get_currency(),
                                'subTotal' => $aItem['subtotal'],
                                'discount' => floatval($aItem['subtotal']) - floatval($aItem['total']),
                                'tax'      => $aItem['total_tax'],
                                'total'    => $aItem['total']
                            ]
                        );
                    }
                }
            }
        }
    }

    /**
     * Inserting Invoice after WooCommerce Subscription Order is created
     */
    public function insertNewInvoiceAfterPaymentViaWooCommerceSubscriptionRenewed(\WC_Subscription $that)
    {
        $orderID = $that->get_parent_id();
        $this->insertWilokeSubmissionInvoiceAfterWooCommerceOrderCreated([
            'orderID' => $orderID
        ]);
    }

    /*
     * Inserting Invoice after payment has been completed
     * It's for NonRecurring Payment only
     *
     * @since 1.0
     */
    public function insertNewInvoiceAfterPayViaWooCommerceSucceeded($aData)
    {
        if (WooCommerce::isSubscription($aData['orderID'])) {
            return false;
        }

        $this->insertWilokeSubmissionInvoiceAfterWooCommerceOrderCreated($aData);
    }

    /*
     * For Direct Bank Transfer Only
     */
    public function update($aInfo)
    {
        if ($aInfo['newStatus'] != 'active' && $aInfo['newStatus'] != 'succeeded' && $aInfo['gateway'] != 'banktransfer') {
            return false;
        }

        $aTransactionInfo = PaymentMetaModel::get($aInfo['paymentID'],
            wilokeListingToolsRepository()->get('payment:paymentInfo'));

        if (GetWilokeSubmission::isNonRecurringPayment($aInfo['billingType']) || (!GetWilokeSubmission::isNonRecurringPayment($aInfo['billingType']) && $aInfo['newStatus'] == 'active' && $aInfo['oldStatus'] == 'processing')) {
            InvoiceModel::set(
                $aInfo['paymentID'],
                [
                    'currency' => $aTransactionInfo['currency'],
                    'subTotal' => $aTransactionInfo['subTotal'],
                    'discount' => $aTransactionInfo['discount'],
                    'tax'      => $aTransactionInfo['tax'],
                    'total'    => $aTransactionInfo['total']
                ]
            );
        }
    }
}
