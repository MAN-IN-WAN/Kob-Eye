<?php
class Registration extends genericClass {
    function exportCsv(){
        $cols = array(
            "FirstName::FirstName::varchar",
            "LastName::LastName::varchar",
            "Email::Email::varchar",
            "Address::Address::varchar",
            "City::City::varchar",
            "Country::Country::varchar",
            "Product::Product::varchar",
            "ProductSerial::ProductSerial::varchar",
            "Size::Size::varchar",
            "Shop::Shop::varchar",
            "DatePurchased::DatePurchased::varchar",
        );
        header("Content-type: application/vnd.ms-excel; charset=".CHARSET_CODE."");
        header("Content-disposition: attachment; filename=\"registration_list.csv\"");
        return parent::exportCsv($cols,'Products','Registration','ASC','Id','registration_list.csv','csv');
    }
    function checkCaptcha() {
        require_once 'Class/Lib/ReCaptcha/autoload.php';
        $secret ='6LeSQQoTAAAAACEDjM8tk09mvfq8tfGrhhhH_iGF'; 
        $recaptcha = new \ReCaptcha\ReCaptcha($secret);
        $resp = $recaptcha->verify($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);
        return $resp->isSuccess();
    }
}