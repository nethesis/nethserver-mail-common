<?php

echo $view->header()->setAttribute('template', $T('Opendkim_Configure_header'));


echo $view->fieldsetSwitch('status', 'enabled', $view::FIELDSETSWITCH_EXPANDABLE | $view::FIELDSETSWITCH_CHECKBOX)
    ->setAttribute('uncheckedValue', 'disabled')

    ->insert($view->radioButton('openDkimNoRestrictedHosts', 'enabled'))
    ->insert($view->fieldsetSwitch('openDkimNoRestrictedHosts', 'disabled', $view::FIELDSETSWITCH_EXPANDABLE)
    ->insert($view->textArea('openDkimRestrictedIpList', $view::LABEL_ABOVE)->setAttribute('dimensions', '10x30')))
;

echo $view->buttonList($view::BUTTON_SUBMIT | $view::BUTTON_HELP | $view::BUTTON_CANCEL);
