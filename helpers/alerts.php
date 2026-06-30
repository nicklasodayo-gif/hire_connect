<?php

function flash($type,$message)
{

    $_SESSION['flash'] = [

        "type"=>$type,

        "message"=>$message

    ];

}