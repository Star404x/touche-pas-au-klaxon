/**
 * app.js - Logique applicative générale
 * TOUCHE PAS AU KLAXON
 * 
 * @category Application Logic
 * @package KlaxonApp\JavaScript
 */

'use strict';

/**
 * Classe applicative principale
 */
class KlaxonApp {
    constructor() {
        this.init();
    }

    /**
     * Initialiser l'application
     */
    init() {
        this.setupGlobalErrorHandler();
        this.setupServiceWorker();
        this.setupAccessibility();
        this.setupResponsive();
    }

    /**
     * Gestionnaire d'erreur global
     */
    setupGlobalErrorHandler() {
        window.addEventListener('error', (event) => {
            console.error('Erreur:', event.error);
            // Optionnel: Envoyer à un service de monitoring
        });

        window.addEventListener('unhandledrejection', (event) => {
            console.error('Promise rejetée non gérée:', event.reason);
        });
    }

    /**
     * Initialiser Service Worker (optionnel)
     */
    setupServiceWorker() {
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js').catch(err => {
                console.log('Service Worker non enregistré:', err);
            });
        }
    }

    /**
     * Améliorer l'accessibilité
     */
    setupAccessibility() {
        // Ajouter support pour keyboard navigation
        document.addEventListener('keydown', (e) => {
            // Échap pour fermer les modales
            if (e.key === 'Escape') {
                this.closeAllModals();
            }

            // Alt+M pour skip to main content
            if (e.altKey && e.key === 'm') {
                const main = document.querySelector('main') || 
                            document.querySelector('[role="main"]');
                if (main) {
                    main.focus();
                    main.scrollIntoView();
                }
            }
        });

        // Améliorer focus visible
        document.addEventListener('keydown', () => {
            document.body.classList.remove('mouse-active');
        });

        document.addEventListener('mousedown', () => {
            document.body.classList.add('mouse-active');
        });
    }

    /**
     * Fermer toutes les modales
     */
    closeAllModals() {
        document.querySelectorAll('.modal.show').forEach(modal => {
            const bsModal = bootstrap.Modal.getInstance(modal);
            if (bsModal) bsModal.hide();
        });
    }

    /**
     * Gérer le responsive
     */
    setupResponsive() {
        // Fermer navbar au clic
        document.querySelectorAll('.navbar-nav a').forEach(link => {
            link.addEventListener('click', () => {
                const navbar = document.querySelector('.navbar-collapse');
                if (navbar && navbar.classList.contains('show')) {
                    const toggler = document.querySelector('.navbar-toggler');
                    if (toggler) toggler.click();
                }
            });
        });
    }
}

/**
 * Classe pour gérer les requêtes API
 */
class ApiClient {
    constructor(baseUrl = '') {
        this.baseUrl = baseUrl;
    }

    /**
     * GET request
     * @param {string} endpoint
     * @returns {Promise}
     */
    async get(endpoint) {
        return this.request(endpoint, { method: 'GET' });
    }

    /**
     * POST request
     * @param {string} endpoint
     * @param {object} data
     * @returns {Promise}
     */
    async post(endpoint, data) {
        return this.request(endpoint, {
            method: 'POST',
            body: JSON.stringify(data),
            headers: {
                'Content-Type': 'application/json'
            }
        });
    }

    /**
     * PUT request
     * @param {string} endpoint
     * @param {object} data
     * @returns {Promise}
     */
    async put(endpoint, data) {
        return this.request(endpoint, {
            method: 'PUT',
            body: JSON.stringify(data),
            headers: {
                'Content-Type': 'application/json'
            }
        });
    }

    /**
     * DELETE request
     * @param {string} endpoint
     * @returns {Promise}
     */
    async delete(endpoint) {
        return this.request(endpoint, { method: 'DELETE' });
    }

    /**
     * Effectuer une requête
     * @param {string} endpoint
     * @param {object} options
     * @returns {Promise}
     */
    async request(endpoint, options = {}) {
        const url = `${this.baseUrl}${endpoint}`;

        try {
            const response = await fetch(url, {
                ...options,
                headers: {
                    ...options.headers,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            // Essayer de parser JSON, sinon retourner texte
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return await response.json();
            }

            return await response.text();
        } catch (error) {
            console.error('API Error:', error);
            throw error;
        }
    }
}

/**
 * Classe pour gérer les dates
 */
class DateUtils {
    /**
     * Formater une date
     * @param {Date|string} date
     * @param {string} format
     * @returns {string}
     */
    static format(date, format = 'DD/MM/YYYY HH:mm') {
        if (typeof date === 'string') {
            date = new Date(date);
        }

        const pad = (n) => String(n).padStart(2, '0');

        const replacements = {
            'YYYY': date.getFullYear(),
            'MM': pad(date.getMonth() + 1),
            'DD': pad(date.getDate()),
            'HH': pad(date.getHours()),
            'mm': pad(date.getMinutes()),
            'ss': pad(date.getSeconds())
        };

        let result = format;
        Object.entries(replacements).forEach(([key, value]) => {
            result = result.replace(key, value);
        });

        return result;
    }

    /**
     * Obtenir le relativetime (ex: "il y a 2 heures")
     * @param {Date|string} date
     * @returns {string}
     */
    static getRelativeTime(date) {
        if (typeof date === 'string') {
            date = new Date(date);
        }

        const now = new Date();
        const diff = now.getTime() - date.getTime();
        const seconds = Math.floor(diff / 1000);
        const minutes = Math.floor(seconds / 60);
        const hours = Math.floor(minutes / 60);
        const days = Math.floor(hours / 24);

        if (seconds < 60) {
            return 'à l\'instant';
        } else if (minutes < 60) {
            return `il y a ${minutes} minute${minutes > 1 ? 's' : ''}`;
        } else if (hours < 24) {
            return `il y a ${hours} heure${hours > 1 ? 's' : ''}`;
        } else if (days < 7) {
            return `il y a ${days} jour${days > 1 ? 's' : ''}`;
        } else {
            return this.format(date, 'DD/MM/YYYY');
        }
    }
}

/**
 * Classe pour gérer le localStorage
 */
class StorageManager {
    /**
     * Obtenir une valeur
     * @param {string} key
     * @param {*} defaultValue
     * @returns {*}
     */
    static get(key, defaultValue = null) {
        const item = localStorage.getItem(key);
        if (item === null) return defaultValue;

        try {
            return JSON.parse(item);
        } catch (e) {
            return item;
        }
    }

    /**
     * Stocker une valeur
     * @param {string} key
     * @param {*} value
     */
    static set(key, value) {
        try {
            localStorage.setItem(key, JSON.stringify(value));
        } catch (e) {
            console.error('localStorage full:', e);
        }
    }

    /**
     * Supprimer une valeur
     * @param {string} key
     */
    static remove(key) {
        localStorage.removeItem(key);
    }

    /**
     * Vider tout le storage
     */
    static clear() {
        localStorage.clear();
    }
}

/**
 * Initialisation globale
 */
document.addEventListener('DOMContentLoaded', function() {
    // Initialiser l'application
    window.app = new KlaxonApp();

    // Rendre les utilitaires globalement disponibles
    window.api = new ApiClient();
    window.DateUtils = DateUtils;
    window.Storage = StorageManager;

    // Gestion de la navigation
    const links = document.querySelectorAll('a[href^="/"]');
    links.forEach(link => {
        link.addEventListener('click', function(e) {
            // Si c'est un lien vers une modale ou autre, ne pas empêcher
            if (this.getAttribute('data-bs-toggle') === 'modal' || 
                this.getAttribute('target') === '_blank') {
                return;
            }
        });
    });

    // Console bienvenue (debug)
    console.log('%c🚗 TOUCHE PAS AU KLAXON', 'color: #ff6b00; font-weight: bold; font-size: 16px;');
    console.log('Version: 1.0.0 | Build: Production');
});

// Exporter pour les modèles ES6
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { KlaxonApp, ApiClient, DateUtils, StorageManager };
}
