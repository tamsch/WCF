<?php
namespace wcf\data\package;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\exception\SystemException;
use wcf\system\io\RemoteFile;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\util\HTTPRequest;
use wcf\util\JSON;

/**
 * Executes package-related actions.
 * 
 * @author	Alexander Ebert
 * @copyright	2001-2019 WoltLab GmbH
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	WoltLabSuite\Core\Data\Package
 * 
 * @method	Package			create()
 * @method	PackageEditor[]		getObjects()
 * @method	PackageEditor		getSingleObject()
 */
class PackageAction extends AbstractDatabaseObjectAction {
	/**
	 * @inheritDoc
	 */
	protected $className = PackageEditor::class;
	
	/**
	 * @inheritDoc
	 */
	protected $permissionsCreate = ['admin.configuration.package.canInstallPackage'];
	
	/**
	 * @inheritDoc
	 */
	protected $permissionsDelete = ['admin.configuration.package.canUninstallPackage'];
	
	/**
	 * @inheritDoc
	 */
	protected $permissionsUpdate = ['admin.configuration.package.canUpdatePackage'];
	
	/**
	 * @inheritDoc
	 */
	protected $requireACP = ['searchForPurchasedItems'];
	
	/**
	 * Validates parameters to search for purchased items in the WoltLab Plugin-Store.
	 */
	public function validateSearchForPurchasedItems() {
		WCF::getSession()->checkPermissions(['admin.configuration.package.canInstallPackage', 'admin.configuration.package.canUpdatePackage']);
		
		$this->readString('authCode', true);
		
		if (empty($this->parameters['authCode']) && PACKAGE_SERVER_AUTH_CODE) {
			$this->parameters['authCode'] = PACKAGE_SERVER_AUTH_CODE;
		}
	}
	
	/**
	 * Searches for purchased items in the WoltLab Plugin-Store.
	 * 
	 * @return	string[]
	 * @throws	SystemException
	 */
	public function searchForPurchasedItems() {
		if (!RemoteFile::supportsSSL()) {
			return [
				'noSSL' => WCF::getLanguage()->get('wcf.acp.pluginStore.api.noSSL')
			];
		}
		
		if (empty($this->parameters['authCode'])) {
			return [
				'template' => $this->renderAuthorizationDialog(false)
			];
		}
		
		$request = new HTTPRequest('https://api.woltlab.com/2.0/customer/purchases/list.json', [
			'method' => 'POST'
		], [
			'authCode' => $this->parameters['authCode'],
			'apiVersions' => array_merge(
				WCF::getSupportedLegacyApiVersions(),
				[WSC_API_VERSION]
			),
		]);
		
		$request->execute();
		$reply = $request->getReply();
		$response = JSON::decode($reply['body']);
		
		$code = isset($response['status']) ? $response['status'] : 500;
		switch ($code) {
			case 200:
				if (empty($response['products'])) {
					return [
						'noResults' => WCF::getLanguage()->getDynamicVariable('wcf.acp.pluginStore.purchasedItems.noResults')
					];
				}
				else {
					WCF::getSession()->register('__pluginStoreProducts', $response['products']);
					
					return [
						'redirectURL' => LinkHandler::getInstance()->getLink('PluginStorePurchasedItems')
					];
				}
			break;
			
			// authentication error
			case 401:
				return [
					'template' => $this->renderAuthorizationDialog(true, $this->parameters['authCode'])
				];
			break;
			
			// any other kind of errors
			default:
				throw new SystemException(WCF::getLanguage()->getDynamicVariable('wcf.acp.pluginStore.api.error', ['status' => $code]));
			break;
		}
	}
	
	/**
	 * Renders the authentication dialog.
	 * 
	 * @param	boolean		$rejected
	 * @param string $authCode
	 * @return	string
	 */
	protected function renderAuthorizationDialog($rejected, $authCode = '') {
		WCF::getTPL()->assign([
			'authCode' => $authCode ?: PACKAGE_SERVER_AUTH_CODE,
			'rejected' => $rejected
		]);
		
		return WCF::getTPL()->fetch('pluginStoreAuthorization');
	}
}
