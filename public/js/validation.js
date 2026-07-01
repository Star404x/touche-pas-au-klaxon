/**
 * validation.js - Validation HTML5 et JavaScript personnalisée
 * TOUCHE PAS AU KLAXON
 * 
 * @category Form Validation
 * @package KlaxonApp\JavaScript
 */

'use strict';

/**
 * Classe de validation des formulaires
 */
class FormValidator {
    constructor(formElement) {
        this.form = formElement;
        this.errors = {};
        this.init();
    }

    /**
     * Initialiser la validation
     */
    init() {
        if (!this.form) return;

        this.form.addEventListener('submit', (e) => {
            if (!this.validate()) {
                e.preventDefault();
                e.stopPropagation();
                this.displayErrors();
            }
        });

        // Validation en temps réel
        const inputs = this.form.querySelectorAll('[data-validate]');
        inputs.forEach(input => {
            input.addEventListener('blur', () => this.validateField(input));
            input.addEventListener('change', () => this.validateField(input));
        });
    }

    /**
     * Valider le formulaire complet
     * @returns {boolean}
     */
    validate() {
        this.errors = {};
        const fields = this.form.querySelectorAll('[required], [data-validate]');

        fields.forEach(field => {
            this.validateField(field);
        });

        return Object.keys(this.errors).length === 0;
    }

    /**
     * Valider un champ spécifique
     * @param {HTMLElement} field
     */
    validateField(field) {
        const name = field.name || field.id;
        const value = field.value.trim();
        const type = field.getAttribute('type') || field.tagName.toLowerCase();
        const validationType = field.getAttribute('data-validate');

        // Validation HTML5 native
        if (!field.checkValidity()) {
            this.addError(name, this.getValidationMessage(field));
            field.classList.add('is-invalid');
            return;
        }

        field.classList.remove('is-invalid');
        delete this.errors[name];

        // Validations personnalisées
        switch (validationType) {
            case 'email':
                if (!this.isValidEmail(value)) {
                    this.addError(name, 'Email invalide');
                    field.classList.add('is-invalid');
                }
                break;

            case 'phone':
                if (!this.isValidPhone(value)) {
                    this.addError(name, 'Numéro de téléphone invalide');
                    field.classList.add('is-invalid');
                }
                break;

            case 'date':
                if (!this.isValidDate(value)) {
                    this.addError(name, 'Date invalide');
                    field.classList.add('is-invalid');
                }
                break;

            case 'datetime':
                if (!this.isValidDateTime(value)) {
                    this.addError(name, 'Date/heure invalide');
                    field.classList.add('is-invalid');
                }
                break;

            case 'password':
                if (value.length < 8) {
                    this.addError(name, 'Le mot de passe doit contenir au moins 8 caractères');
                    field.classList.add('is-invalid');
                }
                break;

            case 'agencies-different':
                if (this.checkAgenciesDifferent() === false) {
                    this.addError(name, 'Les agences de départ et d\'arrivée doivent être différentes');
                    field.classList.add('is-invalid');
                }
                break;
        }
    }

    /**
     * Vérifier que les agences de départ et arrivée sont différentes
     * @returns {boolean}
     */
    checkAgenciesDifferent() {
        const departField = this.form.querySelector('[name="agence_depart_id"]');
        const arrivalField = this.form.querySelector('[name="agence_arrivee_id"]');

        if (!departField || !arrivalField) return true;

        const depart = departField.value;
        const arrival = arrivalField.value;

        return depart !== arrival || !depart || !arrival;
    }

    /**
     * Valider email
     * @param {string} email
     * @returns {boolean}
     */
    isValidEmail(email) {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(email);
    }

    /**
     * Valider téléphone français
     * @param {string} phone
     * @returns {boolean}
     */
    isValidPhone(phone) {
        const regex = /^(?:0|\+33)[1-9](?:[0-9]{8})$/;
        return regex.test(phone.replace(/\s/g, ''));
    }

    /**
     * Valider date (YYYY-MM-DD)
     * @param {string} date
     * @returns {boolean}
     */
    isValidDate(date) {
        if (!date) return false;
        const d = new Date(date);
        return d instanceof Date && !isNaN(d);
    }

    /**
     * Valider date/heure
     * @param {string} datetime
     * @returns {boolean}
     */
    isValidDateTime(datetime) {
        if (!datetime) return false;
        const d = new Date(datetime);
        return d instanceof Date && !isNaN(d);
    }

    /**
     * Ajouter une erreur
     * @param {string} fieldName
     * @param {string} message
     */
    addError(fieldName, message) {
        if (!this.errors[fieldName]) {
            this.errors[fieldName] = message;
        }
    }

    /**
     * Obtenir le message de validation
     * @param {HTMLElement} field
     * @returns {string}
     */
    getValidationMessage(field) {
        if (field.validity.valueMissing) {
            return 'Ce champ est requis';
        }
        if (field.validity.typeMismatch) {
            return `Format invalide pour ${field.type}`;
        }
        if (field.validity.tooShort) {
            return `Minimum ${field.minLength} caractères requis`;
        }
        if (field.validity.tooLong) {
            return `Maximum ${field.maxLength} caractères`;
        }
        if (field.validity.rangeUnderflow) {
            return `Valeur minimale: ${field.min}`;
        }
        if (field.validity.rangeOverflow) {
            return `Valeur maximale: ${field.max}`;
        }
        return 'Valeur invalide';
    }

    /**
     * Afficher les erreurs
     */
    displayErrors() {
        // Supprimer les anciens messages d'erreur
        this.form.querySelectorAll('.invalid-feedback').forEach(el => {
            el.remove();
        });

        // Afficher les nouvelles erreurs
        Object.entries(this.errors).forEach(([fieldName, message]) => {
            const field = this.form.querySelector(`[name="${fieldName}"], [id="${fieldName}"]`);
            if (field) {
                field.classList.add('is-invalid');

                const errorDiv = document.createElement('div');
                errorDiv.className = 'invalid-feedback d-block';
                errorDiv.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${message}`;

                field.parentNode.appendChild(errorDiv);
            }
        });
    }

    /**
     * Réinitialiser les erreurs
     */
    clearErrors() {
        this.errors = {};
        this.form.querySelectorAll('.is-invalid').forEach(el => {
            el.classList.remove('is-invalid');
        });
        this.form.querySelectorAll('.invalid-feedback').forEach(el => {
            el.remove();
        });
    }
}

/**
 * Initialiser tous les formulaires à la page
 */
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form[novalidate]');
    forms.forEach(form => {
        new FormValidator(form);
    });
});

/**
 * Validation personnalisée pour les trajets
 */
function validateTripForm(formElement) {
    const form = new FormValidator(formElement);
    
    // Validation supplémentaire pour dates
    const departField = form.form.querySelector('[name="date_heure_depart"]');
    const arrivalField = form.form.querySelector('[name="date_heure_arrivee"]');

    if (departField && arrivalField) {
        const validateDateRange = () => {
            const depart = new Date(departField.value);
            const arrival = new Date(arrivalField.value);
            const now = new Date();

            // Départ dans le futur
            if (depart <= now) {
                form.addError('date_heure_depart', 'Le départ doit être dans le futur');
                departField.classList.add('is-invalid');
                return false;
            }

            // Arrivée après départ
            if (arrival <= depart) {
                form.addError('date_heure_arrivee', 'L\'arrivée doit être après le départ');
                arrivalField.classList.add('is-invalid');
                return false;
            }

            form.clearErrors();
            return true;
        };

        [departField, arrivalField].forEach(field => {
            field.addEventListener('change', validateDateRange);
        });
    }

    return form;
}

/**
 * Valider les places
 */
function validatePlaces() {
    const placesField = document.querySelector('[name="places_totales"]');
    const placesDispField = document.querySelector('[name="places_disponibles"]');

    if (placesField && placesDispField) {
        placesField.addEventListener('change', function() {
            const total = parseInt(this.value);
            const disp = parseInt(placesDispField.value);

            if (disp > total) {
                placesDispField.value = total;
            }
        });
    }
}

// Initialiser validations spéciales
document.addEventListener('DOMContentLoaded', function() {
    validatePlaces();
});
