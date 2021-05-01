<?php
/**
saleh sharifi
 */

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}
use WHMCS\Database\Capsule;

require_once __DIR__.'/lib/autoload.php';
//$apiKey = '{InsertApiTokenHere}';

// Require any libraries needed for the module to function.
// require_once __DIR__ . '/path/to/library/loader.php';
//
// Also, perform any initialization required by the service's library.

/**
 * Define module related meta data.
 *
 * Values returned here are used to determine module related abilities and
 * settings.
 *
 * @see https://developers.whmcs.com/provisioning-modules/meta-data-params/
 *
 * @return array
 */
function hetzner_MetaData()
{
    return array(
        'DisplayName' => 'Hetzner Cloud by saleh sharifi',
        'APIVersion' => '1.1', // Use API Version 1.1
        'RequiresServer' => true, // Set true if module requires a server to work
        'DefaultNonSSLPort' => '1111', // Default Non-SSL Connection Port
        'DefaultSSLPort' => '1112', // Default SSL Connection Port
//        'ServiceSingleSignOnLabel' => 'Login to Panel as User',
//        'AdminSingleSignOnLabel' => 'Login to Panel as Admin',
    );
}

/**
 * Define product configuration options.
 *
 * The values you return here define the configuration options that are
 * presented to a user when configuring a product for use with the module. These
 * values are then made available in all module function calls with the key name
 * configoptionX - with X being the index number of the field from 1 to 24.
 *
 * You can specify up to 24 parameters, with field types:
 * * text
 * * password
 * * yesno
 * * dropdown
 * * radio
 * * textarea
 *
 * Examples of each and their possible configuration parameters are provided in
 * this sample function.
 *
 * @see https://developers.whmcs.com/provisioning-modules/config-options/
 *
 * @return array
 */
function hetzner_ConfigOptions()
{
    global $_LANG;

    $create_tables = hetzner_createTables();
    if(isset($create_tables['error'])) {
        return array(
            sprintf(
                '<font color="red"><b>%s</b></font>',
                $create_tables['error']
            ) => array()
        );
    }
    return array(
        // a text field type allows for single line text input
        // 'Text Field' => array(
            // 'Type' => 'text',
            // 'Size' => '25',
            // 'Default' => '1024',
            // 'Description' => 'Enter in megabytes',
        // ),
        // a password field type allows for masked text input
        // 'Password Field' => array(
            // 'Type' => 'password',
            // 'Size' => '25',
            // 'Default' => '',
            // 'Description' => 'Enter secret value here',
        // ),
        // the yesno field type displays a single checkbox option
         // 'auto' => array(
            // 'Type' => 'yesno',
            // 'Description' => 'Tick to enable',
        // ),
        // the dropdown field type renders a select menu of options
        'server_type' => array(
            'Type' => 'dropdown',
            'Options' => array(
                'CX11' => 'CX11',
                'CPX11' => 'CPX11',
                'CX21' => 'CX21',
                'CPX21' => 'CPX21',
                'CX31' => 'CX31',
                'CPX31' => 'CPX31',
                'CX41' => 'CX41',
                'CPX41' => 'CPX41',
                'CX51' => 'CX51',
                'CPX51' => 'CPX51',
            ),
            'Description' => 'Choose one',
        ),
        // the radio field type displays a series of radio button options
        // 'Radio Field' => array(
            // 'Type' => 'radio',
            // 'Options' => 'First Option,Second Option,Third Option',
            // 'Description' => 'Choose your option!',
        // ),
        // the textarea field type allows for multi-line text input
        // 'Textarea Field' => array(
            // 'Type' => 'textarea',
            // 'Rows' => '3',
            // 'Cols' => '60',
            // 'Description' => 'Freeform multi-line text input field',
        // ),
    );
}

/**
 * Provision a new instance of a product/service.
 *
 * Attempt to provision a new instance of a given product/service. This is
 * called any time provisioning is requested inside of WHMCS. Depending upon the
 * configuration, this can be any of:
 * * When a new order is placed
 * * When an invoice for a new order is paid
 * * Upon manual request by an admin user
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
 *
 * @return string "success" or an error message
 */
function hetzner_CreateAccount(array $params)
{
    try {
        // Call the service's provisioning function, using the values provided
        // by WHMCS in `$params`.
        //
        // A sample `$params` array may be defined as:
        //
        // ```
        // array(
        //     'domain' => 'The domain of the service to provision',
        //     'username' => 'The username to access the new service',
        //     'password' => 'The password to access the new service',
        //     'configoption1' => 'The amount of disk space to provision',
        //     'configoption2' => 'The new services secret key',
        //     'configoption3' => 'Whether or not to enable FTP',
        //     ...
        // )
        // ```
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'hetzner',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();
    }

    return 'success';
}
function hetzner_createTables() {
    $pdo = Capsule::connection()->getPdo();
    $pdo->query("CREATE TABLE IF NOT EXISTS `mod_hetzner_cloud` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `hostingid` INT(11) NOT NULL,
        `instanceid` INT(11) NOT NULL,
        PRIMARY KEY (`id`), UNIQUE (`hostingid`)) ENGINE = InnoDB;");

    if ($pdo) {
        return true;
    } else {
        return array(
            'error' => sprintf( $_LANG['ccone']['table_create_error'], 'mod_hetzner_cloud' )
        );
    }
}
/**
 * Suspend an instance of a product/service.
 *
 * Called when a suspension is requested. This is invoked automatically by WHMCS
 * when a product becomes overdue on payment or can be called manually by admin
 * user.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
 *
 * @return string "success" or an error message
 */
function hetzner_SuspendAccount(array $params)
{
    try {
        // Call the service's suspend function, using the values provided by
        // WHMCS in `$params`.
        $apiKey=$params['serveraccesshash'];
        $hetznerClient = new \LKDev\HetznerCloud\HetznerAPIClient($apiKey);

        $pdo = Capsule::connection()->getPdo();
        $q = $pdo->prepare("SELECT instanceid FROM mod_hetzner_cloud WHERE hostingid = ?");
        $q->execute(array($params['serviceid']));

        if ($q && $q->rowCount() > 0) {
            $serverId= $q->fetchObject()->instanceid;
        } else {
            $serverId= 0;
        }
        if($serverId>0) {

            $server = $hetznerClient->servers()->get($serverId);
            $action = $server->powerOff()->getResponsePart('action');
        }
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'hetzner',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();
    }

    return 'success';
}

/**
 * Un-suspend instance of a product/service.
 *
 * Called when an un-suspension is requested. This is invoked
 * automatically upon payment of an overdue invoice for a product, or
 * can be called manually by admin user.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
 *
 * @return string "success" or an error message
 */
function hetzner_UnsuspendAccount(array $params)
{
    try {
        // Call the service's unsuspend function, using the values provided by
        // WHMCS in `$params`.


        $apiKey=$params['serveraccesshash'];
        $hetznerClient = new \LKDev\HetznerCloud\HetznerAPIClient($apiKey);
        $pdo = Capsule::connection()->getPdo();
        $q = $pdo->prepare("SELECT instanceid FROM mod_hetzner_cloud WHERE hostingid = ?");
        $q->execute(array($params['serviceid']));

        if ($q && $q->rowCount() > 0) {
            $serverId= $q->fetchObject()->instanceid;
        } else {
            $serverId= 0;
        }
        if($serverId>0) {
            $server = $hetznerClient->servers()->get($serverId);
            $server->powerOn();

        }
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'hetzner',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();
    }

    return 'success';
}

/**
 * Terminate instance of a product/service.
 *
 * Called when a termination is requested. This can be invoked automatically for
 * overdue products if enabled, or requested manually by an admin user.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
 *
 * @return string "success" or an error message
 */
function hetzner_TerminateAccount(array $params)
{
    try {
        // Call the service's terminate function, using the values provided by
        // WHMCS in `$params`.

    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'hetzner',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();
    }

    return 'success';
}

/**
 * Change the password for an instance of a product/service.
 *
 * Called when a password change is requested. This can occur either due to a
 * client requesting it via the client area or an admin requesting it from the
 * admin side.
 *
 * This option is only available to client end users when the product is in an
 * active status.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
 *
 * @return string "success" or an error message
 */
function hetzner_ChangePassword(array $params)
{
    try {
        // Call the service's change password function, using the values
        // provided by WHMCS in `$params`.
        //
        // A sample `$params` array may be defined as:
        //
        // ```
        // array(
        //     'username' => 'The service username',
        //     'password' => 'The new service password',
        // )
        // ```
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'hetzner',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();
    }

    return 'success';
}

/**
 * Upgrade or downgrade an instance of a product/service.
 *
 * Called to apply any change in product assignment or parameters. It
 * is called to provision upgrade or downgrade orders, as well as being
 * able to be invoked manually by an admin user.
 *
 * This same function is called for upgrades and downgrades of both
 * products and configurable options.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
 *
 * @return string "success" or an error message
 */
function hetzner_ChangePackage(array $params)
{
    try {
        // Call the service's change password function, using the values
        // provided by WHMCS in `$params`.
        //
        // A sample `$params` array may be defined as:
        //
        // ```
        // array(
        //     'username' => 'The service username',
        //     'configoption1' => 'The new service disk space',
        //     'configoption3' => 'Whether or not to enable FTP',
        // )
        // ```
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'hetzner',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();
    }

    return 'success';
}

/**
 * Test connection with the given server parameters.
 *
 * Allows an admin user to verify that an API connection can be
 * successfully made with the given configuration parameters for a
 * server.
 *
 * When defined in a module, a Test Connection button will appear
 * alongside the Server Type dropdown when adding or editing an
 * existing server.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
 *
 * @return array
 */
function hetzner_TestConnection(array $params)
{
    try {
        // Call the service's connection test function.

        $success = true;
        $errorMsg = '';
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'hetzner',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        $success = false;
        $errorMsg = $e->getMessage();
    }

    return array(
        'success' => $success,
        'error' => $errorMsg,
    );
}

/**
 * Additional actions an admin user can invoke.
 *
 * Define additional actions that an admin user can perform for an
 * instance of a product/service.
 *
 * @see hetzner_buttonOneFunction()
 *
 * @return array
 */
function hetzner_AdminCustomButtonArray()
{
    return array(
        "Button 1 Display Value" => "buttonOneFunction",
        "Button 2 Display Value" => "buttonTwoFunction",
    );
}

/**
 * Additional actions a client user can invoke.
 *
 * Define additional actions a client user can perform for an instance of a
 * product/service.
 *
 * Any actions you define here will be automatically displayed in the available
 * list of actions within the client area.
 *
 * @return array
 */
function hetzner_ClientAreaCustomButtonArray()
{
    return array(
        "Action 1 Display Value" => "actionOneFunction",
        "Action 2 Display Value" => "actionTwoFunction",
    );
}

/**
 * Custom function for performing an additional action.
 *
 * You can define an unlimited number of custom functions in this way.
 *
 * Similar to all other module call functions, they should either return
 * 'success' or an error message to be displayed.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
 * @see hetzner_AdminCustomButtonArray()
 *
 * @return string "success" or an error message
 */
function hetzner_buttonOneFunction(array $params)
{
    try {
        // Call the service's function, using the values provided by WHMCS in
        // `$params`.
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'hetzner',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();
    }

    return 'success';
}

/**
 * Custom function for performing an additional action.
 *
 * You can define an unlimited number of custom functions in this way.
 *
 * Similar to all other module call functions, they should either return
 * 'success' or an error message to be displayed.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
 * @see hetzner_ClientAreaCustomButtonArray()
 *
 * @return string "success" or an error message
 */
function hetzner_actionOneFunction(array $params)
{
    try {
        // Call the service's function, using the values provided by WHMCS in
        // `$params`.
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'hetzner',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();
    }

    return 'success';
}

/**
 * Admin services tab additional fields.
 *
 * Define additional rows and fields to be displayed in the admin area service
 * information and management page within the clients profile.
 *
 * Supports an unlimited number of additional field labels and content of any
 * type to output.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
 * @see hetzner_AdminServicesTabFieldsSave()
 *
 * @return array
 */
function hetzner_AdminServicesTabFields(array $params)
{
    try {
        // Call the service's function, using the values provided by WHMCS in
        // `$params`.
        $pdo = Capsule::connection()->getPdo();
        $q = $pdo->prepare("SELECT instanceid FROM mod_hetzner_cloud WHERE hostingid = ?");
        $q->execute(array($params['serviceid']));

        if ($q && $q->rowCount() > 0) {
            $var1= $q->fetchObject()->instanceid;
        } else {
            $var1= '';
        }
        // Return an array based on the function's response.
        return array(
            // 'Number of Apples' => (int) $response['numApples'],
            // 'Number of Oranges' => (int) $response['numOranges'],
            // 'Last Access Date' => date("Y-m-d H:i:s", $response['lastLoginTimestamp']),
            'hetzner service id' => '<input type="hidden" name="hetzner_original_uniquefieldname" '
                . 'value="' . htmlspecialchars($response['textvalue']) . '" />'
                . '<input type="text" name="hetzner_uniqueid"'
                . 'value="' . htmlspecialchars($var1) . '" />',
        );
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'hetzner',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        // In an error condition, simply return no additional fields to display.
    }

    return array();
}

/**
 * Execute actions upon save of an instance of a product/service.
 *
 * Use to perform any required actions upon the submission of the admin area
 * product management form.
 *
 * It can also be used in conjunction with the AdminServicesTabFields function
 * to handle values submitted in any custom fields which is demonstrated here.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
 * @see hetzner_AdminServicesTabFields()
 */
function hetzner_AdminServicesTabFieldsSave(array $params)
{
    // Fetch form submission variables.
    $originalFieldValue = isset($_REQUEST['hetzner_original_uniquefieldname'])
        ? $_REQUEST['hetzner_original_uniquefieldname']
        : '';

    $newFieldValue = isset($_REQUEST['hetzner_uniqueid'])
        ? $_REQUEST['hetzner_uniqueid']
        : '';

    // Look for a change in value to avoid making unnecessary service calls.
    if ($originalFieldValue != $newFieldValue) {
        try {
            // Call the service's function, using the values provided by WHMCS
            // in `$params`.
            $pdo = Capsule::connection()->getPdo();
            $pdo->beginTransaction();

            try {
                $q = $pdo->prepare("INSERT INTO mod_hetzner_cloud
                                (hostingid, instanceid)
                            VALUES
                                (?, ?)
                            ON DUPLICATE KEY UPDATE
                                instanceid = VALUES (instanceid)");

                $q->execute(array($params['serviceid'], $newFieldValue));
                $pdo->commit();

            } catch (Exception $e) {
                $pdo->rollback();
            }

			
        } catch (Exception $e) {
            // Record the error in WHMCS's module log.
            logModuleCall(
                'hetzner',
                __FUNCTION__,
                $params,
                $e->getMessage(),
                $e->getTraceAsString()
            );

				echo $e->getMessage();
				exit();
            // Otherwise, error conditions are not supported in this operation.
        }
    }
}

/**
 * Perform single sign-on for a given instance of a product/service.
 *
 * Called when single sign-on is requested for an instance of a product/service.
 *
 * When successful, returns a URL to which the user should be redirected.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
 *
 * @return array
 */
function hetzner_ServiceSingleSignOn(array $params)
{
    try {
        // Call the service's single sign-on token retrieval function, using the
        // values provided by WHMCS in `$params`.
        $response = array();

        return array(
            'success' => true,
            'redirectTo' => $response['redirectUrl'],
        );
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'hetzner',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return array(
            'success' => false,
            'errorMsg' => $e->getMessage(),
        );
    }
}

/**
 * Perform single sign-on for a server.
 *
 * Called when single sign-on is requested for a server assigned to the module.
 *
 * This differs from ServiceSingleSignOn in that it relates to a server
 * instance within the admin area, as opposed to a single client instance of a
 * product/service.
 *
 * When successful, returns a URL to which the user should be redirected to.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
 *
 * @return array
 */
function hetzner_AdminSingleSignOn(array $params)
{
    try {
        // Call the service's single sign-on admin token retrieval function,
        // using the values provided by WHMCS in `$params`.
        $response = array();

        return array(
            'success' => true,
            'redirectTo' => $response['redirectUrl'],
        );
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'hetzner',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return array(
            'success' => false,
            'errorMsg' => $e->getMessage(),
        );
    }
}

/**
 * Client area output logic handling.
 *
 * This function is used to define module specific client area output. It should
 * return an array consisting of a template file and optional additional
 * template variables to make available to that template.
 *
 * The template file you return can be one of two types:
 *
 * * tabOverviewModuleOutputTemplate - The output of the template provided here
 *   will be displayed as part of the default product/service client area
 *   product overview page.
 *
 * * tabOverviewReplacementTemplate - Alternatively using this option allows you
 *   to entirely take control of the product/service overview page within the
 *   client area.
 *
 * Whichever option you choose, extra template variables are defined in the same
 * way. This demonstrates the use of the full replacement.
 *
 * Please Note: Using tabOverviewReplacementTemplate means you should display
 * the standard information such as pricing and billing details in your custom
 * template or they will not be visible to the end user.
 *
 * @param array $params common module parameters
 *
 * @see https://developers.whmcs.com/provisioning-modules/module-parameters/
 *
 * @return array
 */
function hetzner_ClientArea(array $params){


    $apiKey=$params['serveraccesshash'];
    $hetznerClient = new \LKDev\HetznerCloud\HetznerAPIClient($apiKey);
    $pdo = Capsule::connection()->getPdo();
    $q = $pdo->prepare("SELECT instanceid FROM mod_hetzner_cloud WHERE hostingid = ?");
    $q->execute(array($params['serviceid']));

    if ($q && $q->rowCount() > 0) {
        $serverId= $q->fetchObject()->instanceid;
    } else {
        $serverId= 0;
    }

    global $virt_action_display, $virt_errors, $virt_resp;

    $GLOBALS['virt_img_url'] = 'modules/servers/hetzner/images/';

    $code = '




<table width="550" align="center"><tr><td align="center">
	<h4 class="auto-style1">برای انجام عملیات بر روی آیکن کلیک کنید</h4>

	<table cellpadding="0" dir="rtl" cellspacing="0"><tr><td   align="center"><img src="'.$GLOBALS['virt_img_url'].'start.gif" width="80" height="65" title="Poweron VPS"  alt="Start VPS" onMouseOver="this.style.cursor=\'pointer\'" onClick="window.location=\'clientarea.php?action=productdetails&id=' . $params['serviceid'] . '&modop=custom&a=start\'" /></img><br /></td>
	<td   align="center" ><img src="'.$GLOBALS['virt_img_url'].'stop.gif" width="80" height="65" title="Reboot VPS" alt="Reboot VPS" onMouseOver="this.style.cursor=\'pointer\'" onClick="window.location=\'clientarea.php?action=productdetails&id=' . $params['serviceid'] . '&modop=custom&a=reboot\'" /></img><br /></td>
	<td   align="center" ><img src="'.$GLOBALS['virt_img_url'].'poweroff.gif" width="80" height="65" title="Power off VPS" alt="Power off VPS" onMouseOver="this.style.cursor=\'pointer\'" onClick="window.location=\'clientarea.php?action=productdetails&id=' . $params['serviceid'] . '&modop=custom&a=poweroff\'" /></img><br /></td>
			<td   align="center" ><img src="'.$GLOBALS['virt_img_url'].'vnc.gif" width="80" height="65" title="VNC console" alt="VNC console" onMouseOver="this.style.cursor=\'pointer\'" onClick="window.location=\'clientarea.php?action=productdetails&id=' . $params['serviceid'] . '&modop=custom&b=ram\'" /></img></td>
</tr>
			
</table><br /><br />
	
	
	

';
    if($_GET['a']=='reboot'){
        $text='<pre>';
        if($serverId>0) {
            $server = $hetznerClient->servers()->get($serverId);
            $text.= 'Server: '.$server->name.PHP_EOL;
            $text.= 'در حال راه اندازی مجدد هستیم:'.PHP_EOL;
            /**
             * @var \LKDev\HetznerCloud\Models\Servers\Server
             */
            $action = $server->reset()->getResponsePart('action');

//            $text.= 'Reply from API: Action ID: '.$action->id.' '.$action->command.' '.$action->started.PHP_EOL;

            $text.= 'عملیات با موفقیت انجام شد لطفا دقایقی دیگر برای ورود به سرور تلاش کنید.'.PHP_EOL;
//            sleep(5);
//            $text.= 'Get the Server from the API:'.PHP_EOL;
//            $server = $hetznerClient->servers()->get($serverId);
//            $text.= 'Server status: '.$server->status.PHP_EOL;
//            $text.= "Let's start it again!";
//            $server->powerOn();
//            $text.= 'Wait some seconds that the server could startup.'.PHP_EOL;
//            sleep(5);
//            $text.= 'Get the Server from the API:'.PHP_EOL;
//            $server = $hetznerClient->servers()->get($serverId);
//            $text.= 'Server status: '.$server->status.PHP_EOL;

        }
        $theme=$text.'</pre>';
    }
    if($_GET['a']=='start'){
        $text='<pre>';
        if($serverId>0) {
            $server = $hetznerClient->servers()->get($serverId);
            $text.= 'وضعیت فعلی سرور: '.$server->status.PHP_EOL;
            $text.= "در حال روشن کردن سرور ";
            $server->powerOn();
            $text.= 'برای ورود دقایقی منتظر بمانید.'.PHP_EOL;

        }
        $theme=$text.'</pre>';
    }
    if($_GET['a']=='poweroff'){
        $text='<pre>';
        if($serverId>0) {
            $server = $hetznerClient->servers()->get($serverId);
            $text.= 'Server: '.$server->name.PHP_EOL;
            $text.= 'در حال خاموش کردن سرور:'.PHP_EOL;
            /**
             * @var \LKDev\HetznerCloud\Models\Servers\Server
             */
            $action = $server->powerOff()->getResponsePart('action');

//            $text.= 'Reply from API: Action ID: '.$action->id.' '.$action->command.' '.$action->started.PHP_EOL;

            $text.= 'دقایقی تا خاموش شدن سرور صبر نمایید.'.PHP_EOL;
            sleep(5);
            $text.= 'بررسی وضعیت سرور:'.PHP_EOL;
            $server = $hetznerClient->servers()->get($serverId);
            $text.= ' '.$server->status.PHP_EOL;

        }
        $theme=$text.'</pre>';
    }

//    if($_GET['b'] == 'hostname'){
//        $theme = virt_hostname($params);
//    }
//
//    if($_GET['b'] == 'changeRootPass'){
//        $theme = virt_changeRootPass($params);
//    }
//
//    if($_GET['b'] == 'changeVncPass'){
//        $theme = virt_changeVncPass($params);
//    }
//
//    if($_GET['b'] == 'osreinstall'){
//        $theme = virt_osreinstall($params);
//    }
//
//    if($_GET['b'] == 'controlpanel'){
//        $theme = virt_controlpanel($params);
//    }
//
//    if($_GET['b'] == 'cpu'){
//        $theme = virt_cpu($params);
//    }
//
//    if($_GET['b'] == 'ram'){
//        $theme = virt_ram($params);
//    }
//
//    if($_GET['b'] == 'disk'){
//        $theme = virt_disk($params);
//    }
//
//    if($_GET['b'] == 'bandwidth'){
//        $theme = virt_bandwidth($params);
//    }
//
//    if($_GET['b'] == 'performance'){
//        $theme = virt_performance($params);
//    }
//
//    if($_GET['b'] == 'processes'){
//        $theme = virt_processes($params);
//    }
//
//    if($_GET['b'] == 'services'){
//        $theme = virt_services($params);
//    }
//
//    if($_GET['b'] == ''){
//        $theme = virt_performance($params);
//    }



    // Any errors
    if(!empty($virt_errors)){
        $code .= virt_error($virt_errors);
    }

    // Show a Done message
    if(!empty($virt_action_display)){
        $code .= virt_done($virt_action_display);
    }

    // Show a Form
    if(!empty($theme)){
        $code .= $theme;
    }

    $code .= '<br /><br /></td></tr></table>';

    return $code;
}
function virt_error($error, $table_width = '500', $center = true, $ret = true){

    $str = '';

    //on error call the form
    if(!empty($error)){

        $str = '<table width="'.$table_width.'" cellpadding="2" cellspacing="1" style="background-color: rgb(230, 230, 230);" '.(($center) ? 'align="center"' : '' ).'>
			<tr>
			<td>
			The following errors occured :
			<ul type="square">';

        foreach($error as $ek => $ev){

            $str .= '<li>'.$ev.'</li>';

        }


        $str .= '</ul>
			</td>
			</tr>
			</table>'.(($center) ? '</center>' : '' ).'
			<br />';

        if(empty($ret)){
            echo $str;
        }else{
            return $str;
        }

    }

}

function virt_done($done){
    return '<div style="background-color: #FAFBD9; font-size:13px; padding:8px; text-align:center; margin-bottom: 20px; width: 500px"><img src="'.$GLOBALS['virt_img_url'].'notice.gif" /> &nbsp; '.$done.'</div>';
}

function virt_controlpanel($params) {

    $theme = '<h2>Control Panel Installation</h2>';

    $ins = @array_keys($_POST['ins']);

    if(!empty($ins)){

        $fields = array(
            'ins' => $_POST['ins']
        );

        $virt_resp = Virtualizor_Curl::action($params, 'act=controlpanel&',$fields);

        if(isset($virt_resp['done'])){

            $theme .= virt_done('Control Panel Installation has been Started');
        } elseif(isset($virt_resp['onboot'])) {

            $theme .= virt_done('Please stop and start the VPS after which the control panel installtion will start');
        } else {

            $virt_errors[] = 'There was an error while reinstalling the Control Panel';
            $theme .= virt_error($virt_errors);
        }

    }

    $theme .= '
	<script language="javascript" type="text/javascript">
		function confirmpanel(){
			if(confirm("Are you sure you want to install this panel ? Data on the server will be altered significantly.")){
				return true;
			}else{
				return false;
			}
		}
	</script>
	
	<STYLE TYPE="text/css">	
		.mycss { width: 50px; height: 50px; }
	</STYLE>
	
	<form method="post" action="">
		<table cellpadding="8" cellspacing="1">
			<tr>
				<td align="center">
					<input type="image" name="ins[cpanel]" onclick="return confirmpanel()" src="'.$GLOBALS['virt_img_url'].'cpanel.gif" class="mycss" /><br />cPanel		
				</td>
				<td align="center">
					<input type="image" name="ins[plesk]" onclick="return confirmpanel()" src="'.$GLOBALS['virt_img_url'].'plesk.gif" class="mycss"/><br />Plesk			
				</td>
				
				<td align="center">
					<input type="image" name="ins[webuzo]" onclick="return confirmpanel()" src="'.$GLOBALS['virt_img_url'].'webuzo.gif" class="mycss"/><br />Webuzo		
				</td>
			</tr>
			<tr>
				<td align="center">
					<input type="image" name="ins[kloxo]" onclick="return confirmpanel()" src="'.$GLOBALS['virt_img_url'].'kloxo.gif" class="mycss" /><br />Kloxo
				</td>
				<td align="center">
					<input type="image" name="ins[webmin]" onclick="return confirmpanel()" src="'.$GLOBALS['virt_img_url'].'webmin.gif" class="mycss" /><br />Webmin	
				</td>
				<td>&nbsp;</td>
			</tr>
	
		</table>
	</form><br />';

    return $theme;

}

function hetzner_ClientArea1(array $params)
{
    // Determine the requested action and set service call parameters based on
    // the action.
    $requestedAction = isset($_REQUEST['customAction']) ? $_REQUEST['customAction'] : '';

    if ($requestedAction == 'manage') {
        $serviceAction = 'get_usage';
        $templateFile = 'templates/manage.tpl';
    }elseif ($requestedAction == 'restart') {
		echo 'i restart';
		exit();
        $serviceAction = 'get_usage';
        $templateFile = 'templates/manage.tpl';
    } else {
        $serviceAction = 'get_stats';
        $templateFile = 'templates/overview.tpl';
    }

    try {
        // Call the service's function based on the request action, using the
        // values provided by WHMCS in `$params`.
        $response = array();

        $extraVariable1 = 'abc';
        $extraVariable2 = '123';

        return array(
            'tabOverviewReplacementTemplate' => $templateFile,
            'templateVariables' => array(
                // 'extraVariable1' => $extraVariable1,
                // 'extraVariable2' => $extraVariable2,
            ),
        );
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'hetzner',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        // In an error condition, display an error page.
        return array(
            'tabOverviewReplacementTemplate' => 'error.tpl',
            'templateVariables' => array(
                'usefulErrorHelper' => $e->getMessage(),
            ),
        );
    }
}
