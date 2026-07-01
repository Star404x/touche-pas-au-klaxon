/**
 * modal.js - Gestion des modales Bootstrap
 * TOUCHE PAS AU KLAXON
 * 
 * @category Modal Management
 * @package KlaxonApp\JavaScript
 */

'use strict';

/**
 * Classe de gestion des modales
 */
class ModalManager {
    constructor() {
        this.modals = new Map();
        this.initModals();
    }

    /**
     * Initialiser tous les écouteurs de modales
     */
    initModals() {
        document.querySelectorAll('[data-bs-toggle="modal"]').forEach(trigger => {
            trigger.addEventListener('click', (e) => {
                const targetId = trigger.getAttribute('data-bs-target');
                if (targetId) {
                    this.handleModalOpen(e, trigger, targetId);
                }
            });
        });
    }

    /**
     * Gérer l'ouverture d'une modale
     * @param {Event} event
     * @param {HTMLElement} trigger
     * @param {string} targetId
     */
    handleModalOpen(event, trigger, targetId) {
        const modal = document.querySelector(targetId);
        if (!modal) return;

        // Transférer les data attributes
        const dataAttributes = trigger.dataset;
        Object.entries(dataAttributes).forEach(([key, value]) => {
            modal.dataset[key] = value;
        });

        // Pré-remplir les champs si c'est une édition
        if (dataAttributes.userId || dataAttributes.agencyId || dataAttributes.tripId) {
            this.prefillForm(modal, dataAttributes);
        }
    }

    /**
     * Pré-remplir un formulaire dans la modale
     * @param {HTMLElement} modal
     * @param {object} data
     */
    prefillForm(modal, data) {
        // Cette fonction peut être étendue selon les besoins
        // Pour l'instant, elle est prête pour des extensions futures
    }

    /**
     * Ouvrir une modale
     * @param {string} modalId
     */
    open(modalId) {
        const modal = document.querySelector(modalId);
        if (modal) {
            const bsModal = new bootstrap.Modal(modal);
            bsModal.show();
        }
    }

    /**
     * Fermer une modale
     * @param {string} modalId
     */
    close(modalId) {
        const modal = document.querySelector(modalId);
        if (modal) {
            const bsModal = bootstrap.Modal.getInstance(modal);
            if (bsModal) bsModal.hide();
        }
    }
}

/**
 * Gestion des confirmations de suppression
 */
class DeleteConfirmation {
    constructor(modalId, formId) {
        this.modal = document.querySelector(modalId);
        this.form = document.querySelector(formId);
        this.init();
    }

    /**
     * Initialiser la suppression
     */
    init() {
        if (!this.modal) return;

        this.modal.addEventListener('show.bs.modal', (event) => {
            const trigger = event.relatedTarget;
            if (!trigger) return;

            const itemId = trigger.dataset.userId || 
                          trigger.dataset.agencyId || 
                          trigger.dataset.tripId;

            if (itemId && this.form) {
                const currentAction = this.form.action;
                this.form.action = currentAction.replace(/\/\d+$/, `/${itemId}`);
            }
        });
    }
}

/**
 * Gestion des modales d'édition
 */
class EditModal {
    constructor(modalId, formId) {
        this.modal = document.querySelector(modalId);
        this.form = document.querySelector(formId);
        this.init();
    }

    /**
     * Initialiser la modale d'édition
     */
    init() {
        if (!this.modal || !this.form) return;

        this.modal.addEventListener('show.bs.modal', (event) => {
            const trigger = event.relatedTarget;
            if (!trigger) return;

            const itemId = trigger.dataset.userId || 
                          trigger.dataset.agencyId || 
                          trigger.dataset.tripId;

            if (itemId) {
                this.updateFormAction(itemId);
                this.loadData(itemId);
            }
        });
    }

    /**
     * Mettre à jour l'action du formulaire
     * @param {number} itemId
     */
    updateFormAction(itemId) {
        const baseAction = this.form.getAttribute('data-base-action') || 
                          this.form.action.replace(/\/\d+$/, '');
        this.form.action = `${baseAction}/${itemId}`;
    }

    /**
     * Charger les données de l'item
     * @param {number} itemId
     */
    loadData(itemId) {
        // À implémenter avec AJAX selon les besoins
        // Pour l'instant, fonction vide (prête pour extension)
    }
}

/**
 * Gestion des flash messages
 */
class FlashMessage {
    constructor(message = '', type = 'info', dismissible = true) {
        this.message = message;
        this.type = type;
        this.dismissible = dismissible;
    }

    /**
     * Afficher le flash message
     * @param {string} containerId
     */
    show(containerId = 'flash-container') {
        const container = document.getElementById(containerId) || 
                         document.querySelector('main');
        
        if (!container) return;

        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${this.type} alert-dismissible fade show`;
        alertDiv.role = 'alert';

        let icon = '';
        switch (this.type) {
            case 'success':
                icon = '<i class="fas fa-check-circle"></i>';
                break;
            case 'danger':
                icon = '<i class="fas fa-exclamation-circle"></i>';
                break;
            case 'warning':
                icon = '<i class="fas fa-exclamation-triangle"></i>';
                break;
            default:
                icon = '<i class="fas fa-info-circle"></i>';
        }

        alertDiv.innerHTML = `
            <div class="alert-icon">${icon}</div>
            <div class="alert-content">
                <div>${this.message}</div>
            </div>
            ${this.dismissible ? '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' : ''}
        `;

        // Insérer au début
        container.insertBefore(alertDiv, container.firstChild);

        // Auto-fermer après 5 secondes
        if (this.dismissible) {
            setTimeout(() => {
                alertDiv.remove();
            }, 5000);
        }
    }

    /**
     * Afficher un message succès
     */
    static success(message, containerId = 'flash-container') {
        new FlashMessage(message, 'success').show(containerId);
    }

    /**
     * Afficher un message erreur
     */
    static error(message, containerId = 'flash-container') {
        new FlashMessage(message, 'danger').show(containerId);
    }

    /**
     * Afficher un message attention
     */
    static warning(message, containerId = 'flash-container') {
        new FlashMessage(message, 'warning').show(containerId);
    }

    /**
     * Afficher un message info
     */
    static info(message, containerId = 'flash-container') {
        new FlashMessage(message, 'info').show(containerId);
    }
}

/**
 * Initialisation globale
 */
document.addEventListener('DOMContentLoaded', function() {
    // Initialiser le gestionnaire de modales
    window.modalManager = new ModalManager();

    // Initialiser les confirmations de suppression
    if (document.getElementById('deleteUserModal')) {
        new DeleteConfirmation('#deleteUserModal', '#deleteUserForm');
    }
    if (document.getElementById('deleteAgencyModal')) {
        new DeleteConfirmation('#deleteAgencyModal', '#deleteAgencyForm');
    }
    if (document.getElementById('deleteTripModal')) {
        new DeleteConfirmation('#deleteTripModal', '#deleteTripForm');
    }

    // Initialiser les modales d'édition
    if (document.getElementById('editUserModal')) {
        new EditModal('#editUserModal', '#editUserForm');
    }
    if (document.getElementById('editAgencyModal')) {
        new EditModal('#editAgencyModal', '#editAgencyForm');
    }

    // Rendre FlashMessage disponible globalement
    window.FlashMessage = FlashMessage;
});
