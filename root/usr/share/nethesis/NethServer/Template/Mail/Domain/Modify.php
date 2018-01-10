<?php


/* @var $view \Nethgui\Renderer\WidgetFactoryInterface */
$view->requireFlag($view::INSET_FORM);

if ($view->getModule()->getIdentifier() == 'update') {
    $headerText = $T('Update domain `${0}`');
    $messagesText = $T('Messages to domain ${0}');
    $keyStyles = $view::STATE_READONLY;
    $dkim = $view->fieldsetSwitch('OpenDkimStatus', 'enabled', $view::FIELDSETSWITCH_EXPANDABLE | $view::FIELDSETSWITCH_CHECKBOX)
    ->setAttribute('uncheckedValue', 'disabled')
    ->insert($view->textArea('DkimKey', $view::LABEL_ABOVE|$view::STATE_READONLY)->setAttribute('dimensions', '10x80'));
} else {
    $headerText = $T('Create a new domain');
    $messagesText = $T('Messages to this domain');
    $keyStyles = 0;
    $dkim = $view->fieldsetSwitch('OpenDkimStatus', 'enabled', $view::FIELDSETSWITCH_EXPANDABLE | $view::FIELDSETSWITCH_CHECKBOX)
    ->setAttribute('uncheckedValue', 'disabled')
    ->insert($view->literal($T('DkimKeyNotCreated_label')));
}

echo $view->header('domain')->setAttribute('template', $headerText);

echo $view->textInput('domain', $keyStyles);
echo $view->textInput('Description');

$transportPanel = $view->fieldset('domain')
    ->setAttribute('template', $messagesText)
;

foreach ($view['PlugTransport'] as $pluginView) {
    $value = $pluginView->getModule()->getIdentifier();
    $transportPanel->insert(
        $view->fieldsetSwitch('TransportType', $value, $view::FIELDSETSWITCH_EXPANDABLE)
            ->setAttribute('label', $pluginView->translate('TransportType_' . $value . '_label'))
            ->insert($view->literal($pluginView))
    );
}

$transportTypeTarget = $view->getClientEventTarget('TransportType');
$domainTarget = $view->getClientEventTarget('domain');
$jsPrimaryDomain = json_encode(explode('.', gethostname(), 2)[1]);

echo $transportPanel;

echo $view->fieldsetSwitch('DisclaimerStatus', 'enabled', $view::FIELDSETSWITCH_EXPANDABLE | $view::FIELDSETSWITCH_CHECKBOX)
    ->setAttribute('uncheckedValue', 'disabled')
    ->insert($view->textArea('DisclaimerText', $view::LABEL_NONE)->setAttribute('dimensions', '10x40'));

echo $dkim;

echo $view->buttonList($view::BUTTON_SUBMIT | $view::BUTTON_HELP | $view::BUTTON_CANCEL);

$view->includeJavascript("
(function ( \$ ) {
    \$('.${domainTarget}').on('nethguiupdateview', function(ev, domain) {
        console.log(domain);
        \$('.${transportTypeTarget}[value=Relay]').trigger(domain == $jsPrimaryDomain ? 'nethguidisable' : 'nethguienable');
    });
} ( jQuery ));
");
