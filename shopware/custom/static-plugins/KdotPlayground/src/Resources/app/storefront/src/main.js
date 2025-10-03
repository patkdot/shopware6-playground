// Import all necessary Storefront plugins
import KdotPlugin from './kdot-plugin/kdot-plugin.plugin';

// Register your plugin via the existing PluginManager
const PluginManager = window.PluginManager;

PluginManager.register('KdotPlugin', KdotPlugin, '.cms-element-product-listing');
