<?php
/**
 * Compact, nested navigation for the single AMANAH SEO WordPress menu.
 *
 * Existing submenu URLs, callbacks, and capability checks are preserved.
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
			#toplevel_page_amanah-seo .wp-submenu li.amanah-seo-nav-category,
			#toplevel_page_amanah-seo .wp-submenu li.amanah-seo-nav-group {
				margin: 5px 0 1px;
				border-top: 1px solid rgba(240,246,252,.12);
				padding-top: 4px;
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
			#toplevel_page_amanah-seo .wp-submenu li.amanah-seo-nav-group > a {
				font-weight: 600;
				color: #dcdcde;
				padding: 6px 12px 6px 26px;
				position: relative;
			}
			#toplevel_page_amanah-seo .wp-submenu li.amanah-seo-nav-category > a::after,
			#toplevel_page_amanah-seo .wp-submenu li.amanah-seo-nav-group > a::after {
				content: "▸";
				position: absolute;
				right: 12px;
				font-size: 12px;
			}
			#toplevel_page_amanah-seo .wp-submenu li.amanah-seo-nav-category.is-expanded > a::after,
			#toplevel_page_amanah-seo .wp-submenu li.amanah-seo-nav-group.is-expanded > a::after {
				content: "▾";
			}
			#toplevel_page_amanah-seo .wp-submenu li.amanah-seo-nav-page > a {
				padding-left: 40px;
			}
			#toplevel_page_amanah-seo .wp-submenu li.amanah-seo-nav-page[hidden],
			#toplevel_page_amanah-seo .wp-submenu li.amanah-seo-nav-group[hidden],
			#toplevel_page_amanah-seo .wp-submenu li.amanah-seo-nav-page[hidden] {
				display: none !important;
			}
			#toplevel_page_amanah-seo .wp-submenu li.amanah-seo-nav-category > a:focus,
			#toplevel_page_amanah-seo .wp-submenu li.amanah-seo-nav-group > a:focus {
				box-shadow: inset 4px 0 #72aee6;
			}
		</style>
		<script id="amanah-seo-collapsible-navigation-js">
		(function () {
			function initializeAmanahNavigation() {
				var root = document.querySelector('#toplevel_page_amanah-seo');
				var submenu = root ? root.querySelector(':scope > ul.wp-submenu') : null;
				if (!submenu || submenu.dataset.amanahGrouped === '1') return;
				submenu.dataset.amanahGrouped = '1';

				var pageItems = Array.prototype.slice.call(submenu.querySelectorAll(':scope > li'));
				var categories = [
					{ id: 'core', label: 'CORE' },
					{ id: 'ai', label: 'AI' },
					{ id: 'engine', label: 'ENGINE' }
				];
				var groupDefinitions = {
					core: [
						{ id: 'dashboard', label: 'Dashboard' },
						{ id: 'website', label: 'Website & Workspace' },
						{ id: 'seo', label: 'SEO & Opportunities' },
						{ id: 'tasks', label: 'Tasks & Workflow' },
						{ id: 'settings', label: 'Settings & Access' }
					],
					ai: [
						{ id: 'entity', label: 'Entity & Authority' },
						{ id: 'analysis', label: 'Content Analysis' },
						{ id: 'creation', label: 'AI Content Creation' }
					],
					engine: [
						{ id: 'keywords', label: 'Keyword Intelligence' },
						{ id: 'strategy', label: 'Strategy & Planning' },
						{ id: 'workflow', label: 'Content Workflow' },
						{ id: 'monitoring', label: 'SEO Monitoring' }
					]
				};

				function getSlug(item) {
					var link = item.querySelector('a');
					if (!link) return '';
					try { return new URL(link.href, window.location.href).searchParams.get('page') || ''; }
					catch (e) { return ''; }
				}
				function getLabel(item) {
					var link = item.querySelector('a');
					return link ? (link.textContent || '').replace(/\\s+/g, ' ').trim().toLowerCase() : '';
				}
				function categoryFor(item) {
					var slug = getSlug(item);
					var label = getLabel(item);
					if (slug === 'amanah-seo-ai-settings') return 'core';
					if (slug.indexOf('amanah-seo-engine-') === 0 || slug === 'amanah-seo-engine' || /keyword research|search intent|keyword cluster|url mapping|content scanning|content strategy|content plan|content brief|content blueprint|maturity|publishing|engine workflow/.test(label)) return 'engine';
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
					) return 'ai';
					return 'core';
				}
				function groupFor(item, category) {
					var slug = getSlug(item);
					var label = getLabel(item);
					if (category === 'core') {
						if (slug === 'amanah-seo') return 'dashboard';
						if (/workspace|website/.test(slug + ' ' + label)) return 'website';
						if (/tasks|history|approval|execution|pekerjaan|riwayat/.test(slug + ' ' + label)) return 'tasks';
						if (/access|settings|pengaturan|license|credential|api key/.test(slug + ' ' + label)) return 'settings';
						return 'seo';
					}
					if (category === 'ai') {
						if (/entity|authority|author/.test(slug + ' ' + label)) return 'entity';
						if (/analy|audit|optim/.test(slug + ' ' + label)) return 'analysis';
						return 'creation';
					}
					if (/keyword|intent|cluster|mapping|scanning|intelligence/.test(slug + ' ' + label)) return 'keywords';
					if (/requirement|strateg|plan|brief|blueprint/.test(slug + ' ' + label)) return 'strategy';
					if (/monitor|performance|site-health|orphan|internal-link|cannibal|seo-issue/.test(slug + ' ' + label)) return 'monitoring';
					return 'workflow';
				}

				pageItems.forEach(function (item) {
					var link = item.querySelector('a');
					if (!link) return;
					if (getSlug(item) === 'amanah-seo') link.textContent = 'Dashboard';
					if (getSlug(item) === 'amanah-seo-ai-settings') link.textContent = 'AI Provider & API Credentials';
				});

				var categoryRows = {};
				var groupRows = {};
				var groups = { core: {}, ai: {}, engine: {} };
				categories.forEach(function (category) {
					categoryRows[category.id] = document.createElement('li');
					categoryRows[category.id].className = 'amanah-seo-nav-category';
					categoryRows[category.id].dataset.amanahCategory = category.id;
					var categoryLink = document.createElement('a');
					categoryLink.href = '#amanah-seo-' + category.id;
					categoryLink.textContent = category.label;
					categoryLink.setAttribute('role', 'button');
					categoryLink.setAttribute('aria-expanded', 'false');
					categoryLink.addEventListener('click', function (event) {
						event.preventDefault();
						setActiveCategory(category.id, true);
					});
					categoryRows[category.id].appendChild(categoryLink);

					groupDefinitions[category.id].forEach(function (definition) {
						var key = category.id + ':' + definition.id;
						var row = document.createElement('li');
						row.className = 'amanah-seo-nav-group';
						row.dataset.amanahCategory = category.id;
						row.dataset.amanahGroup = definition.id;
						var link = document.createElement('a');
						link.href = '#amanah-seo-' + key.replace(':', '-');
						link.textContent = definition.label;
						link.setAttribute('role', 'button');
						link.setAttribute('aria-expanded', 'false');
						link.addEventListener('click', function (event) {
							event.preventDefault();
							setActiveCategory(category.id, true, definition.id, true);
						});
						row.appendChild(link);
						groupRows[key] = row;
						groups[category.id][definition.id] = [];
					});
				});

				pageItems.forEach(function (item) {
					var category = categoryFor(item);
					var group = groupFor(item, category);
					item.classList.add('amanah-seo-nav-page');
					item.dataset.amanahCategory = category;
					item.dataset.amanahGroup = group;
					groups[category][group].push(item);
				});

				var fragment = document.createDocumentFragment();
				categories.forEach(function (category) {
					fragment.appendChild(categoryRows[category.id]);
					groupDefinitions[category.id].forEach(function (definition) {
						fragment.appendChild(groupRows[category.id + ':' + definition.id]);
						groups[category.id][definition.id].forEach(function (item) { fragment.appendChild(item); });
					});
				});
				submenu.appendChild(fragment);

				var page = '';
				try { page = new URL(window.location.href).searchParams.get('page') || ''; } catch (e) {}
				var activeCategory = 'core';
				var activeGroup = null;
				var activeItem = pageItems.find(function (item) { return getSlug(item) === page; });
				if (activeItem) {
					activeCategory = activeItem.dataset.amanahCategory || 'core';
					activeGroup = activeItem.dataset.amanahGroup || null;
				} else if (page.indexOf('amanah-seo-engine-') === 0 || page === 'amanah-seo-engine') {
					activeCategory = 'engine';
					activeGroup = 'workflow';
				} else if (page.indexOf('amanah-') === 0 || page.indexOf('amanah-seo-ai-') === 0) {
					activeCategory = page === 'amanah-seo-ai-settings' ? 'core' : 'ai';
					activeGroup = activeCategory === 'core' ? 'settings' : 'creation';
				} else {
					try {
						var remembered = window.localStorage.getItem('amanah-seo-active-menu-category');
						if (!page && ['core', 'ai', 'engine'].indexOf(remembered) !== -1) activeCategory = remembered;
					} catch (e) {}
				}

				function setActiveCategory(id, remember, groupId, toggleGroup) {
					if (typeof groupId !== 'undefined') {
						activeGroup = toggleGroup && activeGroup === groupId && activeCategory === id ? null : groupId;
					} else if (activeCategory !== id) {
						activeGroup = null;
					}
					activeCategory = id;
					categories.forEach(function (category) {
						var expanded = category.id === id;
						var categoryRow = categoryRows[category.id];
						categoryRow.classList.toggle('is-expanded', expanded);
						categoryRow.querySelector('a').setAttribute('aria-expanded', expanded ? 'true' : 'false');
					});
					Object.keys(groupRows).forEach(function (key) {
						var row = groupRows[key];
						var category = row.dataset.amanahCategory;
						var group = row.dataset.amanahGroup;
						var expanded = category === activeCategory && group === activeGroup;
						row.hidden = category !== activeCategory;
						row.classList.toggle('is-expanded', expanded);
						row.querySelector('a').setAttribute('aria-expanded', expanded ? 'true' : 'false');
					});
					pageItems.forEach(function (item) {
						item.hidden = item.dataset.amanahCategory !== activeCategory || item.dataset.amanahGroup !== activeGroup;
					});
					if (remember) {
						try { window.localStorage.setItem('amanah-seo-active-menu-category', id); } catch (e) {}
					}
				}
				setActiveCategory(activeCategory, false, activeGroup, false);
			}
			if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initializeAmanahNavigation);
			else initializeAmanahNavigation();
		})();
		</script>
		<?php
	}
}
