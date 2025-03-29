import Alpine from 'alpinejs'

// Optional plugins you might want
import focus from '@alpinejs/focus'  // For focus directives
import collapse from '@alpinejs/collapse'  // For x-collapse

// Register plugins (if using)
Alpine.plugin(focus)
Alpine.plugin(collapse)

// Make Alpine available globally
window.Alpine = Alpine

// Start Alpine
Alpine.start()