<?php

namespace Laposta\SignupBasic\Service;

use Laposta\SignupBasic\Container\Container;
use Laposta\SignupBasic\Plugin;

class AdminMenu
{
	/**
	 * @var Container
	 */
	protected $c;

	/**
	 * @var string
	 */
	protected $pluginUrl;

	/**
	 * @var string
	 */
	protected $pageTitle;

	/**
	 * @var string
	 */
	protected $menuTitle;

	public function __construct(Container $container, $pluginUrl, $pageTitle, $menuTitle)
	{
		$this->c = $container;
		$this->pluginUrl = $pluginUrl;
		$this->pageTitle = $pageTitle;
		$this->menuTitle = $menuTitle;
		$this->init();
	}

	protected function init(): void
	{
		add_action('admin_menu', [$this, 'renderMenu']);
		add_action('admin_head', [$this, 'addCustomSvgIcon']);
	}

	public function renderMenu(): void
	{
		$actualCapability = apply_filters(Plugin::FILTER_SETTINGS_PAGE_CAPABILITY, Plugin::DEFAULT_CAPABILITY);
		$actualCapability = is_string($actualCapability) ? $actualCapability : Plugin::DEFAULT_CAPABILITY;
		$position = apply_filters(Plugin::FILTER_MENU_POSITION, 79.901);
		$position = is_numeric($position) ? $position : 79.901;
		add_menu_page(
			$this->pageTitle,
			$this->menuTitle,
			$actualCapability,
			Plugin::SLUG_SETTINGS,
			[$this->c->getSettingsController(), 'renderSettings'],
			'',
			$position
		);
	}

	public function addCustomSvgIcon(): void
	{
		?>
        <style>
            #toplevel_page_<?php echo Plugin::SLUG_SETTINGS ?> div.wp-menu-image:before {
                content: '';
                display: inline-block;
                width: 20px;
                height: 20px;
                background-color: currentColor;
                mask: url('<?php echo $this->pluginUrl.'assets/images/icon.svg' ?>') no-repeat center;
                background-size: contain;
            }
        </style>
		<?php
	}
}