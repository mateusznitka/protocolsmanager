<?php

class Buttons
{
    public function createSignProtocolButton($CFG_GLPI,$setMessage = 'send')
    {
        $protocol_for_url = isset($_SERVER["HTTPS"]) ? 'https://' : 'http://';
        $link = $protocol_for_url.$_SERVER['HTTP_HOST'].$CFG_GLPI["root_doc"]."/plugins/protocolsmanager/front/protocols.form.php";
        $message = ($setMessage == 'send') ? __('Go to sign protocol by this button.','protocolsmanager') : __('or sign protocol by this button.','protocolsmanager');
        return '<div style="width:100%;text-align: center;margin-top:15px; margin-bottom: 15px;" >
                <p>'.$message.'</p>
                <a href="'.$link.'"><button style="padding: 15px;background-color: lightskyblue; font-size: 22px;border-radius: 6%; border-color: white;">
                    '.__('Sign protocol','protocolsmanager').'
                    </button>
                </a>
            </div>';

    }
}