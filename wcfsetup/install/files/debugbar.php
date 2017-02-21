<?php
/**
 * @author	Tim Duesterhus
 * @copyright	2001-2017 WoltLab GmbH
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	WoltLabSuite\Core
 */
require_once('./global.php');
$openHandler = new \DebugBar\OpenHandler(\wcf\system\WCF::getDebugBar());
$openHandler->handle();
exit;
