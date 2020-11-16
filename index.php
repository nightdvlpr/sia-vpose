<?php

namespace SIA;

//header("Content-Type: text/xml; charset=UTF-8; encoding=ISO-8859-1");
//echo (new SIA)->XMLgenerator();die;


class SIA
{
    private $_user_id = "AF06784854";
    private $_shop_id = "336040303200045"; // or merchant ID
    private $_start_key = "uhv-rNHraCM9G-U7QBsAcUNQF-hS6ttD-XLj5ryp-YKtMfZ-hqE9Z3abtp-4YVWdZTCQbkyXU9--VcausPTZvZ9xmSyV7D8GjV8C";
    private $_outcome_key = "--5CJbGayb6emFnUnE-DK-77zQ-m-c5vbyndK83PyN2LYJtXv-t-v-76EE-ExSm-RAq-AAS-mFn-mkwr47-BeeVfAtn6-7Lz52xv";
    private $_pan = "5335731215317145";
    private $_mac, $_timestamp = null;
    public $default_data = [
        "Operation" => "AUTHORIZATION",
        "Timestamp" => "2015-02-08T12:02:00.000", //$this->_timestamp,
        "ShopID" => "000000000000003",//$this->_shop_id,
        "OrderID" => "100",
        "OperatorID" => "oper0001",
        "ReqRefNum" => "20150501901234567890123452289000",
        "Pan" => "9998500000000015", // $this->_pan card number
        "ExpDate" => "0409",
        "Amount" => "1",
        "Currency" => "978",
        "AccountingMode" => "I",
        "Network" => "01",
        "Release" => "02",
        "CVV2" => "249",
        "Exponent" => "2",
        "MAC" => "115025d5a5b65df687790867bdece136"//$this->_mac
    ];
    public $data = [];

    function __construct()
    {
        $this->_timestamp = date('Y-m-g') . 'T' . date('H:i:s.u');
        $this->data = [
            "Operation" => "AUTHORIZATION",
            "Timestamp" => $this->_timestamp, // default: 2015-02-08T12:02:00.000
            "ShopID" => "000000000000003",//$this->_shop_id,
            "OrderID" => "100",
            "OperatorID" => "oper0001",
            "ReqRefNum" => "20150501901234567890123452289000",
            "Pan" => "9998500000000015", // $this->_pan card number
            "ExpDate" => "0409",
            "Amount" => "1",
            "Currency" => "978",
            "AccountingMode" => "I",
            "Network" => "01",
            "Release" => "02",
            "CVV2" => "249",
            "Exponent" => "2",
            "MAC" => null
        ];

        $mac = md5("OPERATION=" . $this->data["Operation"] . "&TIMESTAMP=" . $this->data["Timestamp"] . "&SHOPID=" . $this->data["ShopID"] . "&ORDERID=" . $this->data["OrderID"] . "&OPERATORID=" . $this->data["OperatorID"] . "&REQREFNUM=" . $this->data["ReqRefNum"] . "&PAN=" . $this->data["Pan"] . "&CVV2=" . $this->data["CVV2"] . "&EXPDATE=" . $this->data["ExpDate"] . "&AMOUNT=" . $this->data["Amount"] . "&CURRENCY=" . $this->data["Currency"] . "&EXPONENT=" . $this->data["Exponent"] . "&ACCOUNTINGMODE=" . $this->data["AccountingMode"] . "&NETWORK=" . $this->data["Network"] .
            "&$this->_start_key");
        $this->_mac = md5($mac);

        $this->data["MAC"] = ($this->_mac);

        // $this->data = $this->default_data; // Set to defaut
    }

    public function reqAuthentication()
    {
        header("Content-Type: text/xml; charset=UTF-8; encoding=ISO-8859-1");
        echo $this->curl($this->XMLgenerator());
    }

    private function curl($data)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://virtualpostest.sia.eu/vpos/apibo/apiBOXML.app",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "data=" . urlencode($data),

            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/x-www-form-urlencoded"
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        return $response;
    }

    // data generator based on XML
    public function XMLgenerator()
    {
        $content = "<?xml version='1.0' encoding='ISO-8859-1'?>
<BPWXmlRequest>
    <Release>" . $this->data['Release'] . "</Release>
    <Request>
        <Operation>" . $this->data['Operation'] . "</Operation>
        <Timestamp>" . $this->data['Timestamp'] . "</Timestamp>
        <MAC>" . $this->data['MAC'] . "</MAC>
    </Request>
    <Data>
        <AuthorizationRequest>
            <Header>
                <ShopID>" . $this->data['ShopID'] . "</ShopID>
                <OperatorID>" . $this->data['OperatorID'] . "</OperatorID>
                <ReqRefNum>" . $this->data['ReqRefNum'] . "</ReqRefNum>
            </Header>
            <OrderID>" . $this->data['OrderID'] . "</OrderID>
            <Pan>" . $this->data['Pan'] . "</Pan>
            <CVV2>" . $this->data['CVV2'] . "</CVV2>
            <ExpDate>" . $this->data['ExpDate'] . "</ExpDate>
            <Amount>" . $this->data['Amount'] . "</Amount>
            <Currency>" . $this->data['Currency'] . "</Currency>
            <Exponent>" . $this->data['Exponent'] . "</Exponent>
            <AccountingMode>" . $this->data['AccountingMode'] . "</AccountingMode>
            <Network>" . $this->data['Network'] . "</Network>
        </AuthorizationRequest>
    </Data>
</BPWXmlRequest>";
        return $content;
    }


    function redirectVPOS()
    {
        $data = [
            "PAGE" => "LAND",
            "AMOUNT" => "11000",
            "CURRENCY" => "978",
            "ORDERID" => "10001040",
            "SHOPID" => $this->_shop_id,
            "URLBACK" => "https://www.nilaz.biz/payment-gateway/cancelation?IdShop=" . $this->_shop_id,
            "URLDONE" => "https://www.nilaz.biz/payment-gateway/success?IdShop=" . $this->_shop_id,
            "URLMS" => "https://www.nilaz.biz/payment-gateway/ms?IdShop=" . $this->_shop_id,
            "ACCOUNTINGMODE" => "I",
            "AUTHORMODE" => "I",
            "LANG" => "EN"
        ];

        $_3dsdata = [
            "threeDSRequestorChallengeInd" => "03",
            "addrMatch" => "N",
            "chAccAgeInd" => "01",
            "billAddrCity" => "FIRENZE",
            "billAddrCountry" => "ITALY",
            "billAddrLine1" => "VIA UNIONE SOVIETICA 37",
            "billAddrPostCode" => "50125", // postal code
            "billAddrState" => "FIRENZE"//state or province
        ];

        $mac_string = "URLMS=" . $data['URLMS'] .
            "&URLDONE=" . $data['URLDONE'] .
            "&ORDERID=" . $data['ORDERID'] .
            "&SHOPID=" . $data['SHOPID'] .
            "&AMOUNT=" . $data['AMOUNT'] .
            "&CURRENCY=" . $data['CURRENCY'] .
            "&ACCOUNTINGMODE=" . $data['ACCOUNTINGMODE'] .
            //"&3DSDATA=" . $this->threeDsEncrypted(json_encode($_3dsdata), $this->_start_key) .
            "&AUTHORMODE=" . $data['AUTHORMODE'] . "&$this->_start_key";

        // echo $mac_string;die;

        $mac = hash("MD5", $mac_string);

        $postField = "PAGE=" . $data['PAGE'] .
            "&AMOUNT=" . $data['AMOUNT'] .
            "&CURRENCY=" . $data['CURRENCY'] .
            "&ORDERID=" . $data['ORDERID'] .
            "&SHOPID=" . $data['SHOPID'] .
            "&URLBACK=" . $data['URLBACK'] .
            "&URLDONE=" . $data['URLDONE'] .
            "&URLMS=" . $data['URLMS'] .
            "&ACCOUNTINGMODE=" . $data['ACCOUNTINGMODE'] .
            "&AUTHORMODE=" . $data['AUTHORMODE'] .
            "&LANG=" . $data['LANG'] .
            "&EMAIL=nightdvlpr@gmail.com" .
            //"&3DSDATA=" . $this->threeDsEncrypted(json_encode($_3dsdata), $this->_start_key) .
            "&MAC=$mac";
        // echo "https://atpos.ssb.it/atpos/pagamenti/main?$postField";die;

        // CURL start
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://atpos.ssb.it/atpos/pagamenti/main?$postField", // localhost/untitled/verify.php
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_NONE,
            CURLOPT_CUSTOMREQUEST => "POST"
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        echo $response;
    }

    function threeDsEncrypted($str, $key)
    {
        $string = $str;
        $key = $key;

        $iv = base64_encode(openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-128-cbc')));

        $encodedEncryptedData = base64_encode(openssl_encrypt($string, "AES-128-CBC", $key, OPENSSL_RAW_DATA, base64_decode($iv)));
        return $encodedEncryptedData;
        // $decryptedData = openssl_decrypt(base64_decode($encodedEncryptedData), "AES-128-CBC", $key, OPENSSL_RAW_DATA, base64_decode($iv));
    }
}

echo (new SIA)->redirectVPOS();
//(new SIA)->reqAuthentication();


