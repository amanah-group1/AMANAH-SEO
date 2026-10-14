<?php
/**
 * Collapsible three-category navigation for the single AMANAH SEO menu.
 *
 * This is presentation-only: original WordPress submenu URLs, callbacks,
 * capability checks, and feature registrations remain untouched.
 *
 * @package AMANAH_SEO
 */
defined( 'ABSPATH' ) || exit;

final class AMANAH_SEO_Admin_Navigation {
	public static function register(): void {
		add_action( 'admin_footer', array( __CLASS__, 'render_collapsible_navigation' ), 99 );
	}

	public static function render_collapsible_navigation(): void {
		if ( ! is_admin() || ! current_user_can( 'read' ) ) {
			return;
		}
		?>
		<style id="amanah-seo-collapsible-navigation-css">
			#toplevel_page_amanah-seo .wp-submenu li.amanah-seo-nav-category {
				margin: 7px 0 2px;
				border-top: 1px solid rgba(240,246,252,.12);
				padding-top: 5px;
			}
			#toplevel_page_amanah-seo .wp-submenu li.amanah-seo-nav-category:first-child {
				border-top: 0;
				margin-top: 0;
			}
			#toplevel_page_amanah-seo .wp-submenu li.amanah-seo-nav-category > a {
				font-weight: 700;
				color: #f0f0f1;
				padding: 8px 12px 8px 18px;
				position: relative;
			}
			#toplevel_page_amanah-seo .wp-submenu li.amanah-seo-nav-category > a::after {
				content: "▸";
				position: absolute;
				right: 12px;
				font-size: 13px;
			}
			#toplevel_page_amanah-seo .wp-submenu li.amanah-seo-nav-category.is-expanded > a::after {
				content: "▾";
			}
			#toplevel_page_amanah-seo .wp-submenu li.amanah-seo-nav-page > a {
				padding-left: 30px;
			}
			#toplevel_page_amanah-seo .wp-submenu li.amanah-seo-nav-page[hidden] {
				display: none !important;
			}
			#toplevel_page_amanah-seo .wp-submenu li.amanah-seo-nav-category > a:focus {
				box-shadow: inset 4px 0 #72aee6;
			}
		</style>
		<script id="amanah-seo-collapsible-navigation-js">
		(function () {
			function initializeAmanahNavigation() {
				var root = document.querySelector('#toplevel_page_amanah-seo');
				var submenu = root ? root.querySelector(':scope > ul.wp-submenu') : null;
				if (!submenu || submenu.dataset.amanahGrouped === '1') {
					return;
				}
				submenu.dataset.amanahGrouped = '1';

				var pageItems = Array.prototype.slice.call(submenu.querySelectorAll(':scope > li'));
				var categories = [
					{ id: 'core', label: 'CORE' },
					{ id: 'ai', label: 'AI' },
					{ id: 'engine', label: 'ENGINE' }
				];
				var categoryFor = function (item) {
					var link = item.querySelector('a');
					if (!link) return 'core';
					var url;
					try { url = new URL(link.href, window.location.href); }
					catch (e) { return 'core'; }
					var slug = url.searchParams.get('page') || '';
					var label = (link.textContent || '').replace(/\s+/g, ' ').trim().toLowerCase();
					if (slug === 'amanah-seo-ai-settings') {
						link.textContent = 'AI Provider & API Credentials';
						return 'core';
					}

					if (slug.indexOf('amanah-seo-engine-') === 0 || slug === 'amanah-seo-engine') {
						return 'engine';
					}
					if (
						slug === 'amanah-seo-ai' ||
						slug === 'amanah-content-generation' ||
						slug === 'amanah-entity-profile' ||
						slug === 'amanah-authority' ||
						slug.indexOf('amanah-seo-ai-') === 0 ||
						slug.indexOf('amanah-ai-') === 0 ||
						slug.indexOf('amanah-content-analyzer') === 0 ||
						slug.indexOf('amanah-article-generation') === 0 ||
						/entity profile|authority & author|content analyzer|article generation|ai settings/.test(label)
					) {
						return 'ai';
					}
					return 'core';
				};

				// WordPress automatically duplicates the top-level title as its first submenu.
				// Keep its existing dashboard URL and callback, but give it a useful label.
				pageItems.forEach(function (item) {
					var link = item.querySelector('a');
					if (!link) return;
					try {
						var url = new URL(link.href, window.location.href);
						if ((url.searchParams.get('page') || '') === 'amanah-seo') {
							link.textContent = 'Dashboard';
						}
					} catch (e) {}
				});

				var categoryRows = {};
				categories.forEach(function (category) {
					var row = document.createElement('li');
					row.className = 'amanah-seo-nav-category';
					row.dataset.amanahCategory = category.id;
					var link = document.createElement('a');
					link.href = '#amanah-seo-' + category.id;
					link.textContent = category.label;
					link.setAttribute('role', 'button');
					link.setAttribute('aria-expanded', 'false');
					link.addEventListener('click', function (event) {
						event.preventDefault();
						setActiveCategory(category.id, true);
					});
					row.appendChild(link);
					categoryRows[category.id] = row;
				});

				var groups = { core: [], ai: [], engine: [] };
				pageItems.forEach(function (item) {
					var group = categoryFor(item);
					item.classList.add('amanah-seo-nav-page');
					item.dataset.amanahCategory = group;
					groups[group].push(item);
				});

				var fragment = document.createDocumentFragment();
				categories.forEach(function (category) {
					var row = categoryRows[category.id];
					fragment.appendChild(row);
					groups[category.id].forEach(function (item) { fragment.appendChild(item); });
				});
				submenu.appendChild(fragment);

				var page = '';
				try { page = new URL(window.location.href).searchParams.get('page') || ''; }
				catch (e) {}
				var active = 'core';
				if (page.indexOf('amanah-seo-engine-') === 0 || page === 'amanah-seo-engine') {
					active = 'engine';
				} else if (
					page !== 'amanah-seo-ai-settings' && (
						page === 'amanah-seo-ai' ||
						page === 'amanah-entity-profile' ||
						page === 'amanah-authority' ||
						page === 'amanah-content-generation' ||
						page.indexOf('amanah-seo-ai-') === 0 ||
						page.indexOf('amanah-ai-') === 0 ||
						page.indexOf('amanah-content-analyzer') === 0 ||
						page.indexOf('amanah-article-generation') === 0
					)
				) {
					active = 'ai';
				}
				var activeItem = pageItems.find(function (item) {
					var link = item.querySelector('a');
					if (!link) return false;
					try { return (new URL(link.href, window.location.href)).searchParams.get('page') === page; }
					catch (e) { return false; }
				});
				if (activeItem && activeItem.dataset.amanahCategory) {
					active = activeItem.dataset.amanahCategory;
				} else {
					try {
						var remembered = window.localStorage.getItem('amanah-seo-active-menu-category');
						if (!page && ['core', 'ai', 'engine'].indexOf(remembered) !== -1) active = remembered;
					} catch (e) {}
				}

				function setActiveCategory(id, remember) {
					categories.forEach(function (category) {
						var expanded = category.id === id;
						var row = categoryRows[category.id];
						row.classList.toggle('is-expanded', expanded);
						row.querySelector('a').setAttribute('aria-expanded', expanded ? 'true' : 'false');
						groups[category.id].forEach(function (item) {
							item.hidden = !expanded;
						});
					});
					if (remember) {
						try { window.localStorage.setItem('amanah-seo-active-menu-category', id); } catch (e) {}
					}
				}
				setActiveCategory(active, false);
			}
			if (document.readyState === 'loading') {
				document.addEventListener('DOMContentLoaded', initializeAmanahNavigation);
			} else {
				initializeAmanahNavigation();
			}
		})();
		</script>
		<?php
	}
}
