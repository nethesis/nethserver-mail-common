<?php
namespace NethServer\Module\Mail\Domain;

/*
 * Copyright (C) 2017 Nethesis S.r.l.
 *
 * This script is part of NethServer.
 *
 * NethServer is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * NethServer is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with NethServer.  If not, see <http://www.gnu.org/licenses/>.
 */

use Nethgui\System\PlatformInterface as Validate;

/**
 * Configure OpenDkim
 *
 * @author stephane de Labrusse <stephdl@de-labrusse.fr>
 * @since 1.0
 */
class OpenDkim extends \Nethgui\Controller\Table\AbstractAction
{

    public function initialize()
    {
        $this->declareParameter('status', Validate::SERVICESTATUS,  array('configuration', 'opendkim', 'status'));
        $this->declareParameter('openDkimNoRestrictedHosts', Validate::SERVICESTATUS,  array('configuration', 'opendkim', 'openDkimNoRestrictedHosts'));
        $this->declareParameter('openDkimRestrictedIpList', Validate::ANYTHING,  array('configuration', 'opendkim', 'openDkimRestrictedIpList'));
    }

    protected function onParametersSaved($changedParameters)
    {
        $this->getPlatform()->signalEvent('nethserver-mail-common-save');
    }

    public static function splitLines($text)
    {
        return array_filter(preg_split("/[,;\s]+/", $text));
    }

    public function readopenDkimRestrictedIPList($dbList)
    {
        return implode("\r\n", explode(',' ,$dbList));
    }

    public function writeopenDkimRestrictedIpList($viewText)
    {
        return array(implode(',', self::splitLines($viewText)));
    }


    public function validate(\Nethgui\Controller\ValidationReportInterface $report)
    {
        $itemValidator = $this->createValidator()->orValidator($this->createValidator(Validate::IP),
            $this->createValidator(Validate::CIDR_BLOCK));

        foreach (self::splitLines($this->parameters['openDkimRestrictedIpList']) as $v) {
            if ( ! $itemValidator->evaluate($v)) {
                $report->addValidationErrorMessage($this, 'openDkimRestrictedIpList', 'Not an IP', array($v));
                break;
            }
        }
    }



}
