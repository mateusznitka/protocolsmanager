<?php

include ('../../../inc/includes.php');
require_once dirname(__DIR__) . '/inc/SignProtocolByEmail.php';
$dataFromURL = $_GET['p'];
$sp = new SignProtocolByEmail();
$result = $sp->updateDataFromEmailLink($dataFromURL);
Html::header('Self-service');
echo '<div style="text-align: center; width: 100%; color: '.$result['message_color'].'">
        <h1>'.$result['message'].'</h1>
        </div>';

//testy
//$protocol = isset($_SERVER["HTTPS"]) ? 'https://' : 'http://';
//$link = $protocol.$_SERVER['HTTP_HOST'].$CFG_GLPI["root_doc"]."/plugins/protocolsmanager/front/SignProtocolByEmail.php?p=jdshakjldfhasklhfishfriuoahfiasohfdskafdnkljsa";
//$email_content = '<div style="width:100%;text-align: center;margin-top:15px; margin-bottom: 15px;" >
//                <p>'.__('You can sign protocol by this button with out login to application!').'</p>
//                <a href="'.$link.'"><button style="padding: 15px;background-color: lightskyblue; font-size: 22px;border-radius: 6%; border-color: white;">
//                    '.__('Sign protocol').'
//                    </button>
//                </a>
//            </div>';
//echo $email_content;


//$email = 'test@test.pl';
//$protocolID = 11;
//$userID = 2;


//$sp = new SignProtocolByEmail();


//$sp = new SignProtocolByEmail();
//$sp->setEmail($email);
//$sp->setProtocolsID($protocolID);
//$sp->setUserID($userID);
//$test = $sp->createPathToSignProtocol();


