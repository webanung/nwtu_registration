<?php

    use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
    use WebanUg\NwtuRegistration\Controller\RegistrationController;

    defined( 'TYPO3' ) or die( 'Access denied.' );
    /***************
     * Add default RTE configuration
     */
    //$GLOBALS['TYPO3_CONF_VARS']['RTE']['Presets']['schminckede_farbkarte'] = 'EXT:schminckede_farbkarte/Configuration/RTE/Default.yaml';

    /***************
     * PageTS
     */

    //\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('<INCLUDE_TYPOSCRIPT: source="FILE:EXT:schminckede_farbkarte/Configuration/TsConfig/Page/All.tsconfig">');

    //use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
    //use WebanUg\SchminckedeFarbkarte\Controller\FarbkarteController;

    ExtensionUtility::configurePlugin( 'NwtuRegistration', 'Registration', [RegistrationController::class => 'register'], [RegistrationController::class => 'register'] );

