
<!-- Here you can add your html code. This code will be applied at the end of the <body> area
     and after the Admidio module code.
-->

<?php

include(SERVER_PATH."/adm_plugins/appmidio/appmidio.php");

// link to module overall view
if(strpos($_SERVER['REQUEST_URI'], 'index.php') === false)
{
    echo '<div style="text-align: center; margin-top: 5px;">
        <a href="'.$g_root_path.'/adm_program/index.php">'.$gL10n->get('SYS_BACK_TO_MODULE_OVERVIEW').'</a>
    </div>';
}
?>

<div style="text-align: center; margin: 15px;">
    <span style="font-size: 9pt; vertical-align: bottom;">&nbsp;&nbsp;&copy; 2019&nbsp;&nbsp;</span>
</div>

